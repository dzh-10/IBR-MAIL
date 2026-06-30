<?php

namespace App\Http\Controllers;

use App\Models\MailAccount;
use App\Jobs\SyncImapMailbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MailAccountController extends Controller
{
    /**
     * List all mail accounts for the current user.
     */
    public function index()
    {
        if (Auth::user()->is_admin) {
            $accounts = MailAccount::all();
        } else {
            $accounts = Auth::user()->mailAccounts;
        }
        return response()->json($accounts);
    }

    /**
     * Create a new mail account configuration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:mail_accounts,email',
            'name' => 'required|string',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'nullable|string|in:tls,ssl,none',
            'imap_host' => 'required|string',
            'imap_port' => 'required|integer',
            'imap_username' => 'required|string',
            'imap_password' => 'required|string',
        ]);

        $userId = Auth::id();
        if (Auth::user()->is_admin) {
            $targetUser = \App\Models\User::where('email', $request->email)->first();
            if ($targetUser) {
                $userId = $targetUser->id;
            }
        }

        $account = MailAccount::create(array_merge($request->all(), [
            'user_id' => $userId,
            'from_email' => $request->email,
            'from_name' => $request->name,
            'smtp_encryption' => $request->smtp_encryption ?? 'tls',
            'imap_encryption' => $request->imap_encryption ?? 'ssl',
        ]));

        return response()->json($account, 201);
    }

    /**
     * Trigger IMAP sync.
     */
    public function sync(Request $request, $id)
    {
        $user = Auth::user();
        
        // If employee/admin, confirm ownership (or allow admin to sync any)
        $account = MailAccount::findOrFail($id);
        if (!$user->is_admin && $account->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Dispatch background IMAP sync job
        SyncImapMailbox::dispatch($account);

        return response()->json([
            'message' => 'IMAP synchronization job dispatched.'
        ]);
    }
}
