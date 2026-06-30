<x-app-layout>
    <div class="workspace">
        
        <!-- List Panel -->
        <div class="list-panel">
            <div style="padding: 1rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-weight: 600; font-size: 1.125rem;">{{ __('Inbox') }}</h2>
                <button style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>
            </div>
            
            <div style="overflow-y: auto; flex: 1;">
                <!-- Stubbed Mail Items -->
                @for($i=1; $i<=10; $i++)
                <div style="padding: 1rem; border-bottom: 1px solid var(--border-light); cursor: pointer; transition: background-color var(--transition-fast); {{ $i===1 ? 'background-color: var(--primary-light); border-left: 3px solid var(--primary-color);' : '' }}">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.25rem;">
                        <span style="font-weight: {{ $i<=2 ? '600' : '500' }}; color: var(--text-main); font-size: 0.875rem;">Contact Name {{ $i }}</span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">10:{{ str_pad($i, 2, '0', STR_PAD_LEFT) }} AM</span>
                    </div>
                    <div style="font-weight: {{ $i<=2 ? '600' : '400' }}; font-size: 0.875rem; margin-bottom: 0.25rem; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Project Update and Next Steps
                    </div>
                    <div style="font-size: 0.875rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        Hi there, I wanted to reach out regarding the recent update to the project timeline...
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Reader Panel -->
        <div class="reader-panel">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 600; margin-bottom: 1rem;">Project Update and Next Steps</h2>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div style="width: 2.5rem; height: 2.5rem; border-radius: var(--radius-full); background-color: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600;">CN</div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.875rem;">Contact Name 1 <span style="font-weight: 400; color: var(--text-muted);">&lt;contact@example.com&gt;</span></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">To: me <span style="margin: 0 0.25rem;">•</span> Today at 10:01 AM</div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="btn" style="background-color: var(--border-light);"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg></button>
                    <button class="btn" style="background-color: var(--border-light);"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            </div>
            
            <div style="padding: 1.5rem; flex: 1; font-size: 0.875rem; line-height: 1.6; color: var(--text-main);">
                <p>Hi there,</p><br>
                <p>I wanted to reach out regarding the recent update to the project timeline. We've successfully completed the first phase and are ready to move forward.</p><br>
                <p>Please review the attached documents when you have a moment.</p><br>
                <p>Best regards,<br>Contact Name</p>
            </div>
            
            <!-- Quick Reply -->
            <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); background-color: var(--bg-color);">
                <div style="background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem;">
                    <textarea placeholder="{{ __('Click here to reply...') }}" style="width: 100%; border: none; resize: none; outline: none; font-family: inherit; font-size: 0.875rem; color: var(--text-main);" rows="3"></textarea>
                    <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                        <button class="btn btn-primary">{{ __('Send') }}</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
