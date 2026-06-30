<div id="section-mail" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">SMTP Host</label>
            <input type="text" id="smtp_host" name="smtp_host" class="form-control" value="{{ setting('smtp_host') }}">
        </div>

        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label class="form-label">SMTP Port</label>
                <input type="number" id="smtp_port" name="smtp_port" class="form-control" value="{{ setting('smtp_port') }}">
            </div>
            <div>
                <label class="form-label">Encryption</label>
                <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                    <option value="tls" {{ setting('smtp_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ setting('smtp_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="none" {{ setting('smtp_encryption') == 'none' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">SMTP Username</label>
            <input type="text" id="smtp_username" name="smtp_username" class="form-control" value="{{ setting('smtp_username') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <label class="form-label">SMTP Password</label>
            <div class="password-wrapper">
                <input type="password" id="smtp_password" name="smtp_password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                <div class="toggle-pw" onclick="togglePassword('smtp_password')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>
            <div class="form-text">Leave blank to keep existing password.</div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 32px 0;">

        <div class="form-group">
            <label class="form-label">Global From Name</label>
            <input type="text" name="mail_from_name" class="form-control" value="{{ setting('mail_from_name') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Global From Email</label>
            <input type="email" name="mail_from_email" class="form-control" value="{{ setting('mail_from_email') }}">
        </div>

        <div style="margin-top: 24px;">
            <button type="button" class="btn btn-secondary" onclick="testSmtpConnection()">Test SMTP Connection</button>
            <span id="smtp-test-result" style="margin-left: 12px; font-size: 13px; font-weight: 500;"></span>
        </div>
    </div>
</div>

<script>
async function testSmtpConnection() {
    const resultSpan = document.getElementById('smtp-test-result');
    resultSpan.textContent = 'Testing...';
    resultSpan.style.color = 'var(--text-muted)';
    
    try {
        const res = await axios.post('/admin/settings/test/smtp', {
            host: document.getElementById('smtp_host').value,
            port: document.getElementById('smtp_port').value,
            encryption: document.getElementById('smtp_encryption').value,
            username: document.getElementById('smtp_username').value,
            password: document.getElementById('smtp_password').value
        });

        const data = res.data;
        resultSpan.textContent = data.message;
        resultSpan.style.color = data.success ? '#10b981' : 'var(--danger)';
    } catch (e) {
        if (e.response && e.response.data && e.response.data.message) {
            resultSpan.textContent = e.response.data.message;
        } else {
            resultSpan.textContent = 'Request failed.';
        }
        resultSpan.style.color = 'var(--danger)';
    }
}
</script>
