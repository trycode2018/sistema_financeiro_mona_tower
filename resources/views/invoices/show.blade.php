<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Fatura #{{ $invoice->invoice_number }}</h1>
                <p class="text-gray-600">Detalhes da fatura</p>
            </div>
            <div class="flex space-x-2">
                <!-- Botão de Editar -->
                <a href="{{ route('invoices.edit', $invoice) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Editar</span>
                </a>
                
                <!-- Botão de Pagamento - só mostra se houver saldo -->
                @if($invoice->balance > 0)
                <div class="flex space-x-2">
                    <a href="{{ route('invoices.payments.create', $invoice) }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                        <span>Registrar Pagamento</span>
                    </a>
                    
                    <!-- Botão de Pagamento Rápido -->
                    <form action="{{ route('invoices.payments.full', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center space-x-2" 
                                onclick="return confirm('Deseja registrar o pagamento total de Kz {{ number_format($invoice->balance, 2, ',', ' ') }}?')">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="bg-orange-100 rounded-lg">Pagar Tudo (Kz {{ number_format($invoice->balance, 2, ',', ' ') }})</span>
                        </button>
                    </form>
                </div>
                @else
                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Fatura Paga</span>
                </span>
                @endif
                
                <!-- Botão Eliminar -->
                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition flex items-center space-x-2" 
                            onclick="return confirm('Tem certeza que deseja eliminar esta fatura?')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Eliminar</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações da Fatura -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações da Fatura</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Número da Fatura</p>
                    <p class="font-medium">#{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estudante</p>
                    <p class="font-medium">{{ $invoice->student->name ?? 'Estudante não encontrado' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data de Emissão</p>
                    <p class="font-medium">{{ $invoice->issue_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data de Vencimento</p>
                    <p class="font-medium">{{ $invoice->due_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-medium">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Valor Total</p>
                    <p class="font-medium">Kz {{ number_format($invoice->total_amount, 2, ',', ' ') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Valor Pago</p>
                    <p class="font-medium">Kz {{ number_format($invoice->amount_paid, 2, ',', ' ') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saldo</p>
                    <p class="font-medium">Kz {{ number_format($invoice->balance, 2, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <!-- Descrição -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrição</h2>
            <p class="text-gray-700">{{ $invoice->description }}</p>
        </div>

        <!-- Pagamentos -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Pagamentos</h2>
                @if($invoice->balance > 0)
                <a href="{{ route('invoices.payments.create', $invoice) }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition text-sm">
                    Adicionar Pagamento
                </a>
                @endif
            </div>
            <div class="space-y-3">
                @forelse($invoice->payments as $payment)
                <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium">Kz {{ number_format($payment->amount, 2, ',', ' ') }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->payment_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 capitalize">{{ $payment->payment_method }}</p>
                        <p class="text-sm text-gray-600">{{ $payment->reference ?? 'N/A' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">Nenhum pagamento encontrado.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>