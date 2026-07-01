<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Pagamentos</h1>
                <p class="text-gray-600">Lista de todos os pagamentos registados</p>
            </div>
            <div class="flex space-x-2">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('invoices.index') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Registar Novo Pagamento</span>
                    </a>
                @endif
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
                
                <input type="date" class="border-gray-300 rounded-lg shadow-sm">
                <input type="date" class="border-gray-300 rounded-lg shadow-sm">
                
                <button class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition">
                    Filtrar
                </button>
            </div>

            <!-- Tabela de Pagamentos -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estudante</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fatura</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado / Validação</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $payment->id ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->invoice->student->name ?? 'N/A' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->invoice->invoice_number }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-[10px] font-bold rounded-full uppercase 
                                    {{ $payment->payment_method === 'cash' ? 'bg-green-100 text-green-800' : 
                                       ($payment->payment_method === 'bank_transfer' ? 'bg-blue-100 text-blue-800' : 
                                       ($payment->payment_method === 'card' ? 'bg-purple-100 text-purple-800' : 
                                       'bg-orange-100 text-orange-800')) }}">
                                    {{ $payment->payment_method }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                Kz {{ number_format($payment->amount, 2, ',', ' ') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($payment->status === 'pending')
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('payments.confirm', $payment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-1 py-1 rounded text-[10px] font-bold uppercase transition">
                                                Confirmar
                                            </button>
                                        </form>

                                        <form action="{{ route('payments.reject', $payment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-1 py-1 rounded text-[10px] font-bold uppercase transition" onclick="return confirm('Rejeitar este pagamento?')">
                                                Rejeitar
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="px-2 py-1 inline-flex text-[10px] leading-5 font-bold rounded-full uppercase
                                        {{ $payment->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $payment->status === 'confirmed' ? 'Confirmado' : 'Rejeitado' }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Eliminar este registo?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</x-app-layout>