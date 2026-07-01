<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Notificações</h1>
                <p class="text-gray-600">Histórico de todas as notificações</p>
            </div>
            
            <form method="POST" action="/notifications/read" class="flex space-x-2">
                @csrf
                <a href="{{ route('dashboard') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Voltar
                </a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center space-x-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Marcar Todas como Lidas</span>
                </button>
            </form>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @php
            $allNotifications = auth()->user()->notifications()->orderBy('created_at', 'desc')->get();
            $totalNotifications = $allNotifications->count();
        @endphp
        
        @if($totalNotifications > 0)
            <div class="divide-y divide-gray-200">
                @foreach($allNotifications as $notification)
                    @php
                        $invoiceNumber = null;
                        if(isset($notification->data['invoice_id'])) {
                            $invoice = \App\Models\Invoice::find($notification->data['invoice_id']);
                            $invoiceNumber = $invoice ? $invoice->invoice_number : null;
                        }
                        
                        if(isset($notification->data['invoice_number'])) {
                            $invoiceNumber = $notification->data['invoice_number'];
                        }
                        
                        $icon = 'bell';
                        $color = 'blue';
                        
                        if(isset($notification->data['type'])) {
                            switch($notification->data['type']) {
                                case 'payment':
                                    $icon = 'currency-dollar';
                                    $color = 'green';
                                    break;
                                case 'invoice':
                                    $icon = 'document-text';
                                    $color = 'yellow';
                                    break;
                                case 'student':
                                    $icon = 'academic-cap';
                                    $color = 'purple';
                                    break;
                                case 'warning':
                                    $icon = 'exclamation';
                                    $color = 'red';
                                    break;
                            }
                        }
                    @endphp
                    
                    <div class="p-6 hover:bg-gray-50 transition-colors {{ is_null($notification->read_at) ? 'bg-blue-50/30 border-l-4 border-blue-500' : '' }}">
                        <div class="flex items-start space-x-4">
                            <!-- Ícone -->
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center bg-{{ $color }}-100">
                                    @if($icon === 'currency-dollar')
                                        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                    @elseif($icon === 'document-text')
                                        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @elseif($icon === 'academic-cap')
                                        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                                            <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                        </svg>
                                    @elseif($icon === 'exclamation')
                                        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.75a6 6 0 0 0-6 6v2.25l-2.47 2.47a.75.75 0 0 0 .53 1.28h15.88a.75.75 0 0 0 .53-1.28L16.5 12V9.75a6 6 0 0 0-6-6z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Conteúdo -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900">
                                            {{ $notification->data['title'] ?? 'Notificação' }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        @if($invoiceNumber)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Fatura #{{ $invoiceNumber }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-3 ml-4">
                                        <span class="text-xs text-gray-400 whitespace-nowrap">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if(is_null($notification->read_at))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Nova
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if(isset($notification->data['action_url']))
                                    <div class="mt-3">
                                        <a href="{{ $notification->data['action_url'] }}" 
                                           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                            {{ $notification->data['action_text'] ?? 'Ver detalhes' }}
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma notificação</h3>
                <p class="text-gray-500">Você não possui notificações no momento.</p>
            </div>
        @endif
    </div>
</x-app-layout>