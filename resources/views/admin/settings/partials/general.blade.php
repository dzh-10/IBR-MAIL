<div id="section-general" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">Application Name</label>
            <input type="text" name="app_name" class="form-control" value="{{ setting('app_name') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Application URL</label>
            <input type="url" name="app_url" class="form-control" value="{{ setting('app_url') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Default Language</label>
            <select name="default_language" class="form-control">
                <option value="en" {{ setting('default_language') == 'en' ? 'selected' : '' }}>English</option>
                <option value="fr" {{ setting('default_language') == 'fr' ? 'selected' : '' }}>Français</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Timezone</label>
            <input type="text" name="timezone" class="form-control" value="{{ setting('timezone') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Date Format</label>
            <input type="text" name="date_format" class="form-control" value="{{ setting('date_format') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Pagination Limit</label>
            <input type="number" name="pagination_limit" class="form-control" value="{{ setting('pagination_limit') }}">
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <label class="form-label" style="margin-bottom: 0;">Maintenance Mode</label>
                <div class="form-text">If enabled, users will see a maintenance page.</div>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" name="maintenance_mode" {{ setting('maintenance_mode') ? 'checked' : '' }}>
                <span class="toggle-slider"></span>
            </label>
        </div>
    </div>
</div>
