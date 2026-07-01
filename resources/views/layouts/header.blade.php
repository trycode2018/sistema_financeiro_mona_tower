<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between px-6 py-4">

        <!-- TÍTULO -->
        <div class="flex items-center space-x-4">
            <h1 class="text-xl font-semibold text-gray-900">
                @yield('page-title', 'Sistema de Gestão')
            </h1>
        </div>

        <div class="flex items-center space-x-4">
            <!-- 🔔 NOTIFICAÇÕES -->
            <div class="relative">

                <!-- BOTÃO (mantido original) -->
                <button onclick="toggleNotifications()" class="p-2 text-gray-600 hover:text-school-primary rounded-lg hover:bg-gray-100 relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 0-6 6v2.25l-2.47 2.47a.75.75 0 0 0 .53 1.28h15.88a.75.75 0 0 0 .53-1.28L16.5 12V9.75a6 6 0 0 0-6-6z"/>
                    </svg>

                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <!-- DROPDOWN REDUZIDO E OTIMIZADO -->
                <div id="notificationDropdown"
                    class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">

                    <!-- HEADER REDUZIDO -->
                    <div class="p-2.5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-lg">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 0-6 6v2.25l-2.47 2.47a.75.75 0 0 0 .53 1.28h15.88a.75.75 0 0 0 .53-1.28L16.5 12V9.75a6 6 0 0 0-6-6z"/>
                            </svg>
                            <span class="font-semibold text-sm text-gray-700">Notificações</span>
                            @php
                                $totalCount = auth()->user()->notifications->count();
                            @endphp
                            @if($totalCount > 0)
                                <span class="text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-full">
                                    {{ $totalCount }}
                                </span>
                            @endif
                        </div>

                        <form method="POST" action="/notifications/read">
                            @csrf
                            <button class="text-[10px] text-blue-600 hover:text-blue-700 font-medium hover:bg-blue-50 px-2 py-1 rounded transition-all">
                                Marcar todas
                            </button>
                        </form>
                    </div>

                    <!-- LISTA REDUZIDA COM ALTURA LIMITADA -->
                    <div class="max-h-80 overflow-y-auto">

                        @forelse(auth()->user()->notifications->take(5) as $notification)
                            @php
                                // Tenta buscar o número da fatura se existir invoice_id
                                $invoiceNumber = null;
                                if(isset($notification->data['invoice_id'])) {
                                    $invoice = \App\Models\Invoice::find($notification->data['invoice_id']);
                                    $invoiceNumber = $invoice ? $invoice->invoice_number : null;
                                }
                                
                                // Pega o invoice_number direto se existir
                                if(isset($notification->data['invoice_number'])) {
                                    $invoiceNumber = $notification->data['invoice_number'];
                                }
                                
                                // Limita o tamanho do título
                                $title = $notification->data['title'] ?? 'Notificação';
                                if(strlen($title) > 35) {
                                    $title = substr($title, 0, 32) . '...';
                                }
                            @endphp
                            
                            <div class="p-2.5 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer
                                {{ is_null($notification->read_at) ? 'bg-blue-50/50' : '' }}">

                                <div class="flex items-start gap-2">
                                    <!-- Ícone pequeno -->
                                    <div class="flex-shrink-0 mt-0.5">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center
                                            {{ is_null($notification->read_at) ? 'bg-blue-100' : 'bg-gray-100' }}">
                                            <svg class="w-3.5 h-3.5 {{ is_null($notification->read_at) ? 'text-blue-600' : 'text-gray-500' }}" 
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                    d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 0-6 6v2.25l-2.47 2.47a.75.75 0 0 0 .53 1.28h15.88a.75.75 0 0 0 .53-1.28L16.5 12V9.75a6 6 0 0 0-6-6z"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-1">
                                            <strong class="text-xs font-semibold text-gray-800 line-clamp-1">
                                                {{ $title }}
                                            </strong>
                                            <span class="text-[9px] text-gray-400 whitespace-nowrap">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <p class="text-[10px] text-gray-600 mt-0.5 line-clamp-2">
                                            @if($invoiceNumber)
                                                Fatura #{{ $invoiceNumber }}
                                            @else
                                                {{ Str::limit($notification->data['message'] ?? '', 60) }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-8 px-4 text-center">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 text-xs">Sem notificações</p>
                            </div>
                        @endforelse

                    </div>

                    <!-- FOOTER OPCIONAL -->
                    @if(auth()->user()->notifications->count() > 5)
                        <div class="p-2 border-t border-gray-100 text-center bg-gray-50 rounded-b-lg">
                            <a href="{{ route('notifications.index') }}" class="text-[10px] text-blue-600 hover:text-blue-700 font-medium">
                                Ver todas ({{ auth()->user()->notifications->count() }}) →
                            </a>
                        </div>
                    @endif

                </div>
            </div>

            <!-- 👤 USER MENU -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                        class="flex items-center space-x-3 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-school-primary focus:ring-offset-2">

                    <div class="w-8 h-8 bg-school-primary rounded-full flex items-center justify-center">
                        <span class="text-sm font-semibold text-white">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>

                    <div class="hidden md:block text-left">
                        <p class="font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                    </div>

                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- DROPDOWN USER -->
                <div x-show="open" @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">

                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center space-x-2">
                        <span>Meu Perfil</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Terminar Sessão
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>
<script>
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function (event) {
    const dropdown = document.getElementById('notificationDropdown');

    if (!event.target.closest('#notificationDropdown') &&
        !event.target.closest('button')) {
        dropdown.classList.add('hidden');
    }
});
</script>