<div id="section-security" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Force HTTPS</label>
                <div class="form-text">Redirect all HTTP traffic to HTTPS.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="security_force_https" {{ setting('security_force_https') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label">Session Lifetime (minutes)</label>
            <input type="number" name="security_session_lifetime" class="form-control" value="{{ setting('security_session_lifetime', 120) }}">
        </div>

        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label class="form-label">Max Login Attempts</label>
                <input type="number" name="security_max_login_attempts" class="form-control" value="{{ setting('security_max_login_attempts', 5) }}">
            </div>
            <div>
                <label class="form-label">Lockout Duration (minutes)</label>
                <input type="number" name="security_lockout_duration" class="form-control" value="{{ setting('security_lockout_duration', 15) }}">
            </div>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Enable Two-Factor Authentication</label>
                <div class="form-text">Require users to set up 2FA via authenticator app.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="security_2fa_enabled" {{ setting('security_2fa_enabled') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label">Minimum Password Length</label>
            <input type="number" name="security_min_password_len" class="form-control" value="{{ setting('security_min_password_len', 8) }}">
        </div>
    </div>
</div>
