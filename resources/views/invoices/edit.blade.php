<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editar Fatura</h1>
                <p class="text-gray-600">Atualizar informações da fatura</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
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
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valor Total -->
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Total (Kz) *</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount', $invoice->total_amount) }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('total_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição *</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">{{ old('description', $invoice->description) }}</textarea>
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
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                    Atualizar Fatura
                </button>
            </div>
        </form>
    </div>
</x-app-layout>