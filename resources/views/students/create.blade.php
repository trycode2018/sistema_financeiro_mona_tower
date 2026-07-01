<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Adicionar Novo Estudante
                </h1>

                <p class="text-gray-600">
                    Registrar um novo estudante no sistema
                </p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Código --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Código do Estudante *
                    </label>

                    <input
                        type="text"
                        name="student_code"
                        value="{{ old('student_code') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >

                    @error('student_code')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo *
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >

                    @error('name')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >

                    @error('email')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Turma --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Turma *
                    </label>

                    <input
                        type="text"
                        name="class"
                        value="{{ old('class') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >

                    @error('class')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Ano Lectivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ano Lectivo *
                    </label>

                    <input
                        type="text"
                        name="academic_year"
                        value="{{ old('academic_year') }}"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >

                    @error('academic_year')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Encarregado --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Encarregado *
                    </label>

                    <select
                        name="guardian_id"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-school-primary focus:border-school-primary transition"
                    >
                        <option value="">
                            Seleccione o Encarregado
                        </option>

                        @foreach($guardians as $guardian)

                            <option
                                value="{{ $guardian->id }}"
                                {{ old('guardian_id') == $guardian->id ? 'selected' : '' }}
                            >
                                {{ $guardian->name }} ({{ $guardian->email }})
                            </option>

                        @endforeach

                    </select>

                    @error('guardian_id')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Serviços --}}
                <div class="md:col-span-2">

                    <div class="border border-gray-200 rounded-xl p-6">

                        <div class="mb-5">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Serviços Associados
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Seleccione os serviços adicionais do estudante.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            @forelse($services as $service)

                                <label
                                    class="flex items-center justify-between border border-gray-200 rounded-xl px-4 py-4 cursor-pointer hover:border-school-primary hover:bg-gray-50 transition"
                                >

                                    <div class="flex items-center gap-4">

                                        <input
                                            type="checkbox"
                                            name="services[]"
                                            value="{{ $service->id }}"
                                            {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}
                                            class="w-5 h-5 text-school-primary focus:ring-school-primary border-gray-300 rounded"
                                        >

                                        <div>

                                            <div class="font-medium text-gray-900">
                                                {{ $service->name }}
                                            </div>

                                            <div class="text-sm text-gray-500">
                                                {{ number_format($service->price, 2, ',', '.') }} Kz
                                                •
                                                {{ $service->billing_type == 'monthly'
                                                    ? 'Mensal'
                                                    : 'Único'
                                                }}
                                            </div>

                                        </div>

                                    </div>

                                </label>

                            @empty

                                <div class="text-sm text-gray-500">
                                    Nenhum serviço disponível.
                                </div>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-4 mt-8">

                <a
                    href="{{ route('students.index') }}"
                    class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="px-6 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition"
                >
                    Adicionar Estudante
                </button>

            </div>

        </form>

    </div>
</x-app-layout>