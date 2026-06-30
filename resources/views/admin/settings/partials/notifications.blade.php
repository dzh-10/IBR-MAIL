<div id="section-notifications" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Email Notifications</label>
                <div class="form-text">Send system notifications via email.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_email" {{ setting('notify_email') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Realtime Notifications</label>
                <div class="form-text">Enable WebSockets (Reverb) for instant UI updates.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_realtime" {{ setting('notify_realtime') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Internal Message Alerts</label>
                <div class="form-text">Notify users when they receive a new internal chat message.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_internal" {{ setting('notify_internal') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">External Email Alerts</label>
                <div class="form-text">Notify users when a new external email arrives via IMAP/POP3.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_external" {{ setting('notify_external') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Notification Sound</label>
                <div class="form-text">Play an audio chime on new notification.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="notify_sound" {{ setting('notify_sound') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>
</div>
