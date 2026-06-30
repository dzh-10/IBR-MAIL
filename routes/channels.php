<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('conversation.{conversationId}', function ($user, $conversationId) {
    if (!$user->is_employee && !$user->is_admin) {
        return false;
    }

    $conversation = Conversation::find($conversationId);
    if (!$conversation) {
        return false;
    }

    // For internal chat, the user must be the initiator (user_id) or the recipient (recipient_id)
    if ($conversation->type === 'internal_chat') {
        return $user->id === $conversation->user_id || $user->id === $conversation->recipient_id;
    }

    // For external_email channels, any registered employee/admin can listen
    return true;
});

// User specific notifications (e.g. toast notification of new emails)
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
