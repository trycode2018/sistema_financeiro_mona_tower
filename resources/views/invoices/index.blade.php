<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Faturas</h1>
                <p class="text-gray-600">Lista de todas as faturas</p>
            </div>
            <a href="{{ route('invoices.create') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Nova Fatura</span>
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <!-- Filtros -->
            <div class="mb-6 flex gap-4">
                <select class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                    <option value="">Todos os Status</option>
                    <option value="pending">Pendente</option>
                    <option value="paid">Paga</option>
                    <option value="overdue">Vencida</option>
                </select>
                <input type="date" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50" placeholder="Data Inicial">
                <input type="date" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50" placeholder="Data Final">
                <button class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                    Filtrar
                </button>
            </div>

            <!-- Tabela de Faturas -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Número da Fatura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estudante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data Venc.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($invoices as $invoice)
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Kz {{ number_format($invoice->total_amount, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                       ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                       'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">Ver</a>
                                    <a href="{{ route('invoices.edit', $invoice) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    
                                    <!-- Botão de Pagamento - só mostra se houver saldo -->
                                    @if($invoice->balance > 0)
                                    <a href="{{ route('invoices.payments.create', $invoice) }}" class="text-green-600 hover:text-green-900 font-medium">
                                        Pagar
                                    </a>
                                    @else
                                    <span class="text-gray-400">Paga</span>
                                    @endif
                                    
                                    <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Tem certeza?')">Eliminar</button>
                                    </form>
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