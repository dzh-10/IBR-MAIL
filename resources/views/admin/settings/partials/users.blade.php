<div id="section-user" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Allow Self Registration</label>
                <div class="form-text">Allow public users to register new accounts.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="user_registration" {{ setting('user_registration') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <div class="form-group">
            <label class="form-label">Default Role</label>
            <select name="user_default_role" class="form-control">
                <option value="employee" {{ setting('user_default_role') == 'employee' ? 'selected' : '' }}>Employee</option>
                <option value="admin" {{ setting('user_default_role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <div class="form-text">The role assigned to newly registered users.</div>
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Allow Profile Editing</label>
                <div class="form-text">Allow users to change their own avatar and language preferences.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="user_allow_profile_edit" {{ setting('user_allow_profile_edit') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 32px 0;">

        <div class="form-group">
            <label class="form-label">Max Attachment Size (MB)</label>
            <input type="number" name="user_max_attachment_mb" class="form-control" value="{{ setting('user_max_attachment_mb', 10) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Allowed Attachment Types</label>
            <input type="text" name="user_allowed_attachments" class="form-control" value="{{ setting('user_allowed_attachments', 'jpg,png,pdf,doc,docx,xls,xlsx') }}">
            <div class="form-text">Comma-separated file extensions.</div>
        </div>
    </div>
</div>
