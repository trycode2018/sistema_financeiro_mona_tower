<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Sistema de Gestão - Colégio Mona Tower')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            @include('layouts.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                <div class="container mx-auto px-6 py-8">
                    @if (isset($header))
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $header }}</h1>
                        </div>
                    @endif

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Success Messages -->
                    @if (session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if (session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>