<div id="section-imap" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">IMAP Host</label>
            <input type="text" id="imap_host" name="imap_host" class="form-control" value="{{ setting('imap_host') }}">
        </div>

        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label class="form-label">IMAP Port</label>
                <input type="number" id="imap_port" name="imap_port" class="form-control" value="{{ setting('imap_port') }}">
            </div>
            <div>
                <label class="form-label">Encryption</label>
                <select id="imap_encryption" name="imap_encryption" class="form-control">
                    <option value="tls" {{ setting('imap_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                    <option value="ssl" {{ setting('imap_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    <option value="none" {{ setting('imap_encryption') == 'none' ? 'selected' : '' }}>None</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">IMAP Username</label>
            <input type="text" id="imap_username" name="imap_username" class="form-control" value="{{ setting('imap_username') }}" autocomplete="off">
        </div>

        <div class="form-group">
            <label class="form-label">IMAP Password</label>
            <div class="password-wrapper">
                <input type="password" id="imap_password" name="imap_password" class="form-control" placeholder="••••••••" autocomplete="new-password">
                <div class="toggle-pw" onclick="togglePassword('imap_password')">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </div>
            </div>
            <div class="form-text">Leave blank to keep existing password.</div>
        </div>

        <div class="form-group">
            <label class="form-label">IMAP Folder</label>
            <input type="text" id="imap_folder" name="imap_folder" class="form-control" value="{{ setting('imap_folder', 'INBOX') }}">
            <div class="form-text">Default folder to synchronize.</div>
        </div>

        <div style="margin-top: 24px;">
            <button type="button" class="btn btn-secondary" onclick="testImapConnection()">Test IMAP Connection</button>
            <span id="imap-test-result" style="margin-left: 12px; font-size: 13px; font-weight: 500;"></span>
        </div>
    </div>
</div>

<script>
async function testImapConnection() {
    const resultSpan = document.getElementById('imap-test-result');
    resultSpan.textContent = 'Testing...';
    resultSpan.style.color = 'var(--text-muted)';
    
    try {
        const res = await axios.post('/admin/settings/test/imap', {
            host: document.getElementById('imap_host').value,
            port: document.getElementById('imap_port').value,
            encryption: document.getElementById('imap_encryption').value,
            username: document.getElementById('imap_username').value,
            password: document.getElementById('imap_password').value
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
