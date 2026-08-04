<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Sistema de Papeletas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        window.VAPID_PUBLIC_KEY = "{{ config('webpush.vapid.public_key') }}";
    </script>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-white shadow px-4 py-3 flex justify-between items-center">
        <a href="{{ route('papeletas.index') }}" class="font-bold text-lg text-blue-700">Papeletas</a>
        <div class="flex items-center gap-3 text-sm">
            <button onclick="activarNotificaciones()" class="text-blue-600 hidden sm:inline">🔔 Activar</button>
            <span class="hidden sm:inline text-gray-600">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-red-600">Salir</button>
            </form>
        </div>
    </nav>

    <main class="max-w-3xl mx-auto p-4">
        @if (session('status'))
            <div class="bg-green-100 text-green-800 text-sm p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 text-sm p-3 rounded mb-4">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('contenido')
    </main>

    <script src="{{ asset('js/push.js') }}"></script>
</body>
</html>
