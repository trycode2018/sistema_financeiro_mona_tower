<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Estudantes
                </h1>
                <p class="text-gray-600">
                    Gestão de estudantes do sistema
                </p>
            </div>
           <a href="{{ route('students.create') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Novo Estudante</span>
                </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Código</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Estudante</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Turma</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Encarregado</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Serviços</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">Ações</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- Código --}}
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-900">
                                    {{ $student->student_code }}
                                </div>
                            </td>

                            {{-- Estudante --}}
                            <td class="px-6 py-5">
                                <div class="font-semibold text-gray-900">
                                    {{ $student->name }}
                                </div>

                                <div class="text-sm text-gray-500">
                                    {{ $student->email }}
                                </div>
                            </td>

                            {{-- Turma --}}
                            <td class="px-6 py-5 text-gray-700">
                                {{ $student->class }}
                            </td>
                            {{-- Encarregado --}}
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-800">
                                    {{ $student->guardian->name ?? '---' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $student->guardian->email ?? '' }}
                                </div>
                            </td>

                            {{-- Serviços --}}
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($student->services->take(2) as $service)
                                        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full">
                                            {{ $service->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-400 text-sm">
                                            Nenhum
                                        </span>
                                    @endforelse
                                    @if($student->services->count() > 2)
                                        <span class="bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full">
                                            +{{ $student->services->count() - 2 }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-5">
                                <div class="flex justify-end items-center gap-4">
                                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    <a href="{{ route('students.edit', $student) }}" class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('students.destroy', $student) }}"
                                        method="POST"
                                        onsubmit="return confirm('Deseja remover este estudante?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-6 py-10 text-center text-gray-500">
                                Nenhum estudante encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Paginação --}}
    <div class="mt-6">
        {{ $students->links() }}
    </div>
</x-app-layout>