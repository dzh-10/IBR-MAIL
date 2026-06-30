<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Console\Command;

class SyncUsersToContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contacts:sync-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all internal users to the contacts directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sync of internal users to contacts directory...');
        
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            Contact::updateOrCreate(
                ['email' => $user->email],
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'is_internal' => true,
                    'is_active' => true,
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random',
                ]
            );
            $count++;
        }

        $this->info("Successfully synced {$count} internal users to contacts.");
        return Command::SUCCESS;
    }
}
