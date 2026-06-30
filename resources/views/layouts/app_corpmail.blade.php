<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Reverb Broadcasting Meta configs -->
    <meta name="reverb-app-key" content="{{ env('REVERB_APP_KEY') }}">
    <meta name="reverb-host" content="{{ env('REVERB_HOST', '127.0.0.1') }}">
    <meta name="reverb-port" content="{{ env('REVERB_PORT', '8080') }}">
    <meta name="reverb-scheme" content="{{ env('REVERB_SCHEME', 'http') }}">

    <title>@yield('title', setting('app_name', 'Messagerie Internal & External'))</title>

    @if(setting('app_favicon'))
    <link rel="icon" href="{{ setting('app_favicon') }}">
    @endif

    <!-- Public Local Stylesheet -->
    <link rel="stylesheet" href="/css/app.css">
    
    <style>
        :root {
            --primary: {{ setting('primary_color', '#3b82f6') }};
        }
    </style>

    <!-- Dependencies via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>

    <!-- Configure Global Axios & Laravel Echo Reverb Client -->
    <script>
        window.axios = axios;
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        window.axios.defaults.withCredentials = true;

        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }

        const reverbAppKey = document.querySelector('meta[name="reverb-app-key"]')?.content || '';
        const reverbHost = document.querySelector('meta[name="reverb-host"]')?.content || window.location.hostname;
        const reverbPort = document.querySelector('meta[name="reverb-port"]')?.content || '8080';
        const reverbScheme = document.querySelector('meta[name="reverb-scheme"]')?.content || 'http';

        if (window.Echo === undefined && typeof Echo !== 'undefined') {
            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: reverbAppKey,
                wsHost: reverbHost,
                wsPort: parseInt(reverbPort),
                wssPort: parseInt(reverbPort),
                forceTLS: reverbScheme === 'https',
                enabledTransports: ['ws', 'wss'],
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': token ? token.content : '',
                    }
                }
            });
        }
    </script>

    <!-- Main JS logic (Only load for authenticated users) -->
    @auth
        <script src="/js/chat.js" defer></script>
    @endauth
</head>
<body>
    @yield('content')
</body>
</html>
