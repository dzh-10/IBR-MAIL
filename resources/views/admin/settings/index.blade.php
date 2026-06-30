@extends('layouts.app_corpmail')

@section('title', 'Global Settings - CorpMail Admin')

@section('content')
<style>
    .settings-container {
        display: grid;
        grid-template-columns: 260px 1fr;
        height: calc(100vh - var(--header-height));
        background-color: var(--bg-base);
    }
    
    .settings-sidebar {
        background-color: var(--bg-surface);
        border-right: 1px solid var(--border-color);
        padding: 20px 0;
        overflow-y: auto;
    }

    .settings-menu-item {
        padding: 12px 24px;
        cursor: pointer;
        font-weight: 500;
        font-size: 14px;
        color: var(--text-main);
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 12px;
        border-left: 3px solid transparent;
    }

    .settings-menu-item:hover {
        background-color: var(--bg-base);
    }

    .settings-menu-item.active {
        background-color: var(--primary-light);
        color: var(--primary);
        border-left-color: var(--primary);
    }

    .settings-content {
        padding: 40px;
        overflow-y: auto;
    }

    .settings-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: var(--shadow-sm);
    }

    .settings-card h3 {
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 18px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 12px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 6px;
        color: var(--text-main);
    }

    .form-text {
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background-color: var(--bg-base);
        color: var(--text-main);
        font-family: inherit;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .toggle-slider {
        background-color: var(--primary);
    }
    input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }

    .btn {
        padding: 10px 20px;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: var(--transition-smooth);
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .btn-secondary {
        background-color: var(--bg-base);
        color: var(--text-main);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background-color: #e2e8f0;
    }

    .save-toast {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background-color: #10b981;
        color: white;
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-md);
        display: none;
        align-items: center;
        gap: 12px;
        z-index: 9999;
    }

    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .toggle-pw {
        position: absolute;
        right: 12px;
        top: 10px;
        cursor: pointer;
        color: var(--text-muted);
    }
</style>

<div class="settings-container">
    <aside class="settings-sidebar">
        <div style="padding: 0 24px 16px;">
            <h2 style="font-size: 18px; font-weight: 700; color: var(--text-main);">Global Settings</h2>
        </div>
        
        <div class="settings-menu-item active" onclick="showTab('general')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('branding')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('mail')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('imap')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('pop3')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('sync')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('notifications')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('user')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('storage')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('security')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('localization')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('') }}
        </div>
        <div class="settings-menu-item" onclick="showTab('system')">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
            {{ __('') }}
        </div>
        
        <div style="margin-top: 40px; padding: 0 24px;">
            <a href="/admin" class="btn btn-secondary" style="display: block; text-align: center; text-decoration: none;">&larr; Back to Admin</a>
        </div>
    </aside>

    <main class="settings-content">
        <div class="top-bar">
            <h1 id="tab-title" style="font-size: 24px; font-weight: 700;">General Settings</h1>
            <button class="btn btn-primary" onclick="saveSettings()">Save Changes</button>
        </div>

        <form id="settings-form" onsubmit="event.preventDefault(); saveSettings();">
            @include('admin.settings.partials.general')
            @include('admin.settings.partials.branding')
            @include('admin.settings.partials.mail')
            @include('admin.settings.partials.imap')
            @include('admin.settings.partials.pop')
            @include('admin.settings.partials.sync')
            @include('admin.settings.partials.notifications')
            @include('admin.settings.partials.users')
            @include('admin.settings.partials.storage')
            @include('admin.settings.partials.security')
            @include('admin.settings.partials.localization')
            @include('admin.settings.partials.system')
        </form>
    </main>
</div>

<!-- Toast -->
<div class="save-toast" id="save-toast">
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    Settings saved successfully.
</div>

<script>
    let currentTab = 'general';

    function showTab(tabId) {
        currentTab = tabId;
        
        // Update menu
        document.querySelectorAll('.settings-menu-item').forEach(el => el.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // Update title
        document.getElementById('tab-title').textContent = event.currentTarget.textContent.trim();
        
        // Show partial
        document.querySelectorAll('.settings-section').forEach(el => el.style.display = 'none');
        document.getElementById('section-' + tabId).style.display = 'block';
    }

    // Toggle password visibility
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }

    async function saveSettings() {
        const form = document.getElementById('settings-form');
        // Get only inputs from the current visible section to save per group
        const section = document.getElementById('section-' + currentTab);
        const formData = new FormData();
        
        section.querySelectorAll('input, select, textarea').forEach(input => {
            if (input.type === 'checkbox') {
                formData.append(input.name, input.checked ? '1' : '0');
            } else if (input.name && !input.disabled) {
                // If it's a password field and it's empty, don't update it to empty
                if (input.type === 'password' && !input.value) return;
                formData.append(input.name, input.value);
            }
        });

        const data = Object.fromEntries(formData.entries());
        
        try {
            const res = await axios.post(`/admin/settings/${currentTab}`, data);

            if (res.status === 200) {
                showToast();
                // Optionally refresh page if branding was changed to update logo
                if (currentTab === 'branding') setTimeout(() => window.location.reload(), 1000);
            } else {
                alert('Failed to save settings.');
            }
        } catch (e) {
            console.error(e);
            alert('An error occurred.');
        }
    }

    function showToast() {
        const toast = document.getElementById('save-toast');
        toast.style.display = 'flex';
        setTimeout(() => toast.style.display = 'none', 3000);
    }
    
    // Initialize first tab
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.settings-section').forEach(el => el.style.display = 'none');
        document.getElementById('section-general').style.display = 'block';
    });
</script>
@endsection
