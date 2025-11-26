<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Adicionar Novo Estudante</h1>
                <p class="text-gray-600">Registrar um novo estudante no sistema</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Código do Estudante -->
                <div>
                    <label for="student_code" class="block text-sm font-medium text-gray-700 mb-2">Código do Estudante *</label>
                    <input type="text" name="student_code" id="student_code" value="{{ old('student_code') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('student_code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nome Completo -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Turma -->
                <div>
                    <label for="class" class="block text-sm font-medium text-gray-700 mb-2">Turma *</label>
                    <input type="text" name="class" id="class" value="{{ old('class') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('class')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ano Lectivo -->
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Ano Lectivo *</label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                    @error('academic_year')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Encarregado -->
                <div>
                    <label for="guardian_id" class="block text-sm font-medium text-gray-700 mb-2">Encarregado *</label>
                    <select name="guardian_id" id="guardian_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition">
                        <option value="">Seleccione o Encarregado</option>
                        @foreach($guardians as $guardian)
                            <option value="{{ $guardian->id }}" {{ old('guardian_id') == $guardian->id ? 'selected' : '' }}>
                                {{ $guardian->name }} ({{ $guardian->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('guardian_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Transporte -->
                <div class="flex items-center">
                    <input type="checkbox" name="transport_required" id="transport_required" value="1" {{ old('transport_required') ? 'checked' : '' }}
                           class="w-4 h-4 text-school-primary focus:ring-school-primary border-gray-300 rounded">
                    <label for="transport_required" class="ml-2 block text-sm text-gray-700">
                        Necessita de transporte escolar?
                    </label>
                </div>
            </div>

            <!-- Botões -->
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('students.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                    Adicionar Estudante
                </button>
            </div>
        </form>
    </div>
</x-app-layout>