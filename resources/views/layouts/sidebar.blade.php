<aside 
    :class="collapsed ? 'w-20' : 'w-64'"
    class="fixed left-0 top-0 h-full bg-school-primary text-white flex flex-col transition-all duration-300 z-50 transform lg:translate-x-0"
    :class="{ '-translate-x-full lg:translate-x-0': !sidebarOpen }"
>
    <!-- Logo -->
    <div class="flex items-center h-16 bg-school-dark px-4" :class="collapsed ? 'justify-center' : 'justify-between'">
        <div class="flex items-center space-x-3">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl">
                <img src="{{ asset('images/MonaTower.png') }}" alt="Logo da Mona Tower" class="w-full h-full object-contain drop-shadow-sm">
            </div>
            <span x-show="!collapsed" class="text-xl font-bold whitespace-nowrap">Mona Tower</span>
        </div>
        <!-- Botão de recolher/expandir para desktop -->
        <button @click="toggleSidebar()" class="hidden lg:block text-school-light hover:text-white transition-colors flex-shrink-0">
            <svg x-show="!collapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="collapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-2 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('dashboard') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Dashboard</span>
            <!-- Tooltip quando recolhido -->
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Dashboard
            </div>
        </a>

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretaria')
        <!-- Estudantes -->
        <a href="{{ route('students.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('students.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Estudantes</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Estudantes
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretaria')
        <!-- Encarregados -->
        <a href="{{ route('guardians.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('guardians.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Encarregados</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Encarregados
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretaria')
        <!-- Serviços -->
        <a href="{{ route('services.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('services.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Serviços</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Serviços
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'secretaria')
        <!-- Faturas -->
        <a href="{{ route('invoices.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('invoices.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Faturas</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Faturas
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'financeiro')
        <!-- Pagamentos -->
        <a href="{{ route('payments.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('payments.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Pagamentos</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Pagamentos
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'financeiro')
        <!-- Relatórios com submenu -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="flex items-center justify-between w-full px-3 py-3 rounded-lg transition-colors duration-200 group relative
                           {{ request()->routeIs('reports.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
                    :class="collapsed ? 'justify-center' : ''">
                <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="!collapsed" class="whitespace-nowrap">Relatórios</span>
                </div>
                <svg x-show="!collapsed" class="w-4 h-4 transition-transform flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <!-- Tooltip quando recolhido -->
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 top-3 hidden lg:block">
                Relatórios
            </div>

            <!-- Submenu - sempre alinhado -->
            <div x-show="open && !collapsed" 
                 class="pl-4 mt-2 space-y-1">
                <a href="{{ route('reports.financial') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('reports.financial') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    <span class="text-sm">Financeiro</span>
                </a>
                <a href="{{ route('reports.students') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('reports.students') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm">Estudantes</span>
                </a>
                <a href="{{ route('reports.invoices') }}" 
                   class="flex items-center space-x-3 px-4 py-2 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('reports.invoices') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm">Faturas</span>
                </a>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'admin')
        <!-- Utilizadores -->
        <a href="{{ route('users.index') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('users.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Utilizadores</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Utilizadores
            </div>
        </a>
        @endif

        @if(auth()->user()->role === 'admin')
        <!-- Auditoria -->
        <a href="{{ route('audit-logs') }}" 
           class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group relative
                  {{ request()->routeIs('audit-logs') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}"
           :class="collapsed ? 'justify-center' : 'space-x-3'">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span x-show="!collapsed" class="whitespace-nowrap">Registos de Auditoria</span>
            <div x-show="collapsed" 
                 class="absolute left-full ml-2 px-2 py-1 bg-gray-900 text-white text-sm rounded opacity-0 group-hover:opacity-100 whitespace-nowrap pointer-events-none transition-opacity z-50 hidden lg:block">
                Registos de Auditoria
            </div>
        </a>
        @endif
    </nav>

    <!-- User Info -->
    <div class="p-3 border-t border-school-secondary">
        <div class="flex items-center" :class="collapsed ? 'justify-center' : 'space-x-3'">
            <div class="w-8 h-8 bg-school-accent rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-sm font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div x-show="!collapsed" class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-school-light truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>