<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Fatura #{{ $invoice->invoice_number }}
                </h1>

                <p class="text-gray-600">
                    Detalhes completos da fatura
                </p>
            </div>

            <div class="flex space-x-2">

                {{-- Editar - Só permite editar se não estiver paga ou com pagamento pendente --}}
                @if(!in_array($invoice->status, ['pago', 'aguardando_confirmacao']))
                    <a href="{{ route('invoices.edit', $invoice) }}"
                       class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition flex items-center space-x-2">
                        <span>Editar</span>
                    </a>
                @endif

                {{-- Pagamento - Mostrar apenas se houver saldo real --}}
                @php
                    $totalConfirmed = $invoice->payments->where('status', 'confirmed')->sum('amount');
                    $realBalance = $invoice->total_amount - $totalConfirmed;
                    $hasPendingPayment = $invoice->payments->contains('status', 'pending');
                @endphp

                @if($realBalance > 0)
                    @if(!$hasPendingPayment && $invoice->status !== 'pago')
                        <a href="{{ route('invoices.payments.create', $invoice) }}"
                           class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition flex items-center space-x-2">
                            <span>Registrar Pagamento</span>
                        </a>
                    @elseif($hasPendingPayment)
                        <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-lg flex items-center">
                            ⏳ Pagamento Aguardando Confirmação
                        </span>
                    @endif
                @else
                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg flex items-center">
                        ✓ Fatura Paga
                    </span>
                @endif

                {{-- Eliminar - Só permite eliminar se não estiver paga --}}
                @if($invoice->status !== 'pago')
                    <form action="{{ route('invoices.destroy', $invoice) }}"
                          method="POST"
                          onsubmit="return confirm('Eliminar esta fatura?')">
                        @csrf
                        @method('DELETE')
                        <button class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                            Eliminar
                        </button>
                    </form>
                @endif

            </div>

        </div>
    </x-slot>

    {{-- GRID PRINCIPAL --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- INFORMAÇÕES --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

            <h2 class="text-lg font-semibold mb-4">
                Informações da Fatura
            </h2>

            <div class="space-y-3">

                <div>
                    <p class="text-sm text-gray-500">Estudante</p>
                    <p class="font-medium">
                        {{ $invoice->student->name ?? 'N/A' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Emissão</p>
                    <p class="font-medium">
                        {{ $invoice->issue_date->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Vencimento</p>
                    <p class="font-medium">
                        {{ $invoice->due_date->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Status</p>

                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                        {{ $invoice->status === 'pago' ? 'bg-green-100 text-green-800' :
                        ($invoice->status === 'vencido' ? 'bg-red-100 text-red-800' :
                        ($invoice->status === 'aguardando_confirmacao' ? 'bg-purple-100 text-purple-800' :
                        ($invoice->status === 'parcial' ? 'bg-blue-100 text-blue-800' :
                        ($invoice->status === 'rejeitado' ? 'bg-gray-100 text-gray-800' :
                        'bg-yellow-100 text-yellow-800')))) }}">
                        
                        {{ $invoice->status === 'aguardando_confirmacao' ? 'A Aguardar Confirmação' : 
                        ($invoice->status === 'vencido' ? 'Vencida' :
                        ($invoice->status === 'parcial' ? 'Parcial' :
                        ($invoice->status === 'rejeitado' ? 'Rejeitado' :
                        ucfirst($invoice->status)))) }}
                    </span>

                </div>

            </div>

        </div>

        {{-- DESCRIÇÃO --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

            <h2 class="text-lg font-semibold mb-4">
                Descrição
            </h2>

            <p class="text-gray-700">
                {{ $invoice->description }}
            </p>

        </div>

        {{-- RESUMO FINANCEIRO --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

            <h2 class="text-lg font-semibold mb-4">
                Resumo Financeiro
            </h2>

            <div class="space-y-3">

                <div>
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="font-bold text-lg">
                        Kz {{ number_format($invoice->total_amount, 2, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Pago (Confirmado)</p>
                    <p class="font-medium text-green-600">
                        Kz {{ number_format($invoice->amount_paid, 2, ',', '.') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Saldo</p>
                    <p class="font-bold text-red-600">
                        Kz {{ number_format($realBalance, 2, ',', '.') }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    {{-- ========================= --}}
    {{-- ITENS DA FATURA --}}
    {{-- ========================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">

        <div class="flex justify-between items-center mb-6">

            <h2 class="text-lg font-semibold">
                Itens da Fatura
            </h2>

            <div class="text-sm text-gray-500">
                Detalhamento completo
            </div>

        </div>

        <div class="space-y-4">

            @forelse($invoice->items as $item)

                <div class="flex justify-between items-center border-b pb-3">

                    <div>

                        <div class="font-medium text-gray-900">
                            {{ $item->description }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ ucfirst($item->type) }}
                        </div>

                    </div>

                    <div class="font-bold text-school-primary">
                        Kz {{ number_format($item->amount, 2, ',', '.') }}
                    </div>

                </div>

            @empty

                <p class="text-gray-500">
                    Nenhum item encontrado nesta fatura.
                </p>

            @endforelse

        </div>

    </div>

    {{-- ========================= --}}
    {{-- PAGAMENTOS --}}
    {{-- ========================= --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-lg font-semibold">
                Pagamentos
            </h2>

            @if($realBalance > 0 && $invoice->status !== 'pago' && !$hasPendingPayment)
                <a href="{{ route('invoices.payments.create', $invoice) }}"
                   class="bg-school-primary text-white px-4 py-2 rounded-lg text-sm hover:bg-school-dark transition">
                    + Adicionar Pagamento
                </a>
            @endif

        </div>

        

        <div class="space-y-3">

            @forelse($invoice->payments as $payment)

                <div class="flex justify-between items-center p-3 border rounded-lg {{ $payment->status === 'pending' ? 'bg-yellow-50 border-yellow-300' : ($payment->status === 'confirmed' ? 'bg-green-50 border-green-300' : 'bg-gray-50 border-gray-300') }}">

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-lg">
                                Kz {{ number_format($payment->amount, 2, ',', '.') }}
                            </p>
                            <span class="text-xs px-2 py-0.5 rounded-full 
                                {{ $payment->status === 'confirmed' ? 'bg-green-200 text-green-800' : 
                                   ($payment->status === 'pending' ? 'bg-yellow-200 text-yellow-800' : 
                                   'bg-red-200 text-red-800') }}">
                                {{ $payment->status === 'confirmed' ? 'Confirmado' : 
                                   ($payment->status === 'pending' ? 'Pendente' : 'Rejeitado') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $payment->payment_date->format('d/m/Y') }} • {{ ucfirst($payment->payment_method) }}
                        </p>
                        @if($payment->reference)
                            <p class="text-xs text-gray-400">Ref: {{ $payment->reference }}</p>
                        @endif
                        @if($payment->notes)
                            <p class="text-xs text-gray-500 mt-1">{{ $payment->notes }}</p>
                        @endif
                    </div>
                </div>

            @empty

                <p class="text-gray-500 text-center py-4">
                    Nenhum pagamento registrado.
                </p>

            @endforelse

        </div>

    </div>

    {{-- VOLTAR --}}
    <div class="mt-6">

        <a href="{{ route('invoices.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900">

            ← Voltar à lista

        </a>

    </div>

</x-app-layout>