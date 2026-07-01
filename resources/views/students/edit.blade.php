<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Editar Estudante
            </h1>
            <p class="text-gray-600">
                Actualizar dados do estudante
            </p>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('students.update', $student) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Código --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Código do Estudante
                    </label>
                    <input type="text" name="student_code" value="{{ old('student_code', $student->student_code) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo
                    </label>
                    <input type="text" name="name" value="{{ old('name', $student->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $student->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg" >
                </div>

                {{-- Turma --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Turma
                    </label>
                    <input type="text" name="class" value="{{ old('class', $student->class) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Ano Lectivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ano Lectivo
                    </label>
                    <input type="text" name="academic_year" value="{{ old('academic_year', $student->academic_year) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Encarregado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Encarregado
                    </label>

                    <select name="guardian_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg" >
                        @foreach($guardians as $guardian)

                            <option value="{{ $guardian->id }}" {{ old('guardian_id', $student->guardian_id) == $guardian->id ? 'selected' : '' }}>
                                {{ $guardian->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Serviços --}}
                <div class="md:col-span-2">
                    <div class="border border-gray-200 rounded-xl p-6">
                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Serviços Associados
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($services as $service)
                                <label class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-4 cursor-pointer hover:border-school-primary hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-4">
                                        <input type="checkbox" name="services[]" value="{{ $service->id }}" {{ in_array($service->id, old('services', $student->services->pluck('id')->toArray())) ? 'checked' : '' }} class="w-5 h-5 text-school-primary rounded">
                                        <div>
                                            <div class="font-medium text-gray-900">
                                                {{ $service->name }}
                                            </div>

                                            <div class="text-sm text-gray-500">
                                                {{ number_format($service->price, 2, ',', '.') }} Kz
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('students.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg">
                    Actualizar Estudante
                </button>
            </div>
        </form>
    </div>
</x-app-layout>