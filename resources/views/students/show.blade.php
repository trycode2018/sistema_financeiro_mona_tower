<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $student->name }}
                </h1>

                <p class="text-gray-600">
                    Perfil completo do estudante
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('students.edit', $student) }}"
                   class="px-5 py-3 bg-school-primary text-white rounded-lg hover:bg-school-dark transition">
                    Editar Estudante
                </a>

                <a href="{{ route('students.index') }}"
                   class="px-5 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Informações principais --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Dados do estudante --}}
        <div class="md:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                Informações do Estudante
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="text-sm text-gray-500">
                        Código
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->student_code }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Nome
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->name }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Email
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->email }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Turma
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->class }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Ano Lectivo
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->academic_year }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Data de Registo
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Encarregado --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">
                Encarregado
            </h2>
            <div class="space-y-4">
                <div>
                    <div class="text-sm text-gray-500">
                        Nome
                    </div>
                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->guardian->name ?? '---' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Email
                    </div>

                    <div class="font-semibold text-gray-900 mt-1">
                        {{ $student->guardian->email ?? '---' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Serviços --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Serviços Associados
                </h2>

                <p class="text-sm text-gray-500">
                    Serviços vinculados ao estudante
                </p>
            </div>

            <div class="text-right">
                <div class="text-sm text-gray-500">
                    Total mensal adicional
                </div>

                <div class="text-2xl font-bold text-school-primary">
                    {{
                        number_format(
                            $student->services
                                ->where('billing_type', 'monthly')
                                ->sum('price'), 2, ',', '.'
                        )
                    }} Kz
                </div>
            </div>
        </div>

        @if($student->services->count())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($student->services as $service)
                    <div class="border border-gray-200 rounded-xl p-5 hover:shadow-sm transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">
                                    {{ $service->name }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ $service->description }}
                                </div>
                            </div>

                            @if($service->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">
                                    Ativo
                                </span>

                            @else
                                <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full">
                                    Inativo
                                </span>
                            @endif
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                {{ $service->billing_type == 'monthly'
                                    ? 'Cobrança mensal'
                                    : 'Cobrança única'
                                }}
                            </div>

                            <div class="text-lg font-bold text-school-primary">
                                {{ number_format($service->price, 2, ',', '.') }} Kz
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-400 text-lg">
                    Nenhum serviço associado
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    Este estudante não possui serviços adicionais.
                </p>
            </div>
        @endif
    </div>
</x-app-layout>