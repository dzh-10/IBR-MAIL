<?php

namespace App\Services;

use App\Models\MailAccount;
use App\Models\ExternalMessage;

class SmtpSendService
{
    public function send(MailAccount $account, array $data)
    {
        // Stub for SMTP send logic
        // In a real implementation:
        // 1. Configure dynamic SwiftMailer/Symfony Mailer transport with $account credentials
        // 2. Build the email message (Mailable)
        // 3. Send email
        // 4. Save copy to external_messages with direction='outbound', folder='Sent'
        
        $message = ExternalMessage::create([
            'mail_account_id' => $account->id,
            'to' => $data['to'],
            'from' => [$account->from_email],
            'subject' => $data['subject'],
            'body_text' => $data['body'],
            'direction' => 'outbound',
            'folder' => 'Sent',
            'sent_at' => now(),
            'is_read' => true,
        ]);
        
        return $message;
    }
}
