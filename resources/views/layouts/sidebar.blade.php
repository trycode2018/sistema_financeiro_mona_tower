<aside class="w-64 bg-school-primary text-white flex flex-col">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-school-dark px-4">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-school-accent rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l9 5m-9-5v10"/>
                </svg>
            </div>
            <span class="text-xl font-bold">Mona Tower</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('students.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('students.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <span>Estudantes</span>
        </a>

        <a href="{{ route('invoices.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Faturas</span>
        </a>

        <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('payments.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            <span>Pagamentos</span>
        </a>

        <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('users.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <span>Utilizadores</span>
        </a>

        <a href="{{ route('reports.financial') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-school-secondary text-white' : 'text-school-light hover:bg-school-secondary' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Relatórios</span>
        </a>
    </nav>

    <!-- User Info -->
    <div class="p-4 border-t border-school-secondary">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-school-accent rounded-full flex items-center justify-center">
                <span class="text-sm font-semibold text-white">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-school-light truncate">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>
</aside>