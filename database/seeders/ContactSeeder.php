<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@partner.com',
                'department' => 'External Vendors',
                'job_title' => 'Account Manager',
                'phone' => '+1-555-0198',
                'is_internal' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@agency.io',
                'department' => 'Marketing',
                'job_title' => 'Creative Director',
                'phone' => '+1-555-0222',
                'is_internal' => false,
                'is_active' => true,
            ]
        ];

        foreach ($contacts as $contact) {
            $contact['avatar'] = 'https://ui-avatars.com/api/?name=' . urlencode($contact['name']) . '&background=random';
            Contact::create($contact);
        }
    }
}
