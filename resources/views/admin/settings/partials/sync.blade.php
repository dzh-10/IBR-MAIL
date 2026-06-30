<div id="section-sync" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">Sync Frequency (minutes)</label>
            <input type="number" name="sync_frequency" class="form-control" value="{{ setting('sync_frequency', 5) }}">
            <div class="form-text">How often background jobs should check for new emails.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Max Emails Per Sync</label>
            <input type="number" name="sync_max_emails" class="form-control" value="{{ setting('sync_max_emails', 50) }}">
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Auto Mark as Read</label>
                <div class="form-text">Automatically mark fetched emails as read on the server.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="sync_auto_read" {{ setting('sync_auto_read') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 32px 0;">

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Enable IMAP Sync</label>
                <div class="form-text">Allow the system to sync incoming emails via IMAP.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="sync_imap_enabled" {{ setting('sync_imap_enabled') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Enable POP3 Sync</label>
                <div class="form-text">Allow the system to sync incoming emails via POP3.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="sync_pop_enabled" {{ setting('sync_pop_enabled') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>
</div>
