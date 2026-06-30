@extends('layouts.app_corpmail')

@section('title', 'CorpMail - Messagerie & Chat')

@section('content')
<div class="app-container" id="app">
    
    <!-- Toast System Notifications -->
    <div id="toast-container" class="toast-container"></div>

    <!-- 1. LEFT-MOST NAVIGATION BAR (80px) -->
    <aside class="sidebar-nav">
        <div style="display: flex; flex-direction: column; align-items: center; width: 100%;">
            <div class="nav-logo" style="display: flex; align-items: center; justify-content: center; height: 60px; width: 100%;">
                @if(setting('app_logo'))
                    <img src="{{ setting('app_logo') }}" alt="Logo" style="max-width: 40px; max-height: 40px; border-radius: 4px;">
                @else
                    M
                @endif
            </div>
            <nav class="nav-links">
                <!-- Mail toggle -->
                <div class="nav-item active" id="nav-mail-btn" title="Email Management">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <!-- Chat toggle -->
                <div class="nav-item" id="nav-chat-btn" title="Internal Chat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="badge" id="chat-global-unread" style="display: none;">0</span>
                </div>
                <!-- Contacts toggle -->
                <a href="/contacts" class="nav-item" id="nav-contacts-btn" title="Company Directory" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </a>
                <!-- Admin shortcut -->
                @if($user->is_admin)
                <a href="/admin" class="nav-item" title="Admin Panel" style="text-decoration: none;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </a>
                @endif
            </nav>
        </div>

        <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
            <!-- Dark mode toggle -->
            <button id="theme-toggle-btn" class="nav-item" style="border: none; background: none; font-size: inherit;" title="Toggle Light/Dark Mode">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <!-- User avatar with logout actions -->
            <div style="position: relative; cursor: pointer;" id="profile-dropdown-trigger">
                <img src="{{ $user->avatar ?: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80' }}" class="avatar" alt="Avatar">
                <div id="profile-dropdown-menu" style="display: none; position: absolute; bottom: 50px; left: 10px; width: 160px; background-color: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); z-index: 60;">
                    <div style="padding: 10px 14px; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-muted);">
                        Logged as <strong>{{ $user->name }}</strong>
                    </div>
                    <div id="logout-btn" style="padding: 10px 14px; font-size: 13px; color: var(--danger); hover: background-color: var(--primary-light); cursor: pointer;">
                        Sign Out
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- 2. SIDEBAR PANEL (Folders / Chat Contacts list) (280px) -->
    <section class="sidebar-chats-mails" id="sidebar-sub-panel">
        
        <!-- Mail Sidebar Panel -->
        <div id="sidebar-mail-section" style="display: flex; flex-direction: column; height: 100%;">
            <div class="sidebar-header">
                <h2>Mailbox</h2>
                <button class="compose-btn" id="compose-mail-btn">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Compose Email
                </button>
            </div>
            
            <div class="folder-nav-list">
                <div class="folder-nav-item active" data-folder="inbox">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0l-8 4-8-4"></path></svg>
                        Inbox
                    </span>
                    <span class="badge" id="badge-inbox"></span>
                </div>
                <div class="folder-nav-item" data-folder="starred">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.246.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.176 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.05 10.1c-.773-.565-.374-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z"></path></svg>
                        Starred
                    </span>
                </div>
                <div class="folder-nav-item" data-folder="snoozed">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Snoozed
                    </span>
                </div>
                <div class="folder-nav-item" data-folder="sent">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Sent
                    </span>
                </div>
                <div class="folder-nav-item" data-folder="draft">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Drafts
                    </span>
                </div>
                <div class="folder-nav-item" data-folder="spam">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Spam
                    </span>
                </div>
                <div class="folder-nav-item" data-folder="trash">
                    <span class="folder-nav-name">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Trash
                    </span>
                </div>
            </div>

            <!-- Mail Account Trigger sync buttons -->
            <div style="padding: 16px; margin-top: auto; border-top: 1px solid var(--border-color);">
                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 10px;">Mail Accounts</div>
                <div id="mail-accounts-sync-list" style="display: flex; flex-direction: column; gap: 8px;">
                    <!-- Dynamically populated -->
                </div>
            </div>
        </div>

        <!-- Chat Sidebar Panel (Toggleable) -->
        <div id="sidebar-chat-section" style="display: none; flex-direction: column; height: 100%;">
            <div class="sidebar-header">
                <h2>Chats</h2>
                <button class="compose-btn" id="new-chat-btn">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    New Chat
                </button>
            </div>
            
            <div class="scrollable-list" id="chat-list-container">
                <!-- Chat conversation items populate dynamically -->
            </div>
        </div>
    </section>

    <!-- 3. MAIN PANE VIEW (Flexible 1fr) -->
    <main class="main-view-pane" id="main-view-pane">
        
        <!-- Mail Table View (default shown) -->
        <div id="mail-view-container" style="display: flex; flex-direction: column; height: 100%;">
            <div class="pane-header">
                <div style="display: flex; align-items: center; gap: 12px; width: 50%;">
                    <div class="search-container">
                        <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" id="mail-search-input" class="search-input" placeholder="Search emails...">
                    </div>
                </div>
                <div style="font-size: 13px; color: var(--text-muted);" id="mail-pagination-count"></div>
            </div>

            <!-- Email Rows List -->
            <div class="scrollable-list" id="mail-rows-container" style="padding: 0;">
                <!-- Emails populate dynamically -->
            </div>
        </div>

        <!-- Chat Conversation View -->
        <div id="chat-view-container" style="display: none; flex-direction: column; height: 100%;">
            <div class="pane-header">
                <div class="pane-user-info">
                    <img id="chat-header-avatar" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80" class="avatar" alt="Avatar">
                    <div class="user-status-container">
                        <span class="user-status-name" id="chat-header-name">Choose a Chat</span>
                        <span class="user-status-text" id="chat-header-status">Select a conversation to start messaging</span>
                    </div>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="chat-messages-container" id="chat-messages-stream">
                <!-- Messages populating dynamically -->
            </div>

            <!-- Typing indicator -->
            <div class="typing-indicator" id="chat-typing-indicator" style="display: none;">
                <span id="chat-typing-name">Someone</span> is typing
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>

            <!-- Message Input bar -->
            <div class="chat-input-bar">
                <form class="chat-form" id="chat-message-form">
                    <input type="text" id="chat-message-input" class="chat-text-input" placeholder="Type a message..." autocomplete="off">
                    <!-- Simple Emoji Trigger -->
                    <button type="button" id="emoji-picker-btn" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">😀</button>
                    <button type="submit" class="compose-send-btn" style="padding: 10px 20px; border-radius: var(--radius-full);">Send</button>
                </form>
            </div>
        </div>

        <!-- Single Email Detail View Modal / Cover -->
        <div id="email-detail-container" style="display: none; flex-direction: column; height: 100%; background: var(--bg-surface); z-index: 20; position: absolute; left: 0; top: 0; right: 0; bottom: 0;">
            <div class="pane-header" style="border-bottom: 1px solid var(--border-color);">
                <button id="close-email-detail-btn" style="border: none; background: none; display: flex; align-items: center; gap: 8px; color: var(--text-muted); cursor: pointer; font-size: 14px;">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to List
                </button>

                <div style="display: flex; gap: 12px;">
                    <!-- star, trash icons -->
                    <button id="detail-star-btn" class="mail-star" style="border: none; background: none; font-size: 20px; cursor: pointer;">★</button>
                    <button id="detail-trash-btn" style="border: none; background: none; cursor: pointer; color: var(--text-muted);">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>

            <div style="flex: 1; overflow-y: auto; padding: 30px;">
                <h1 style="font-size: 22px; font-weight: 700; margin-bottom: 24px;" id="detail-subject">Email Subject</h1>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <img id="detail-avatar" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=100&h=100&q=80" class="avatar" alt="Sender">
                        <div>
                            <div style="font-weight: 600; font-size: 14px;" id="detail-from-name">Sender Name</div>
                            <div style="font-size: 12px; color: var(--text-muted);" id="detail-from-email">sender@example.com</div>
                        </div>
                    </div>
                    <div style="font-size: 12px; color: var(--text-muted);" id="detail-date">June 29, 2026</div>
                </div>

                <!-- Email HTML/Text Body Container -->
                <div id="detail-body-container" style="line-height: 1.6; font-size: 14px; color: var(--text-main); margin-bottom: 30px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                    <!-- Render body here safely -->
                </div>

                <!-- Attachments list -->
                <div id="detail-attachments-container" style="display: none; border-top: 1px solid var(--border-color); padding-top: 24px;">
                    <div style="font-size: 12px; font-weight: 600; text-transform: uppercase; color: var(--text-muted); margin-bottom: 12px;">Attachments</div>
                    <div id="detail-attachments-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                        <!-- Attachments dynamic -->
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 4. GMAIL-STYLE COMPOSE WINDOW -->
    <div class="compose-window" id="compose-window">
        <div class="compose-header">
            <span class="compose-header-title" id="compose-title">New Email</span>
            <div class="compose-controls">
                <button class="compose-control-btn" id="minimize-compose-btn">−</button>
                <button class="compose-control-btn" id="close-compose-btn">×</button>
            </div>
        </div>

        <form id="compose-form" style="display: flex; flex-direction: column; flex: 1;" enctype="multipart/form-data">
            <div class="compose-body">
                <!-- Select sending account -->
                <div class="compose-field">
                    <label class="compose-label">From:</label>
                    <select id="compose-from-select" name="mail_account_id" class="compose-field-input" style="border: none; background: none; font-size: 13px; font-family: var(--font-primary);">
                        <!-- populated dynamically -->
                    </select>
                </div>

                <div class="compose-field">
                    <label class="compose-label">To:</label>
                    <input type="text" id="compose-to" name="to_raw" class="compose-field-input" placeholder="recipients separated by comma" required>
                </div>

                <div class="compose-field">
                    <label class="compose-label">Subject:</label>
                    <input type="text" id="compose-subject" name="subject" class="compose-field-input" placeholder="Subject">
                </div>

                <textarea id="compose-body-text" name="body_text" class="compose-content-area" placeholder="Write your email content here..."></textarea>
                
                <!-- Attached Files preview during creation -->
                <div id="compose-attachments-preview" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
            </div>

            <div class="compose-footer">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <button type="submit" class="compose-send-btn">Send</button>
                    <!-- Attachment paperclip trigger -->
                    <label for="compose-file-input" style="cursor: pointer; color: var(--text-muted);" title="Attach Files">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    </label>
                    <input type="file" id="compose-file-input" name="attachments[]" multiple style="display: none;">
                </div>

                <button type="button" id="compose-save-draft-btn" style="border: none; background: none; color: var(--text-muted); cursor: pointer; font-size: 13px;">Save Draft</button>
            </div>
        </form>
    </div>

    <!-- 5. NEW CHAT / START CHAT DIALOG MODAL -->
    <div id="new-chat-modal" style="display: none; position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center;">
        <div class="glass" style="width: 400px; padding: 24px; border-radius: var(--radius-md); background-color: var(--bg-surface); box-shadow: var(--shadow-lg);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-weight: 600; font-size: 16px;">Start Conversation</h3>
                <button id="close-chat-modal-btn" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">×</button>
            </div>
            
            <div class="search-container" style="margin-bottom: 16px;">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="employee-autocomplete-input" class="search-input" placeholder="Search employees by name...">
            </div>

            <div class="scrollable-list" id="employee-results-list" style="max-height: 200px;">
                <!-- Employee autocomplete options -->
            </div>
        </div>
    </div>
</div>

<!-- Config parameters injection for chat.js script -->
<div id="corpchat-config" data-user-id="{{ $user->id }}" data-user-name="{{ $user->name }}" style="display: none;"></div>
@endsection
