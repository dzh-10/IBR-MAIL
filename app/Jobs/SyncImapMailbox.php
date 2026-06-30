<?php

namespace App\Jobs;

use App\Models\MailAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webklex\PHPIMAP\ClientManager;

class SyncImapMailbox implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $account;

    /**
     * Create a new job instance.
     */
    public function __construct(MailAccount $account)
    {
        $this->account = $account;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting IMAP Sync for Account: {$this->account->email}");

        try {
            $clientManager = new ClientManager();
            
            $client = $clientManager->make([
                'host'          => $this->account->imap_host,
                'port'          => $this->account->imap_port,
                'encryption'    => $this->account->imap_port == 993 ? 'ssl' : 'tls',
                'validate_cert' => false,
                'username'      => $this->account->imap_username,
                'password'      => $this->account->imap_password,
                'protocol'      => 'imap'
            ]);

            $client->connect();

            $folders = $client->getFolders();
            foreach ($folders as $folder) {
                if (strtolower($folder->name) !== 'inbox') {
                    continue;
                }

                $query = $folder->query();

                if ($this->account->last_synced_at) {
                    $query->since($this->account->last_synced_at->subDay());
                } else {
                    $query->unseen();
                }

                $messages = $query->get();

                Log::info("Found {$messages->count()} emails to sync for {$this->account->email}");

                foreach ($messages as $msg) {
                    // Extract attachments securely (saving to temp storage)
                    $attachmentsData = [];
                    foreach ($msg->getAttachments() as $attachment) {
                        $tempPath = 'temp_attachments/' . Str::random(40);
                        Storage::put($tempPath, $attachment->getContent());
                        
                        $attachmentsData[] = [
                            'filename' => $attachment->getName(),
                            'mime_type' => $attachment->getMimeType() ?: 'application/octet-stream',
                            'size' => $attachment->getSize() ?: 0,
                            'temp_path' => $tempPath
                        ];
                    }

                    // Format date
                    $dateStr = $msg->getDate()->first() ? $msg->getDate()->first()->toString() : now()->toDateTimeString();

                    ProcessIncomingEmail::dispatch(
                        $this->account->id,
                        $msg->getMessageId(),
                        $msg->getSubject(),
                        $msg->getFrom()[0]->mail ?? 'unknown@example.com',
                        $msg->getFrom()[0]->personal ?? '',
                        array_map(fn($t) => $t->mail ?? '', $msg->getTo()->toArray()),
                        array_map(fn($c) => $c->mail ?? '', $msg->getCc()->toArray()),
                        $msg->getTextBody(),
                        $msg->getHTMLBody(),
                        $msg->getHeader()->raw,
                        $dateStr,
                        $attachmentsData
                    );
                }
            }

            $this->account->update([
                'last_synced_at' => now(),
            ]);

            Log::info("IMAP Sync completed for: {$this->account->email}");

        } catch (\Exception $e) {
            Log::error("IMAP Sync failed for {$this->account->email}: " . $e->getMessage());
        }
    }
}
