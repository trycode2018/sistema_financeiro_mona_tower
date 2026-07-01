<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Faturas</h1>
                <p class="text-gray-600">Lista de todas as faturas</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark flex items-center space-x-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-semibold">Nova Fatura</span>
            </a>
        </div>
    </x-slot>

    <div class="mb-6 p-4 bg-white border border-gray-200 rounded-xl">
        <form action="{{ route('invoices.mass-action') }}" method="POST">
            @csrf
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Esquerda: Toggle de Ativação -->
            <div class="flex items-center space-x-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="status_toggle" value="1" 
                        {{ $statusAtivo ? 'checked' : '' }} 
                        onchange="this.form.submit()"
                        class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
                <span class="text-sm font-medium text-gray-700">
                    Faturamento Automático: 
                    <span class="font-semibold {{ $statusAtivo ? 'text-green-600' : 'text-red-500' }}">
                        {{ $statusAtivo ? 'ATIVADO' : 'DESATIVADO' }}
                    </span>
                </span>
            </div>

            <!-- Direita: Botões de Ação -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- Botão: Gerar Mensalidades -->
                <button type="submit" name="processar_agora" value="1"
                        class="inline-flex items-center px-4 py-2 bg-school-primary text-white text-sm font-bold rounded-lg hover:bg-school-dark transition shadow-sm"
                        title="Gerar novas mensalidades do mês atual">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    GERAR MENSALIDADES DE {{ now()->translatedFormat('F') }}
                </button>

                <!-- Botão: Enviar Notificações -->
                <button type="submit" name="enviar_notificacoes" value="1" class="inline-flex items-center px-4 py-2 bg-school-primary text-white text-sm font-bold rounded-lg hover:bg-school-dark transition shadow-sm"
                        title="Enviar notificações para os estudantes com faturas pendentes ou vencidas">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    ENVIAR NOTIFICAÇÕES AGORA
                </button>
            </div>
        </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <!-- Filtros -->
            <div class="mb-6 flex gap-4 flex-wrap">
                <select id="status_filter" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                    <option value="">Todos os Status</option>
                    <option value="pendente">Pendente</option>
                    <option value="aguardando_confirmacao">A Aguardar Confirmação</option>
                    <option value="pago">Paga</option>
                    <option value="vencido">Vencida</option>
                    <option value="parcial">Parcial</option>
                    <option value="rejeitado">Rejeitado</option>
                </select>
                <input type="date" id="date_from" placeholder="Data inicial" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                <input type="date" id="date_to" placeholder="Data final" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                <button id="filter_btn" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Filtrar
                </button>
                <button id="clear_filter" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                    Limpar
                </button>
            </div>

            <!-- Tabela de Faturas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                        @php
                            $hasPendingPayment = $invoice->payments->contains('status', 'pending');
                            $totalConfirmed = $invoice->payments->where('status', 'confirmed')->sum('amount');
                            $realBalance = $invoice->total_amount - $totalConfirmed;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invoice->student->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                Kz {{ number_format($invoice->total_amount, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                Kz {{ number_format($invoice->amount_paid, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'pago' => 'bg-green-100 text-green-800',
                                        'aguardando_confirmacao' => 'bg-purple-100 text-purple-800',
                                        'vencido' => 'bg-red-100 text-red-800',
                                        'parcial' => 'bg-blue-100 text-blue-800',
                                        'pendente' => 'bg-yellow-100 text-yellow-800',
                                        'rejeitado' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'pago' => 'Paga',
                                        'aguardando_confirmacao' => 'A Aguardar Confirmação',
                                        'vencido' => 'Vencida',
                                        'parcial' => 'Parcial',
                                        'pendente' => 'Pendente',
                                        'rejeitado' => 'Rejeitado',
                                    ];
                                @endphp
                                <span class="px-2 py-1 inline-flex text-[11px] leading-5 font-bold rounded-full uppercase {{ $statusClasses[$invoice->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900" title="Ver Detalhes">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    <!-- Botão Pagar - mostra apenas se houver saldo real e não houver pendente -->
                                    @if($realBalance > 0 && $invoice->status !== 'pago' && $invoice->status !== 'aguardando_confirmacao' && !$hasPendingPayment)
                                        <a href="{{ route('invoices.payments.create', $invoice) }}" class="text-green-600 hover:text-green-900" title="Registrar Pagamento">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </a>
                                    @endif

                                    @if($invoice->status !== 'pago')
                                        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza que deseja eliminar esta fatura?')" title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="mt-6">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

<script>
document.getElementById('filter_btn')?.addEventListener('click', function() {
    let status = document.getElementById('status_filter').value;
    let dateFrom = document.getElementById('date_from').value;
    let dateTo = document.getElementById('date_to').value;
    
    let url = new URL(window.location.href);
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (dateFrom) url.searchParams.set('date_from', dateFrom);
    else url.searchParams.delete('date_from');
    if (dateTo) url.searchParams.set('date_to', dateTo);
    else url.searchParams.delete('date_to');
    
    window.location.href = url.toString();
});

document.getElementById('clear_filter')?.addEventListener('click', function() {
    window.location.href = window.location.pathname;
});
</script>