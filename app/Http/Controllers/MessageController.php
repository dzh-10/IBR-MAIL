<?php

namespace App\Http\Controllers;

use App\Models\ExternalMessage;
use App\Models\Attachment;
use App\Models\MailAccount;
use App\Jobs\SendOutgoingEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    /**
     * List external emails matching folder and search filters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $accountIds = $user->mailAccounts()->pluck('id');

        $query = ExternalMessage::whereIn('mail_account_id', $accountIds)
            ->with(['attachments']);

        // Filter by Folder
        if ($request->has('folder')) {
            $folder = $request->folder;
            if ($folder === 'starred') {
                $query->where('starred', true);
            } elseif ($folder === 'snoozed') {
                $query->where('folder', 'snoozed');
            } elseif ($folder === 'spam') {
                $query->where('folder', 'spam');
            } elseif ($folder === 'trash') {
                $query->where('folder', 'trash');
            } else {
                $query->where('folder', $folder);
            }
        } else {
            $query->where('folder', 'inbox');
        }

        // Full-Text / Keyword Search
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('body_text', 'like', "%{$search}%")
                  ->orWhere('from', 'like', "%{$search}%");
            });
        }

        $messages = $query->orderBy('received_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Normalize the JSON response to match frontend expectations
        $messages->getCollection()->transform(function ($msg) {
            $fromArray = is_array($msg->from) ? $msg->from : json_decode($msg->from, true) ?? [];
            $msg->from_name = $fromArray['name'] ?? null;
            $msg->from_email = $fromArray['email'] ?? $msg->from;
            $msg->is_starred = $msg->starred; // Map for frontend
            return $msg;
        });

        return response()->json($messages);
    }

    /**
     * Send email via SMTP (Queued).
     */
    public function send(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'mail_account_id' => 'required|exists:mail_accounts,id',
            'to' => 'required|array',
            'to.*' => 'required|email',
            'cc' => 'nullable|array',
            'cc.*' => 'email',
            'bcc' => 'nullable|array',
            'bcc.*' => 'email',
            'subject' => 'nullable|string',
            'body_text' => 'nullable|string',
            'body_html' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:20480', // max 20MB per file
        ]);

        $mailAccount = MailAccount::where('user_id', $user->id)->findOrFail($request->mail_account_id);

        // Store as Draft or dispatch for Sending
        $isDraft = $request->boolean('is_draft', false);
        
        $message = ExternalMessage::create([
            'mail_account_id' => $mailAccount->id,
            'message_id' => '<' . Str::uuid() . '@' . parse_url($mailAccount->smtp_host, PHP_URL_HOST) . '>',
            'from' => [
                'email' => $mailAccount->from_email,
                'name' => $mailAccount->from_name,
            ],
            'to' => $request->to,
            'cc' => $request->cc ?? [],
            'bcc' => $request->bcc ?? [],
            'subject' => $request->subject ?? '(No Subject)',
            'body_text' => $request->body_text ?? strip_tags($request->body_html ?? ''),
            'body_html' => $request->body_html ?? nl2br(e($request->body_text ?? '')),
            'direction' => 'outbound',
            'folder' => $isDraft ? 'draft' : 'sent',
            'is_read' => true,
            'sent_at' => $isDraft ? null : now(),
            'received_at' => $isDraft ? null : now(),
        ]);

        // Process attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/' . $message->id);
                Attachment::create([
                    'message_id' => $message->id,
                    'message_type' => ExternalMessage::class,
                    'filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'path' => $path,
                ]);
            }
        }

        if (!$isDraft) {
            // Dispatch SMTP Send Queue Job
            SendOutgoingEmail::dispatch($message);
        }

        return response()->json([
            'message' => $isDraft ? 'Draft saved.' : 'Email queued for sending.',
            'data' => $message->load('attachments')
        ], 201);
    }

    /**
     * Update flags like read, starred, snoozed, or change folders.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $accountIds = $user->mailAccounts()->pluck('id');
        $message = ExternalMessage::whereIn('mail_account_id', $accountIds)->findOrFail($id);

        $data = $request->validate([
            'is_read' => 'nullable|boolean',
            'is_starred' => 'nullable|boolean', // frontend sends this
            'folder' => 'nullable|string|in:inbox,sent,draft,spam,trash,starred,snoozed',
        ]);

        if (isset($data['is_starred'])) {
            $message->starred = $data['is_starred'];
        }

        if (isset($data['folder'])) {
            $message->folder = $data['folder'];
        }

        if (isset($data['is_read'])) {
            $message->is_read = $data['is_read'];
        }

        $message->save();

        return response()->json($message);
    }
}
