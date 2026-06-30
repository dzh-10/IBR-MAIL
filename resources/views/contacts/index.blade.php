@extends('layouts.app_corpmail')

@section('title', 'Contacts Directory - CorpMail')

@section('content')
<div class="app-container" id="app">
    <!-- 1. LEFT-MOST NAVIGATION BAR (80px) -->
    <aside class="sidebar-nav">
        <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
            <div class="nav-logo">M</div>
            <nav class="nav-links">
                <!-- Mail toggle -->
                <a href="/" class="nav-item" title="Email Management" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </a>
                <!-- Chat toggle -->
                <a href="/?chat=1" class="nav-item" title="Internal Chat" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                </a>
                <!-- Contacts toggle -->
                <a href="/contacts" class="nav-item active" id="nav-contacts-btn" title="Company Directory" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </a>
                <!-- Admin shortcut -->
                @if(Auth::user()->is_admin)
                <a href="/admin" class="nav-item" title="Admin Panel" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </a>
                @endif
            </nav>
        </div>
    </aside>

    <!-- MAIN CONTACTS PANE -->
    <main class="main-view-pane" style="flex: 1; display: flex; flex-direction: column;">
        <div class="pane-header" style="border-bottom: 1px solid var(--border-color); padding: 20px; display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 24px; font-weight: 700; margin: 0;">Company Directory</h1>
            
            <form action="{{ route('contacts.index') }}" method="GET" style="display: flex; gap: 12px; align-items: center;">
                <div class="search-container" style="width: 300px;">
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" name="search" class="search-input" placeholder="Search contacts..." value="{{ request('search') }}">
                </div>
                
                <select name="department" class="compose-field-input" style="width: 150px; padding: 8px 12px; font-size: 13px;" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                    @endforeach
                </select>
                
                <select name="type" class="compose-field-input" style="width: 120px; padding: 8px 12px; font-size: 13px;" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>External</option>
                </select>

                @if(Auth::user()->is_admin)
                <button type="button" class="compose-send-btn" style="padding: 8px 16px; border-radius: var(--radius-sm);" onclick="document.getElementById('modal-add-contact').style.display='flex'">
                    + Add Contact
                </button>
                @endif
            </form>
        </div>

        <div style="flex: 1; overflow-y: auto; padding: 24px; background: var(--bg-body);">
            @if(session('success'))
            <div style="padding: 12px 16px; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-md); margin-bottom: 24px;">
                {{ session('success') }}
            </div>
            @endif

            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                @forelse($contacts as $contact)
                <div class="glass" style="border-radius: var(--radius-lg); padding: 20px; display: flex; flex-direction: column; gap: 16px; border: 1px solid var(--border-color); box-shadow: var(--shadow-sm); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
                    <div style="display: flex; align-items: flex-start; gap: 16px;">
                        <div style="position: relative;">
                            <img src="{{ $contact->avatar }}" class="avatar" style="width: 60px; height: 60px;" alt="Avatar">
                            @if($contact->is_internal)
                            <div style="position: absolute; bottom: 0; right: 0; width: 14px; height: 14px; background: var(--success); border: 2px solid var(--bg-surface); border-radius: 50%;" title="Internal Employee"></div>
                            @endif
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h3 style="font-weight: 600; font-size: 16px; margin: 0 0 4px 0; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $contact->name }}</h3>
                            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $contact->job_title ?: 'No Title' }}</div>
                            @if($contact->department)
                            <span style="display: inline-block; padding: 2px 8px; background: var(--primary-light); color: var(--primary); border-radius: var(--radius-full); font-size: 11px; font-weight: 600;">{{ $contact->department }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div style="font-size: 13px; color: var(--text-muted); display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $contact->email }}">
                            <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ $contact->email }}
                        </div>
                        @if($contact->phone)
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <svg style="width: 16px; height: 16px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ $contact->phone }}
                        </div>
                        @endif
                    </div>

                    <div style="display: flex; gap: 8px; margin-top: auto; padding-top: 16px; border-top: 1px solid var(--border-color);">
                        @if($contact->is_internal && $contact->user_id)
                        <a href="/?chat=1&user={{ $contact->user_id }}" style="flex: 1; text-align: center; padding: 8px; border-radius: var(--radius-md); background: var(--primary-light); color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid transparent; transition: all 0.2s;" onmouseover="this.style.background='var(--primary)'; this.style.color='white';" onmouseout="this.style.background='var(--primary-light)'; this.style.color='var(--primary)';">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Chat
                        </a>
                        @endif
                        <a href="/?compose=1&to={{ urlencode($contact->email) }}" style="flex: 1; text-align: center; padding: 8px; border-radius: var(--radius-md); background: transparent; color: var(--text-main); text-decoration: none; font-size: 13px; font-weight: 500; display: flex; align-items: center; justify-content: center; gap: 6px; border: 1px solid var(--border-color); transition: all 0.2s;" onmouseover="this.style.background='var(--bg-hover)';" onmouseout="this.style.background='transparent';">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Email
                        </a>
                    </div>
                </div>
                @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                    <svg style="width: 48px; height: 48px; margin: 0 auto 16px auto; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <p style="font-size: 16px; font-weight: 500; color: var(--text-main); margin: 0 0 8px 0;">No contacts found</p>
                    <p style="font-size: 14px; margin: 0;">Try adjusting your search or filters.</p>
                </div>
                @endforelse
            </div>
            
            <div style="margin-top: 30px;">
                {{ $contacts->links() }}
            </div>
        </div>
    </main>
</div>

<!-- Modal Add Contact -->
@if(Auth::user()->is_admin)
<div id="modal-add-contact" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
    <div class="glass" style="padding: 32px; border-radius: var(--radius-md); width: 420px; box-shadow: var(--shadow-lg); background-color: var(--bg-surface);">
        <h3 style="margin-bottom: 20px; font-weight: 600;">Add Company Contact</h3>
        <form action="{{ route('contacts.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Name *</label>
                <input type="text" name="name" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main); border-radius: var(--radius-sm);" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Email *</label>
                <input type="email" name="email" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main); border-radius: var(--radius-sm);" required>
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Department</label>
                <input type="text" name="department" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main); border-radius: var(--radius-sm);">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Job Title</label>
                <input type="text" name="job_title" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main); border-radius: var(--radius-sm);">
            </div>
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 12px; margin-bottom: 6px; font-weight: 600;">Phone</label>
                <input type="text" name="phone" style="width:100%; padding:10px; border:1px solid var(--border-color); background: var(--bg-base); color: var(--text-main); border-radius: var(--radius-sm);">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" class="compose-send-btn" style="background: var(--bg-hover); color: var(--text-main);" onclick="document.getElementById('modal-add-contact').style.display='none'">Cancel</button>
                <button type="submit" class="compose-send-btn">Save Contact</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
