<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Criar Nova Fatura</h1>
                <p class="text-gray-600">Emitir uma nova fatura para um estudante</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @if($students->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Nenhum Estudante Encontrado</h3>
            <p class="text-yellow-700 mb-4">É necessário criar pelo menos um estudante antes de criar uma fatura.</p>
            <a href="{{ route('students.create') }}" class="bg-school-primary text-white px-6 py-3 rounded-lg hover:bg-school-dark transition inline-flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Criar Primeiro Estudante</span>
            </a>
        </div>
        @else
        <form action="{{ route('invoices.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Estudante -->
                <div class="md:col-span-2">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Estudante *</label>
                    <select name="student_id" id="student_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                        <option value="">Seleccione o Estudante</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} ({{ $student->student_code }}) - {{ $student->class }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Data de Vencimento -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Data de Vencimento *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           min="{{ date('Y-m-d') }}">
                    @error('due_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valor Total -->
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">Valor Total (Kz) *</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                           placeholder="0.00" min="0">
                    @error('total_amount')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descrição -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descrição *</label>
                    <textarea name="description" id="description" rows="3" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                              placeholder="Descreva os serviços ou itens incluídos nesta fatura">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('invoices.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                    Criar Fatura
                </button>
            </div>
        </form>
        @endif
    </div>
</x-app-layout>