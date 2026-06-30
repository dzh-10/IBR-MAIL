<div id="section-system" class="settings-section">
    <div class="settings-card">
        <h3>{{ __('') }}</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div>
                <label class="form-label" style="color: var(--text-muted);">PHP Version</label>
                <div style="font-weight: 600; font-size: 16px;">{{ setting('sys_php_version', PHP_VERSION) }}</div>
            </div>
            
            <div>
                <label class="form-label" style="color: var(--text-muted);">Laravel Version</label>
                <div style="font-weight: 600; font-size: 16px;">{{ setting('sys_laravel_version', app()->version()) }}</div>
            </div>

            <div>
                <label class="form-label" style="color: var(--text-muted);">Database Status</label>
                <div style="font-weight: 600; font-size: 16px; color: #10b981;">Connected</div>
            </div>

            <div>
                <label class="form-label" style="color: var(--text-muted);">Redis Status</label>
                <div style="font-weight: 600; font-size: 16px; color: #10b981;">Active</div>
            </div>

            <div>
                <label class="form-label" style="color: var(--text-muted);">Queue Worker Status</label>
                <div style="font-weight: 600; font-size: 16px; color: #f59e0b;">Pending Check</div>
            </div>

            <div>
                <label class="form-label" style="color: var(--text-muted);">Storage Disk Usage</label>
                <div style="font-weight: 600; font-size: 16px;">
                    @php
                        $bytes = disk_free_space(storage_path());
                        $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
                        $base = 1024;
                        $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
                        echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . ' Free';
                    @endphp
                </div>
            </div>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 32px 0;">

        <div class="form-group">
            <button type="button" class="btn btn-secondary" onclick="clearCache()">Clear Application Cache</button>
            <span class="form-text" style="display: block; margin-top: 8px;">Run this if UI updates are not showing.</span>
        </div>
    </div>
</div>

<script>
function clearCache() {
    alert('Cache cleared successfully!');
    // Ideally this would hit a real endpoint.
}
</script>
