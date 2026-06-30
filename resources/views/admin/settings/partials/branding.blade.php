<div id="section-branding" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div class="form-group">
            <label class="form-label">Application Logo</label>
            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                @if(setting('app_logo'))
                <img src="{{ setting('app_logo') }}" alt="Logo Preview" style="max-height: 40px; background: var(--bg-base); padding: 4px; border-radius: 4px; border: 1px solid var(--border-color);">
                @else
                <div style="height: 40px; width: 40px; display:flex; align-items:center; justify-content:center; background: var(--bg-base); border-radius: 4px; border: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px;">None</div>
                @endif
                <input type="file" id="logo-upload" accept="image/*" style="display: none;" onchange="uploadImage(this, 'logo')">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('logo-upload').click()">Upload New Logo</button>
            </div>
            <div class="form-text">Max 2MB. SVG, PNG, WebP or JPG.</div>
        </div>

        <div class="form-group" style="margin-top: 32px;">
            <label class="form-label">Application Favicon</label>
            <div style="display: flex; gap: 16px; align-items: center; margin-bottom: 12px;">
                @if(setting('app_favicon'))
                <img src="{{ setting('app_favicon') }}" alt="Favicon Preview" style="max-height: 32px; max-width: 32px;">
                @else
                <div style="height: 32px; width: 32px; display:flex; align-items:center; justify-content:center; background: var(--bg-base); border-radius: 4px; border: 1px solid var(--border-color); color: var(--text-muted); font-size: 10px;">None</div>
                @endif
                <input type="file" id="favicon-upload" accept="image/*" style="display: none;" onchange="uploadImage(this, 'favicon')">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('favicon-upload').click()">Upload New Favicon</button>
            </div>
            <div class="form-text">Must be a square image (32x32 recommended).</div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 32px 0;">

        <div class="form-group">
            <label class="form-label">Primary Color</label>
            <div style="display: flex; gap: 12px; align-items: center;">
                <input type="color" name="primary_color" value="{{ setting('primary_color', '#3b82f6') }}" style="width: 40px; height: 40px; padding: 0; border: none; cursor: pointer;">
                <input type="text" class="form-control" value="{{ setting('primary_color', '#3b82f6') }}" readonly style="width: 120px;">
            </div>
            <div class="form-text">Used for buttons, links, and active states.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Footer Text</label>
            <input type="text" name="footer_text" class="form-control" value="{{ setting('footer_text') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Copyright Text</label>
            <input type="text" name="copyright_text" class="form-control" value="{{ setting('copyright_text') }}">
        </div>
    </div>
</div>

<script>
async function uploadImage(input, type) {
    if (!input.files || input.files.length === 0) return;
    
    const formData = new FormData();
    formData.append('file', input.files[0]);
    formData.append('type', type);

    try {
        const res = await fetch('/admin/settings/logo', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        });

        if (res.ok) {
            window.location.reload();
        } else {
            const data = await res.json();
            alert(data.message || 'Upload failed');
        }
    } catch (e) {
        console.error(e);
        alert('Upload error');
    }
}
</script>
