<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\Attachment;
use App\Events\NewEmailReceived;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProcessIncomingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mailAccountId;
    protected $messageId;
    protected $subject;
    protected $fromEmail;
    protected $fromName;
    protected $to;
    protected $cc;
    protected $bodyText;
    protected $bodyHtml;
    protected $rawHeaders;
    protected $receivedAt;
    protected $attachmentsData;

    /**
     * Create a new job instance.
     */
    public function __construct(
        int $mailAccountId,
        ?string $messageId,
        ?string $subject,
        string $fromEmail,
        ?string $fromName,
        array $to,
        array $cc,
        ?string $bodyText,
        ?string $bodyHtml,
        ?string $rawHeaders,
        string $receivedAt,
        array $attachmentsData
    ) {
        $this->mailAccountId = $mailAccountId;
        $this->messageId = $messageId;
        $this->subject = $subject;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
        $this->to = $to;
        $this->cc = $cc;
        $this->bodyText = $bodyText;
        $this->bodyHtml = $bodyHtml;
        $this->rawHeaders = $rawHeaders;
        $this->receivedAt = $receivedAt;
        $this->attachmentsData = $attachmentsData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Deduplicate: check if message already exists
        if ($this->messageId) {
            $exists = Message::where('mail_account_id', $this->mailAccountId)
                ->where('message_id', $this->messageId)
                ->exists();
            if ($exists) {
                Log::info("Message with ID {$this->messageId} already exists. Skipping.");
                // Clean up temp attachments
                foreach ($this->attachmentsData as $att) {
                    Storage::delete($att['temp_path']);
                }
                return;
            }
        }

        // Parse date
        try {
            $parsedDate = new \DateTime($this->receivedAt);
        } catch (\Exception $e) {
            $parsedDate = now();
        }

        // Save email message
        $message = Message::create([
            'mail_account_id' => $this->mailAccountId,
            'message_id' => $this->messageId,
            'thread_id' => null, // Grouping can be added later via Subject threading
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
            'to' => $this->to,
            'cc' => $this->cc,
            'bcc' => [],
            'subject' => $this->subject ?: '(No Subject)',
            'body_text' => $this->bodyText,
            'body_html' => $this->bodyHtml,
            'direction' => 'inbound',
            'folder' => 'inbox',
            'is_read' => false,
            'raw_headers' => $this->rawHeaders,
            'received_at' => $parsedDate,
            'sent_at' => $parsedDate,
        ]);

        // Process attachments
        foreach ($this->attachmentsData as $att) {
            if (Storage::exists($att['temp_path'])) {
                $permanentPath = 'attachments/' . $message->id . '/' . $att['filename'];
                Storage::move($att['temp_path'], $permanentPath);

                Attachment::create([
                    'message_id' => $message->id,
                    'filename' => $att['filename'],
                    'mime_type' => $att['mime_type'],
                    'size' => $att['size'],
                    'path' => $permanentPath,
                ]);
            }
        }

        Log::info("Successfully stored email {$message->id} from {$this->fromEmail}");

        // Broadcast notification of new mail
        broadcast(new NewEmailReceived($message))->toOthers();
    }
}
