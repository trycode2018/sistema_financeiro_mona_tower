<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Registrar Pagamento</h1>
                <p class="text-gray-600">Registrar um pagamento para a fatura #{{ $invoice->invoice_number }}</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('invoices.payments.store', $invoice) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informações da Fatura -->
                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-medium text-gray-900">Informações da Fatura</h3>
                    <div class="grid grid-cols-2 gap-4 mt-2">
                        <div>
                            <p class="text-sm text-gray-600">Estudante</p>
                            <p class="font-medium">
                                {{ $invoice->student ? $invoice->student->name : 'Estudante não encontrado' }}
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

                <!-- Valor do Pagamento -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Valor do Pagamento (Kz) *</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           max="{{ $invoice->balance }}"
                           placeholder="0.00">
                    @error('amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data do Pagamento -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Data do Pagamento *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('payment_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Método de Pagamento -->
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Método de Pagamento *</label>
                    <select name="payment_method" id="payment_method" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                        <option value="">Seleccione o Método</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Dinheiro</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transferência Bancária</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Cartão</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referência -->
                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">Referência</label>
                    <input type="text" name="reference" id="reference" value="{{ old('reference') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           placeholder="Ex: Transferência nº 123">
                    @error('reference')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notas -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                              placeholder="Observações adicionais sobre o pagamento">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('invoices.show', $invoice) }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                    Registrar Pagamento
                </button>
            </div>
        </form>
    </div>

    <!-- Script para validação do valor máximo -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount');
            const maxAmount = parseFloat('{{ $invoice->balance }}');
            
            amountInput.addEventListener('input', function() {
                const value = parseFloat(this.value) || 0;
                if (value > maxAmount) {
                    this.setCustomValidity(`O valor não pode exceder Kz ${maxAmount.toFixed(2)}`);
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</x-app-layout>