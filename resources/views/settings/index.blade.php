<x-app-layout>
    <div class="workspace" style="flex-direction: column;">
        <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); background-color: var(--surface-color);">
            <h2 style="font-size: 1.5rem; font-weight: 600;">{{ __('Settings') }}</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem;">{{ __('Manage your global platform settings.') }}</p>
        </div>

        <div style="flex: 1; padding: 1.5rem; overflow-y: auto; background-color: var(--bg-color);">
            <div style="max-width: 800px; margin: 0 auto;">
                
                @if(session('success'))
                    <div style="background-color: var(--success); color: white; padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    
                    <div style="background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
                        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background-color: var(--border-light);">
                            <h3 style="font-weight: 600;">{{ __('General & Branding') }}</h3>
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Platform Name') }}</label>
                                <input type="text" name="site_name" value="{{ $settings['site_name']->value ?? 'Messagerie' }}" class="input-field">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('Logo URL') }}</label>
                                <input type="text" name="logo_url" value="{{ $settings['logo_url']->value ?? '' }}" class="input-field" placeholder="/images/logo.png">
                            </div>
                        </div>
                    </div>

                    <div style="background-color: var(--surface-color); border: 1px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; margin-bottom: 1.5rem; box-shadow: var(--shadow-sm);">
                        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); background-color: var(--border-light);">
                            <h3 style="font-weight: 600;">{{ __('Default Mail Server Configuration') }}</h3>
                        </div>
                        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('SMTP Host') }}</label>
                                <input type="text" name="smtp_host" value="{{ $settings['smtp_host']->value ?? '' }}" class="input-field">
                            </div>
                            <div style="display: flex; gap: 1rem;">
                                <div style="flex: 1;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('SMTP Port') }}</label>
                                    <input type="text" name="smtp_port" value="{{ $settings['smtp_port']->value ?? '587' }}" class="input-field">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; font-size: 0.875rem; font-weight: 500; margin-bottom: 0.5rem;">{{ __('SMTP Encryption') }}</label>
                                    <input type="text" name="smtp_encryption" value="{{ $settings['smtp_encryption']->value ?? 'tls' }}" class="input-field">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">{{ __('Save Settings') }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
