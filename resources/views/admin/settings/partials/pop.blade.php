<div id="section-pop3" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">POP3 Host</label>
            <input type="text" id="pop_host" name="pop_host" class="form-control" value="{{ setting('pop_host') }}">
        </div>

        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label class="form-label">POP3 Port</label>
                <input type="number" id="pop_port" name="pop_port" class="form-control" value="{{ setting('pop_port') }}">
            </div>
            <div>
                <label class="form-label">Encryption</label>
                <select id="pop_encryption" name="pop_encryption" class="form-control">
                    <option value="tls" {{ setting('pop_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ setting('pop_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="none" {{ setting('pop_encryption') == 'none' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">POP3 Username</label>
            <input type="text" id="pop_username" name="pop_username" class="form-control" value="{{ setting('pop_username') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <label class="form-label">POP3 Password</label>
            <div class="password-wrapper">
                <input type="password" id="pop_password" name="pop_password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                <div class="toggle-pw" onclick="togglePassword('pop_password')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>
            <div class="form-text">Leave blank to keep existing password.</div>
        </div>

        <div style="margin-top: 24px;">
            <button type="button" class="btn btn-secondary" onclick="testPopConnection()">Test POP3 Connection</button>
            <span id="pop-test-result" style="margin-left: 12px; font-size: 13px; font-weight: 500;"></span>
        </div>
    </div>
</div>

<script>
async function testPopConnection() {
    const resultSpan = document.getElementById('pop-test-result');
    resultSpan.textContent = 'Testing...';
    resultSpan.style.color = 'var(--text-muted)';
    
    try {
        const res = await axios.post('/admin/settings/test/pop', {
            host: document.getElementById('pop_host').value,
            port: document.getElementById('pop_port').value,
            encryption: document.getElementById('pop_encryption').value,
            username: document.getElementById('pop_username').value,
            password: document.getElementById('pop_password').value
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
