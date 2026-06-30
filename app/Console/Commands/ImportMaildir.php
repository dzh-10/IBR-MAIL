<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ImportMaildirBackup;
use App\Models\MailAccount;

class ImportMaildir extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:import-maildir 
                            {path : Path to the Maildir root directory} 
                            {account_id : The ID of the local mail account to assign emails to} 
                            {--sync : Run imports synchronously instead of queuing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import emails from a cPanel Maildir backup directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');
        $accountId = $this->argument('account_id');
        $runSync = $this->option('sync');

        if (!MailAccount::where('id', $accountId)->exists()) {
            $this->error("Mail Account with ID {$accountId} does not exist in the database.");
            return 1;
        }

        if (!is_dir($path)) {
            $this->error("The path '{$path}' is not a valid directory.");
            return 1;
        }

        $this->info("Scanning directory: {$path}");

        // Standard Maildir folders to check
        $subfolders = ['cur', 'new'];
        $filesFound = [];

        foreach ($subfolders as $sub) {
            $subPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $sub;
            if (is_dir($subPath)) {
                $files = scandir($subPath);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $fullFilePath = $subPath . DIRECTORY_SEPARATOR . $file;
                        if (is_file($fullFilePath)) {
                            $filesFound[] = [
                                'file' => $fullFilePath,
                                'folder' => 'inbox', // Map folder back to inbox
                            ];
                        }
                    }
                }
            }
        }

        $total = count($filesFound);
        $this->info("Found {$total} emails to import.");

        if ($total === 0) {
            $this->warn("No emails found to import in {$path}/cur or {$path}/new.");
            return 0;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($filesFound as $item) {
            if ($runSync) {
                ImportMaildirBackup::dispatchSync($item['file'], $accountId, $item['folder']);
            } else {
                ImportMaildirBackup::dispatch($item['file'], $accountId, $item['folder']);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\nImport process completed! Processing " . ($runSync ? "synchronously" : "in queue background") . ".");

        return 0;
    }
}
