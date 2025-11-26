<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pagamento #{{ $payment->id }}</h1>
                <p class="text-gray-600">Detalhes do pagamento</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('payments.edit', $payment) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Editar
                </a>
                <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition" 
                            onclick="return confirm('Tem certeza que deseja eliminar este pagamento?')">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações do Pagamento -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações do Pagamento</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Valor</p>
                    <p class="font-medium">Kz {{ number_format($payment->amount, 2, ',', ' ') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data do Pagamento</p>
                    <p class="font-medium">{{ $payment->payment_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Método de Pagamento</p>
                    <p class="font-medium capitalize">{{ $payment->payment_method }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Referência</p>
                    <p class="font-medium">{{ $payment->reference ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Data de Registro</p>
                    <p class="font-medium">{{ $payment->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Informações da Fatura -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fatura</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Número da Fatura</p>
                    <p class="font-medium">#{{ $payment->invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estudante</p>
                    <p class="font-medium">{{ $payment->invoice->student->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Valor Total</p>
                    <p class="font-medium">Kz {{ number_format($payment->invoice->total_amount, 2, ',', ' ') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Valor Pago</p>
                    <p class="font-medium">Kz {{ number_format($payment->invoice->amount_paid, 2, ',', ' ') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Saldo</p>
                    <p class="font-medium">Kz {{ number_format($payment->invoice->balance, 2, ',', ' ') }}</p>
                </div>
            </div>
        </div>

        <!-- Notas -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Notas</h2>
            <p class="text-gray-700">{{ $payment->notes ?? 'Nenhuma nota fornecida.' }}</p>
        </div>
    </div>
</x-app-layout>