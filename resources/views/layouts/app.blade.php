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
    <div x-data="{ 
        sidebarOpen: window.innerWidth >= 1024, 
        collapsed: window.innerWidth >= 1024,
        init() {
            // Recuperar preferência do localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState !== null) {
                this.collapsed = savedState === 'true';
            }
            
            // Ajustar para mobile
            if (window.innerWidth < 1024) {
                this.sidebarOpen = false;
                this.collapsed = false;
            }
            
            // Listener para redimensionamento
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                } else {
                    this.sidebarOpen = false;
                    this.collapsed = false;
                }
            });
            
            // Salvar preferência do usuário
            this.$watch('collapsed', value => {
                if (window.innerWidth >= 1024) {
                    localStorage.setItem('sidebarCollapsed', value);
                }
            });
        },
        toggleSidebar() {
            this.collapsed = !this.collapsed;
        }
    }" @keydown.escape.window="sidebarOpen = false">
        
        <!-- Overlay escuro para mobile -->
        <div x-show="sidebarOpen && window.innerWidth < 1024" 
             @click="sidebarOpen = false" 
             class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" 
             x-transition.opacity>
        </div>
        
        <div class="flex min-h-screen">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Content -->
            <div class="flex-1 flex flex-col transition-all duration-300"
                 :style="window.innerWidth >= 1024 ? 'margin-left: ' + (collapsed ? '5rem' : '16rem') : ''">
                <!-- Header -->
                @include('layouts.header')

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">
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
                            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ session('success') }}
                                </div>
                            </div>
                        @endif

                        <!-- Error Messages -->
                        @if (session('error'))
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ session('error') }}
                                </div>
                            </div>
                        @endif

                        <!-- Warning Messages -->
                        @if (session('warning'))
                            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg shadow-sm">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ session('warning') }}
                                </div>
                            </div>
                        @endif

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    <div>
                                        <p class="font-medium mb-1">Por favor, corrija os seguintes erros:</p>
                                        <ul class="list-disc list-inside text-sm">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
                
                <!-- Footer opcional -->
                @hasSection('footer')
                    <footer class="bg-white border-t border-gray-200 py-4">
                        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                            @yield('footer')
                        </div>
                    </footer>
                @endif
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>