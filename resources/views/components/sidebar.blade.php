<aside class="sidebar">
    <!-- Logo -->
    <div style="height: 64px; display: flex; align-items: center; padding: 0 1.5rem; border-bottom: 1px solid var(--border-color);">
        <h1 style="font-size: 1.25rem; font-weight: 600; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            Messagerie
        </h1>
    </div>

    <div style="padding: 1.5rem;">
        <button class="btn btn-primary" style="width: 100%; justify-content: flex-start; padding: 0.75rem 1rem; border-radius: var(--radius-lg); font-size: 1rem; gap: 0.5rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            {{ __('Compose') }}
        </button>
    </div>

    <!-- Navigation -->
    <nav style="flex: 1; padding: 0 1rem; overflow-y: auto;">
        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; padding: 0 0.5rem;">{{ __('Messages') }}</div>
        
        <a href="/inbox" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-main); margin-bottom: 0.25rem; background-color: var(--primary-light); font-weight: 500;">
            <div class="flex items-center gap-2">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                {{ __('Inbox') }}
            </div>
            <span style="background-color: var(--primary-color); color: white; border-radius: 9999px; padding: 0.125rem 0.5rem; font-size: 0.75rem;">12</span>
        </a>

        <a href="/sent" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-main); margin-bottom: 0.25rem;">
            <div class="flex items-center gap-2 text-muted">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                {{ __('Sent') }}
            </div>
        </a>

        <a href="/drafts" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-main); margin-bottom: 0.25rem;">
            <div class="flex items-center gap-2 text-muted">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                {{ __('Drafts') }}
            </div>
        </a>

        <div style="font-size: 0.75rem; text-transform: uppercase; font-weight: 600; color: var(--text-muted); margin: 1.5rem 0 0.5rem 0; padding: 0 0.5rem;">{{ __('Company') }}</div>
        
        <a href="/contacts" style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem; border-radius: var(--radius-md); text-decoration: none; color: var(--text-main); margin-bottom: 0.25rem;">
            <div class="flex items-center gap-2 text-muted">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                {{ __('Contacts') }}
            </div>
        </a>
    </nav>

    <!-- Storage Usage -->
    <div style="padding: 1.5rem; border-top: 1px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
            <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500;">Storage</span>
            <span style="font-size: 0.75rem; color: var(--text-main); font-weight: 600;">64%</span>
        </div>
        <div style="height: 4px; background-color: var(--border-light); border-radius: 9999px; overflow: hidden;">
            <div style="height: 100%; width: 64%; background-color: var(--primary-color);"></div>
        </div>
        <a href="/settings" style="display: block; text-align: center; margin-top: 1rem; font-size: 0.75rem; color: var(--primary-color); text-decoration: none; font-weight: 500;">
            {{ __('Manage Settings') }}
        </a>
    </div>
</aside>
