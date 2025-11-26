<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Pagamentos</h1>
                <p class="text-gray-600">Lista de todos os pagamentos registados</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <!-- Filtros -->
            <div class="mb-6 flex flex-wrap gap-4">
                <select class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                    <option value="">Todos os Métodos</option>
                    <option value="cash">Dinheiro</option>
                    <option value="bank_transfer">Transferência Bancária</option>
                    <option value="card">Cartão</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>
                
                <input type="date" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50" placeholder="Data Inicial">
                <input type="date" class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50" placeholder="Data Final">
                
                <button class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark">
                    Filtrar
                </button>
            </div>

            <!-- Tabela de Pagamentos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Referência
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estudante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fatura
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Método
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acções
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment->reference ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->invoice->student->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->invoice->invoice_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                       ($payment->payment_method === 'bank_transfer' ? 'bg-blue-100 text-blue-800' : 
                                       ($payment->payment_method === 'card' ? 'bg-purple-100 text-purple-800' : 
                                       'bg-orange-100 text-orange-800')) }}">
                                    @switch($payment->payment_method)
                                        @case('cash')
                                            Dinheiro
                                            @break
                                        @case('bank_transfer')
                                            Transferência
                                            @break
                                        @case('card')
                                            Cartão
                                            @break
                                        @case('mobile_money')
                                            Mobile Money
                                            @break
                                    @endswitch
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                MZN {{ number_format($payment->amount, 2, ',', ' ') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('payments.show', $payment) }}" class="text-school-primary hover:text-school-dark">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('payments.edit', $payment) }}" class="text-yellow-600 hover:text-yellow-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    
                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Tem certeza que deseja eliminar este pagamento?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
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
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>