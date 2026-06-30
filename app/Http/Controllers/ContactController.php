<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%")
                  ->orWhere('job_title', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('type')) {
            if ($request->type === 'internal') {
                $query->where('is_internal', true);
            } elseif ($request->type === 'external') {
                $query->where('is_internal', false);
            }
        }

        $contacts = $query->orderBy('name')->paginate(50);
        
        $departments = Contact::whereNotNull('department')->distinct()->pluck('department');

        return view('contacts.index', compact('contacts', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:contacts',
            'department' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Check if an internal user matches the email
        $user = User::where('email', $validated['email'])->first();
        
        $validated['is_internal'] = $user ? true : false;
        $validated['user_id'] = $user ? $user->id : null;
        $validated['is_active'] = true;
        
        if ($user && $user->avatar) {
            $validated['avatar'] = $user->avatar;
        } else {
            // Generate a default avatar
            $validated['avatar'] = 'https://ui-avatars.com/api/?name=' . urlencode($validated['name']) . '&background=random';
        }

        Contact::create($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact added successfully.');
    }

    public function update(Request $request, Contact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:contacts,email,' . $contact->id,
            'department' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        // Re-check internal status if email changes
        if ($contact->email !== $validated['email']) {
            $user = User::where('email', $validated['email'])->first();
            $validated['is_internal'] = $user ? true : false;
            $validated['user_id'] = $user ? $user->id : null;
        }

        $contact->update($validated);

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }
}
