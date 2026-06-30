<div id="section-localization" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">System Default Language</label>
            <select name="loc_default_lang" class="form-control">
                <option value="en" {{ setting('loc_default_lang') == 'en' ? 'selected' : '' }}>English</option>
                <option value="fr" {{ setting('loc_default_lang') == 'fr' ? 'selected' : '' }}>Français</option>
            </select>
            <div class="form-text">The default language for new visitors and guests.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Available Languages</label>
            <input type="text" name="loc_available_langs" class="form-control" value="{{ setting('loc_available_langs', 'en,fr') }}">
            <div class="form-text">Comma-separated list of active language codes.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Date & Time Locale</label>
            <input type="text" name="loc_datetime_locale" class="form-control" value="{{ setting('loc_datetime_locale', 'en_US') }}">
            <div class="form-text">Used for formatting dates and numbers.</div>
        </div>
    </div>
</div>
