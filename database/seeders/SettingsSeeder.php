<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'app_name', 'value' => 'Messagerie', 'group' => 'general', 'type' => 'string', 'label' => 'Application Name', 'description' => 'The name of the application'],
            ['key' => 'app_url', 'value' => 'http://127.0.0.1:8000', 'group' => 'general', 'type' => 'string', 'label' => 'Application URL', 'description' => 'The base URL of the application'],
            ['key' => 'default_language', 'value' => 'en', 'group' => 'general', 'type' => 'string', 'label' => 'Default Language', 'description' => 'en or fr'],
            ['key' => 'timezone', 'value' => 'UTC', 'group' => 'general', 'type' => 'string', 'label' => 'Timezone', 'description' => 'System timezone'],
            ['key' => 'date_format', 'value' => 'Y-m-d H:i:s', 'group' => 'general', 'type' => 'string', 'label' => 'Date Format', 'description' => 'Format for displaying dates'],
            ['key' => 'pagination_limit', 'value' => '20', 'group' => 'general', 'type' => 'integer', 'label' => 'Pagination Limit', 'description' => 'Items per page'],
            ['key' => 'maintenance_mode', 'value' => 'false', 'group' => 'general', 'type' => 'boolean', 'label' => 'Maintenance Mode', 'description' => 'Enable maintenance mode'],

            // Branding
            ['key' => 'app_logo', 'value' => '', 'group' => 'branding', 'type' => 'string', 'label' => 'Application Logo URL', 'description' => 'Public URL of the logo'],
            ['key' => 'app_favicon', 'value' => '', 'group' => 'branding', 'type' => 'string', 'label' => 'Application Favicon URL', 'description' => 'Public URL of the favicon'],
            ['key' => 'primary_color', 'value' => '#3b82f6', 'group' => 'branding', 'type' => 'string', 'label' => 'Primary Color', 'description' => 'Hex color code'],
            ['key' => 'footer_text', 'value' => 'CorpMail', 'group' => 'branding', 'type' => 'string', 'label' => 'Footer Text', 'description' => 'Text displayed in the footer'],
            ['key' => 'copyright_text', 'value' => '© 2026 CorpMail. All rights reserved.', 'group' => 'branding', 'type' => 'string', 'label' => 'Copyright Text', 'description' => 'Copyright notice'],

            // Mail
            ['key' => 'smtp_host', 'value' => 'smtp.mailtrap.io', 'group' => 'mail', 'type' => 'string', 'label' => 'SMTP Host', 'description' => 'SMTP server address'],
            ['key' => 'smtp_port', 'value' => '2525', 'group' => 'mail', 'type' => 'integer', 'label' => 'SMTP Port', 'description' => 'SMTP server port'],
            ['key' => 'smtp_encryption', 'value' => 'tls', 'group' => 'mail', 'type' => 'string', 'label' => 'SMTP Encryption', 'description' => 'tls or ssl'],
            ['key' => 'smtp_username', 'value' => '', 'group' => 'mail', 'type' => 'string', 'label' => 'SMTP Username', 'description' => 'Username for SMTP'],
            ['key' => 'smtp_password', 'value' => '', 'group' => 'mail', 'type' => 'string', 'label' => 'SMTP Password', 'description' => 'Password for SMTP', 'is_encrypted' => true],
            ['key' => 'mail_from_name', 'value' => 'Messagerie', 'group' => 'mail', 'type' => 'string', 'label' => 'From Name', 'description' => 'Default from name'],
            ['key' => 'mail_from_email', 'value' => 'noreply@company.local', 'group' => 'mail', 'type' => 'string', 'label' => 'From Email', 'description' => 'Default from email'],

            // IMAP
            ['key' => 'imap_host', 'value' => 'imap.mailtrap.io', 'group' => 'imap', 'type' => 'string', 'label' => 'IMAP Host', 'description' => 'IMAP server address'],
            ['key' => 'imap_port', 'value' => '993', 'group' => 'imap', 'type' => 'integer', 'label' => 'IMAP Port', 'description' => 'IMAP server port'],
            ['key' => 'imap_encryption', 'value' => 'ssl', 'group' => 'imap', 'type' => 'string', 'label' => 'IMAP Encryption', 'description' => 'tls or ssl'],
            ['key' => 'imap_username', 'value' => '', 'group' => 'imap', 'type' => 'string', 'label' => 'IMAP Username', 'description' => 'Username for IMAP'],
            ['key' => 'imap_password', 'value' => '', 'group' => 'imap', 'type' => 'string', 'label' => 'IMAP Password', 'description' => 'Password for IMAP', 'is_encrypted' => true],
            ['key' => 'imap_folder', 'value' => 'INBOX', 'group' => 'imap', 'type' => 'string', 'label' => 'IMAP Folder', 'description' => 'Default mailbox folder'],

            // POP3
            ['key' => 'pop_host', 'value' => 'pop3.mailtrap.io', 'group' => 'pop3', 'type' => 'string', 'label' => 'POP3 Host', 'description' => 'POP3 server address'],
            ['key' => 'pop_port', 'value' => '995', 'group' => 'pop3', 'type' => 'integer', 'label' => 'POP3 Port', 'description' => 'POP3 server port'],
            ['key' => 'pop_encryption', 'value' => 'ssl', 'group' => 'pop3', 'type' => 'string', 'label' => 'POP3 Encryption', 'description' => 'tls or ssl'],
            ['key' => 'pop_username', 'value' => '', 'group' => 'pop3', 'type' => 'string', 'label' => 'POP3 Username', 'description' => 'Username for POP3'],
            ['key' => 'pop_password', 'value' => '', 'group' => 'pop3', 'type' => 'string', 'label' => 'POP3 Password', 'description' => 'Password for POP3', 'is_encrypted' => true],

            // Sync
            ['key' => 'sync_frequency', 'value' => '5', 'group' => 'sync', 'type' => 'integer', 'label' => 'Sync Frequency', 'description' => 'Sync interval in minutes'],
            ['key' => 'sync_max_emails', 'value' => '50', 'group' => 'sync', 'type' => 'integer', 'label' => 'Max Emails', 'description' => 'Max emails to fetch per sync'],
            ['key' => 'sync_auto_read', 'value' => 'false', 'group' => 'sync', 'type' => 'boolean', 'label' => 'Auto Mark Read', 'description' => 'Mark emails as read after sync'],
            ['key' => 'sync_imap_enabled', 'value' => 'true', 'group' => 'sync', 'type' => 'boolean', 'label' => 'Enable IMAP Sync', 'description' => 'Enable global IMAP sync'],
            ['key' => 'sync_pop_enabled', 'value' => 'false', 'group' => 'sync', 'type' => 'boolean', 'label' => 'Enable POP3 Sync', 'description' => 'Enable global POP3 sync'],

            // Notifications
            ['key' => 'notify_email', 'value' => 'true', 'group' => 'notifications', 'type' => 'boolean', 'label' => 'Email Notifications', 'description' => 'Enable email notifications'],
            ['key' => 'notify_realtime', 'value' => 'true', 'group' => 'notifications', 'type' => 'boolean', 'label' => 'Realtime Notifications', 'description' => 'Enable WebSocket notifications'],
            ['key' => 'notify_internal', 'value' => 'true', 'group' => 'notifications', 'type' => 'boolean', 'label' => 'Internal Message Notifications', 'description' => 'Notify on internal messages'],
            ['key' => 'notify_external', 'value' => 'true', 'group' => 'notifications', 'type' => 'boolean', 'label' => 'External Email Notifications', 'description' => 'Notify on external emails'],
            ['key' => 'notify_sound', 'value' => 'true', 'group' => 'notifications', 'type' => 'boolean', 'label' => 'Notification Sound', 'description' => 'Play sound on notification'],

            // User
            ['key' => 'user_registration', 'value' => 'false', 'group' => 'user', 'type' => 'boolean', 'label' => 'Self Registration', 'description' => 'Allow users to register themselves'],
            ['key' => 'user_default_role', 'value' => 'employee', 'group' => 'user', 'type' => 'string', 'label' => 'Default Role', 'description' => 'Role for new users'],
            ['key' => 'user_allow_profile_edit', 'value' => 'true', 'group' => 'user', 'type' => 'boolean', 'label' => 'Allow Profile Edit', 'description' => 'Allow language and avatar change'],
            ['key' => 'user_max_attachment_mb', 'value' => '10', 'group' => 'user', 'type' => 'integer', 'label' => 'Max Attachment Size', 'description' => 'Max size in MB'],
            ['key' => 'user_allowed_attachments', 'value' => 'jpg,png,pdf,doc,docx,xls,xlsx', 'group' => 'user', 'type' => 'string', 'label' => 'Allowed Extensions', 'description' => 'Comma separated list'],

            // Storage
            ['key' => 'storage_disk', 'value' => 'local', 'group' => 'storage', 'type' => 'string', 'label' => 'Default Disk', 'description' => 'local or s3'],
            ['key' => 'storage_max_per_user', 'value' => '1024', 'group' => 'storage', 'type' => 'integer', 'label' => 'Max Storage Per User', 'description' => 'Max storage in MB per user'],
            ['key' => 'storage_archive_days', 'value' => '365', 'group' => 'storage', 'type' => 'integer', 'label' => 'Archive After Days', 'description' => 'Days before archiving emails'],
            ['key' => 'storage_trash_days', 'value' => '30', 'group' => 'storage', 'type' => 'integer', 'label' => 'Empty Trash After Days', 'description' => 'Days before deleting trash'],

            // Security
            ['key' => 'security_force_https', 'value' => 'false', 'group' => 'security', 'type' => 'boolean', 'label' => 'Force HTTPS', 'description' => 'Redirect all traffic to HTTPS'],
            ['key' => 'security_session_lifetime', 'value' => '120', 'group' => 'security', 'type' => 'integer', 'label' => 'Session Lifetime', 'description' => 'Lifetime in minutes'],
            ['key' => 'security_max_login_attempts', 'value' => '5', 'group' => 'security', 'type' => 'integer', 'label' => 'Max Login Attempts', 'description' => 'Attempts before lockout'],
            ['key' => 'security_lockout_duration', 'value' => '15', 'group' => 'security', 'type' => 'integer', 'label' => 'Lockout Duration', 'description' => 'Minutes to lock out'],
            ['key' => 'security_2fa_enabled', 'value' => 'false', 'group' => 'security', 'type' => 'boolean', 'label' => 'Enable 2FA', 'description' => 'Two-factor authentication'],
            ['key' => 'security_min_password_len', 'value' => '8', 'group' => 'security', 'type' => 'integer', 'label' => 'Min Password Length', 'description' => 'Minimum password length'],

            // Localization
            ['key' => 'loc_default_lang', 'value' => 'en', 'group' => 'localization', 'type' => 'string', 'label' => 'System Language', 'description' => 'Default system language'],
            ['key' => 'loc_available_langs', 'value' => 'en,fr', 'group' => 'localization', 'type' => 'string', 'label' => 'Available Languages', 'description' => 'Comma separated list'],
            ['key' => 'loc_datetime_locale', 'value' => 'en_US', 'group' => 'localization', 'type' => 'string', 'label' => 'Datetime Locale', 'description' => 'Locale for formatting dates'],

            // System (Read Only - values will be populated dynamically in UI)
            ['key' => 'sys_php_version', 'value' => PHP_VERSION, 'group' => 'system', 'type' => 'string', 'label' => 'PHP Version', 'description' => ''],
            ['key' => 'sys_laravel_version', 'value' => app()->version(), 'group' => 'system', 'type' => 'string', 'label' => 'Laravel Version', 'description' => ''],
        ];

        foreach ($settings as $setting) {
            $isEncrypted = $setting['is_encrypted'] ?? false;
            $value = $setting['value'];
            
            if ($isEncrypted && $value) {
                $value = \Illuminate\Support\Facades\Crypt::encryptString($value);
            }

            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $value,
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'is_encrypted' => $isEncrypted,
                ]
            );
        }
    }
}
