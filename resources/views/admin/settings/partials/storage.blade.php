<div id="section-storage" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">Default Storage Disk</label>
            <select name="storage_disk" class="form-control">
                <option value="local" {{ setting('storage_disk') == 'local' ? 'selected' : '' }}>Local (Public/Storage)</option>
                <option value="s3" {{ setting('storage_disk') == 's3' ? 'selected' : '' }}>Amazon S3</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Max Total Storage Per User (MB)</label>
            <input type="number" name="storage_max_per_user" class="form-control" value="{{ setting('storage_max_per_user', 1024) }}">
            <div class="form-text">Soft limit for user's inbox size.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Archive Old Emails After (Days)</label>
            <input type="number" name="storage_archive_days" class="form-control" value="{{ setting('storage_archive_days', 365) }}">
            <div class="form-text">Automatically move emails older than this to the archive.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Auto Delete Trash After (Days)</label>
            <input type="number" name="storage_trash_days" class="form-control" value="{{ setting('storage_trash_days', 30) }}">
            <div class="form-text">Permanently delete emails from Trash after this duration.</div>
        </div>
    </div>
</div>
