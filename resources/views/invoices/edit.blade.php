<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Fatura</h1>
                <p class="text-gray-600">Atualizar informações da fatura</p>
            </div>
            <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                ← Voltar à lista
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @if($invoice->status === 'pago' || $invoice->status === 'aguardando_confirmacao')
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Esta fatura está <strong>{{ $invoice->status === 'pago' ? 'paga' : 'com pagamento pendente de confirmação' }}</strong> e não pode ser editada.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('invoices.update', $invoice) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Estudante (não editável) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estudante</label>
                    <p class="font-medium">{{ $invoice->student->name }} ({{ $invoice->student->student_code }})</p>
                    <input type="hidden" name="student_id" value="{{ $invoice->student_id }}">
                </div>

                <!-- Data de Vencimento -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Vencimento *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           {{ $invoice->status === 'pago' || $invoice->status === 'aguardando_confirmacao' ? 'disabled' : '' }}>
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valor Total -->
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Total (Kz) *</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           {{ $invoice->status === 'pago' || $invoice->status === 'aguardando_confirmacao' ? 'disabled' : '' }}>
                    @error('total_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição *</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                              {{ $invoice->status === 'pago' || $invoice->status === 'aguardando_confirmacao' ? 'disabled' : '' }}>{{ old('description', $invoice->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('invoices.show', $invoice) }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                @if($invoice->status !== 'pago' && $invoice->status !== 'aguardando_confirmacao')
                    <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                        Atualizar Fatura
                    </button>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>