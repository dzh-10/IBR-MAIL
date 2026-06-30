<?php

namespace App\Jobs;

use App\Models\ExternalMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SendOutgoingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $messageModel;

    /**
     * Create a new job instance.
     */
    public function __construct(ExternalMessage $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $message = $this->messageModel;
        $account = $message->mailAccount;

        Log::info("Attempting to send SMTP email for message: {$message->id}");

        try {
            // Configure SMTP Mailer dynamically at runtime
            Config::set('mail.mailers.smtp_dynamic', [
                'transport' => 'smtp',
                'host' => $account->smtp_host,
                'port' => $account->smtp_port,
                'encryption' => $account->smtp_encryption === 'none' ? null : $account->smtp_encryption,
                'username' => $account->smtp_username,
                'password' => $account->smtp_password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ]);

            // Construct the sender configurations
            Config::set('mail.from.address', $account->from_email);
            Config::set('mail.from.name', $account->from_name);

            $toAddresses = $message->to;
            $ccAddresses = $message->cc ?? [];
            $bccAddresses = $message->bcc ?? [];
            $subject = $message->subject;
            
            $bodyHtml = $message->body_html;
            $bodyText = $message->body_text;

            // Send via dynamic mailer
            Mail::mailer('smtp_dynamic')->send([], [], function ($email) use ($toAddresses, $ccAddresses, $bccAddresses, $subject, $bodyHtml, $bodyText, $message) {
                $email->to($toAddresses);

                if (!empty($ccAddresses)) {
                    $email->cc($ccAddresses);
                }
                if (!empty($bccAddresses)) {
                    $email->bcc($bccAddresses);
                }

                $email->subject($subject);

                // Add HTML body and text alternative fallback
                if (!empty($bodyHtml)) {
                    $email->html($bodyHtml);
                } else {
                    $email->text($bodyText);
                }

                // Add attachments
                foreach ($message->attachments as $attachment) {
                    if (Storage::exists($attachment->path)) {
                        $email->attach(Storage::path($attachment->path), [
                            'as' => $attachment->filename,
                            'mime' => $attachment->mime_type,
                        ]);
                    }
                }
            });

            // Update database flags
            $message->update([
                'folder' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("SMTP email sent successfully for message: {$message->id}");

        } catch (\Exception $e) {
            Log::error("SMTP email sending failed for message: {$message->id}. Error: " . $e->getMessage());
            
            // Re-throw so the job is retried or moved to failed jobs
            throw $e;
        }
    }
}
