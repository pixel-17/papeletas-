<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistema de Papeletas') }}</title>

    {{-- PWA: instalable en el celular/escritorio --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3b6cf6">
    <link rel="icon" href="/icons/icon-192.png">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Papeletas">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <script>
        // Se aplica ANTES de pintar la página para evitar el parpadeo de tema
        // (flash of wrong theme) al navegar entre páginas server-rendered.
        (function () {
            const guardado = localStorage.getItem('tema');
            const prefiereOscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (guardado === 'oscuro' || (!guardado && prefiereOscuro)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <script>
        window.VAPID_PUBLIC_KEY = "{{ config('webpush.vapid.public_key') }}";

        // Se registra siempre (no solo al activar notificaciones): es lo que
        // hace que el navegador ofrezca "Instalar app". El sw.js sigue
        // manejando el push por separado una vez que el usuario lo active.
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js'));
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-bg font-sans antialiased text-gray-800">

    <x-notification-toast />

    @include('layouts.navigation')

    @if (isset($header))
        <header class="relative z-10">
            <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6">
                {{ $header }}
            </div>
        </header>
    @endif

    <main class="relative z-10 max-w-5xl mx-auto p-4 pb-16">
        @if (session('status'))
            <div class="glass-card border-l-4 !border-l-emerald-400 text-emerald-700 text-sm p-4 mb-4 animate-fade-in-up flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="glass-card border-l-4 !border-l-rose-400 text-rose-700 text-sm p-4 mb-4 animate-fade-in-up">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{ $slot }}
    </main>

    <script src="{{ asset('js/push.js') }}"></script>
</body>
</html>
