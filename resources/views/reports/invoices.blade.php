<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Relatório de Faturas</h1>
                <p class="text-gray-600">Análise e estatísticas das faturas</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
            <form method="GET" action="{{ route('reports.invoices') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                            <option value="">Todos os Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagas</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Vencidas</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-school-primary text-white px-4 py-3 rounded-lg hover:bg-school-dark transition">
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Total de Faturas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalInvoices }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Valor Total Emitido</p>
                    <p class="text-2xl font-semibold text-gray-900">Kz {{ number_format($totalBilled, 2, ',', ' ') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Valor Pendente</p>
                    <p class="text-2xl font-semibold text-yellow-600">Kz {{ number_format($totalPending, 2, ',', ' ') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Valor Vencido</p>
                    <p class="text-2xl font-semibold text-red-600">Kz {{ number_format($totalOverdue, 2, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <!-- Distribuição por Status -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição por Status</h3>
                <div class="space-y-4">
                    @php
                        $statusColors = [
                            'paid' => 'bg-green-100 text-green-800',
                            'pending' => 'bg-yellow-100 text-yellow-800', 
                            'overdue' => 'bg-red-100 text-red-800'
                        ];
                        
                        $statusLabels = [
                            'paid' => 'Pagas',
                            'pending' => 'Pendentes',
                            'overdue' => 'Vencidas'
                        ];
                    @endphp
                    
                    @foreach($statusDistribution as $status => $count)
                    <div class="flex items-center justify-between">
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$status] }}">
                            {{ $statusLabels[$status] }}
                        </span>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                            <span class="text-sm text-gray-500">
                                ({{ $totalInvoices > 0 ? number_format(($count / $totalInvoices) * 100, 1) : 0 }}%)
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo Financeiro</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Valor Total Emitido:</span>
                        <span class="text-sm font-medium">Kz {{ number_format($totalBilled, 2, ',', ' ') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Valor Total Recebido:</span>
                        <span class="text-sm font-medium text-green-600">Kz {{ number_format($totalPaid, 2, ',', ' ') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Valor Pendente:</span>
                        <span class="text-sm font-medium text-yellow-600">Kz {{ number_format($totalPending, 2, ',', ' ') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Valor Vencido:</span>
                        <span class="text-sm font-medium text-red-600">Kz {{ number_format($totalOverdue, 2, ',', ' ') }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-900">Taxa de Cobrança:</span>
                            <span class="text-sm font-medium text-green-600">
                                {{ $totalBilled > 0 ? number_format(($totalPaid / $totalBilled) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Faturas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Lista de Faturas</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fatura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estudante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Venc.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor Pago
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Saldo
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $invoice->student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Kz {{ number_format($invoice->total_amount, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                Kz {{ number_format($invoice->amount_paid, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                Kz {{ number_format($invoice->balance, 2, ',', ' ') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($invoices->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2">Nenhuma fatura encontrada com os filtros aplicados.</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>