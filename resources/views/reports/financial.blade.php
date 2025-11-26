<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Relatórios Financeiros</h1>
                <p class="text-gray-600">Análise financeira e estatísticas</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>
            <form method="GET" action="{{ route('reports.financial') }}">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') ?? $startDate->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') ?? $endDate->format('Y-m-d') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    </div>
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Estudante</label>
                        <select name="student_id" id="student_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                            <option value="">Todos os Estudantes</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
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
                    <p class="text-sm font-medium text-gray-600">Receita Total</p>
                    <p class="text-2xl font-semibold text-gray-900">Kz {{ number_format($totalRevenue, 2, ',', ' ') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Pagamentos Recebidos</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalPayments }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Faturas Emitidas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $totalInvoices }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="text-center">
                    <p class="text-sm font-medium text-gray-600">Taxa de Pagamento</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($paymentRate, 1) }}%</p>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Receita por Mês</h3>
                @if(!empty($revenueChart['data']))
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                @else
                    <p class="text-gray-500 text-center py-8">Nenhum dado disponível para o período selecionado.</p>
                @endif
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição de Pagamentos</h3>
                @if(!empty($paymentMethodChart['data']))
                    <canvas id="paymentMethodChart" width="400" height="200"></canvas>
                @else
                    <p class="text-gray-500 text-center py-8">Nenhum dado disponível para o período selecionado.</p>
                @endif
            </div>
        </div>

        <!-- Tabela de Pagamentos Recentes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pagamentos Recentes</h3>
            </div>
            <div class="overflow-x-auto">
                @if($recentPayments->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estudante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fatura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentPayments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->invoice->student->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                #{{ $payment->invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                {{ $payment->payment_method }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Kz {{ number_format($payment->amount, 2, ',', ' ') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2">Nenhum pagamento encontrado no período selecionado.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfico de Receita por Mês
        @if(!empty($revenueChart['data']))
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: @json($revenueChart['labels']),
                datasets: [{
                    label: 'Receita (Kz)',
                    data: @json($revenueChart['data']),
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Kz ' + value.toLocaleString('pt-MZ');
                            }
                        }
                    }
                }
            }
        });
        @endif

        // Gráfico de Distribuição de Pagamentos
        @if(!empty($paymentMethodChart['data']))
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        new Chart(paymentMethodCtx, {
            type: 'pie',
            data: {
                labels: @json($paymentMethodChart['labels']),
                datasets: [{
                    data: @json($paymentMethodChart['data']),
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.5)',
                        'rgba(59, 130, 246, 0.5)',
                        'rgba(168, 85, 247, 0.5)',
                        'rgba(249, 115, 22, 0.5)'
                    ],
                    borderColor: [
                        'rgb(34, 197, 94)',
                        'rgb(59, 130, 246)',
                        'rgb(168, 85, 247)',
                        'rgb(249, 115, 22)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
        @endif
    </script>
    @endpush
</x-app-layout>