<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZBateson\MailMimeParser\MailMimeParser;

class ImportMaildirBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $mailAccountId;
    protected $targetFolder;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, int $mailAccountId, string $targetFolder = 'inbox')
    {
        $this->filePath = $filePath;
        $this->mailAccountId = $mailAccountId;
        $this->targetFolder = $targetFolder;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!file_exists($this->filePath)) {
            Log::error("Maildir import file not found: {$this->filePath}");
            return;
        }

        Log::info("Parsing Maildir email: {$this->filePath}");

        try {
            $parser = new MailMimeParser();
            $handle = fopen($this->filePath, 'r');
            if (!$handle) {
                throw new \Exception("Could not open file handle for {$this->filePath}");
            }

            $mail = $parser->parse($handle, false);

            $messageId = $mail->getHeaderValue('message-id');
            
            // Deduplicate
            if ($messageId) {
                $exists = Message::where('mail_account_id', $this->mailAccountId)
                    ->where('message_id', $messageId)
                    ->exists();
                if ($exists) {
                    Log::info("Maildir import skipped: Message with ID {$messageId} already exists.");
                    fclose($handle);
                    return;
                }
            }

            // Headers & basic info
            $subject = $mail->getHeaderValue('subject') ?: '(No Subject)';
            
            $fromHeader = $mail->getHeader('from');
            $fromEmail = 'unknown@example.com';
            $fromName = '';
            if ($fromHeader && method_exists($fromHeader, 'getAddresses')) {
                $addresses = $fromHeader->getAddresses();
                if (!empty($addresses)) {
                    $fromEmail = $addresses[0]->getEmail();
                    $fromName = $addresses[0]->getName();
                }
            } elseif ($fromHeader) {
                $fromEmail = $mail->getHeaderValue('from');
            }

            // To, CC, BCC
            $toHeader = $mail->getHeader('to');
            $toAddresses = [];
            if ($toHeader && method_exists($toHeader, 'getAddresses')) {
                foreach ($toHeader->getAddresses() as $addr) {
                    $toAddresses[] = $addr->getEmail();
                }
            } else {
                $toAddresses[] = $mail->getHeaderValue('to') ?: 'unknown@example.com';
            }

            $ccHeader = $mail->getHeader('cc');
            $ccAddresses = [];
            if ($ccHeader && method_exists($ccHeader, 'getAddresses')) {
                foreach ($ccHeader->getAddresses() as $addr) {
                    $ccAddresses[] = $addr->getEmail();
                }
            }

            $bccAddresses = [];
            $bccHeader = $mail->getHeader('bcc');
            if ($bccHeader && method_exists($bccHeader, 'getAddresses')) {
                foreach ($bccHeader->getAddresses() as $addr) {
                    $bccAddresses[] = $addr->getEmail();
                }
            }

            // Date
            $dateStr = $mail->getHeaderValue('date');
            try {
                $parsedDate = $dateStr ? new \DateTime($dateStr) : now();
            } catch (\Exception $e) {
                $parsedDate = now();
            }

            // Bodies
            $bodyText = $mail->getTextContent();
            $bodyHtml = $mail->getHtmlContent();

            // Raw headers
            $rawHeaders = '';
            foreach ($mail->getHeaders() as $header) {
                $rawHeaders .= $header->getName() . ': ' . $header->getRawValue() . "\r\n";
            }

            // Save message
            $messageModel = Message::create([
                'mail_account_id' => $this->mailAccountId,
                'message_id' => $messageId ?: '<' . Str::random(32) . '@imported.local>',
                'thread_id' => null,
                'from_email' => $fromEmail,
                'from_name' => $fromName,
                'to' => $toAddresses,
                'cc' => $ccAddresses,
                'bcc' => $bccAddresses,
                'subject' => $subject,
                'body_text' => $bodyText,
                'body_html' => $bodyHtml,
                'direction' => 'inbound',
                'folder' => $this->targetFolder,
                'is_read' => true,
                'raw_headers' => $rawHeaders,
                'received_at' => $parsedDate,
                'sent_at' => $parsedDate,
            ]);

            // Save attachments
            $attachments = $mail->getAttachmentParts();
            foreach ($attachments as $attachmentPart) {
                $filename = $attachmentPart->getFilename() ?: 'attachment_' . Str::random(10);
                $mimeType = $attachmentPart->getContentType() ?: 'application/octet-stream';
                
                // Write stream to local storage
                $permanentPath = 'attachments/' . $messageModel->id . '/' . $filename;
                $contentStream = $attachmentPart->getContentStream();
                
                if ($contentStream) {
                    Storage::put($permanentPath, $contentStream);
                    
                    Attachment::create([
                        'message_id' => $messageModel->id,
                        'filename' => $filename,
                        'mime_type' => $mimeType,
                        'size' => Storage::size($permanentPath),
                        'path' => $permanentPath,
                    ]);
                }
            }

            fclose($handle);
            Log::info("Successfully imported Maildir email ID: {$messageModel->id}");

        } catch (\Exception $e) {
            Log::error("Failed to import Maildir file {$this->filePath}: " . $e->getMessage());
        }
    }
}
