<header class="topbar">
    <div class="flex items-center gap-4">
        <!-- Search -->
        <div style="width: 400px; position: relative;">
            <input type="text" class="input-field" placeholder="{{ __('Search messages, contacts...') }}" style="padding-left: 2.5rem; background-color: var(--border-light); border: none;">
            <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: var(--text-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <!-- Language Switcher -->
        <select class="input-field" style="width: auto; padding: 0.25rem 0.5rem;" onchange="window.location.href='/lang/'+this.value">
            <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
            <option value="fr" {{ app()->getLocale() == 'fr' ? 'selected' : '' }}>Français</option>
        </select>

        <!-- Notifications -->
        <button style="background: none; border: none; cursor: pointer; color: var(--text-muted); position: relative;">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span style="position: absolute; top: 0; right: 0; width: 0.5rem; height: 0.5rem; background-color: var(--danger); border-radius: var(--radius-full);"></span>
        </button>

        <!-- Profile -->
        <div style="width: 2rem; height: 2rem; border-radius: var(--radius-full); background-color: var(--primary-light); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: 600;">
            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
        </div>
    </div>
</header>
