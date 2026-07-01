<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Encarregados</h1>
                <p class="text-gray-600">Lista de todos os encarregados</p>
            </div>
            <a href="{{ route('guardians.create') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark flex items-center space-x-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-semibold">Novo Encarregado</span>
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Código</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Nome</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Email</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Telefone</th>
                            <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($guardians as $guardian)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-900">
                                    {{ $guardian->id }}
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-900">
                                    {{ $guardian->name }}
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-800">
                                    {{ $guardian->email }}
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="font-medium text-gray-700">
                                    {{ $guardian->phone }}
                                </div>
                            </td>
                            <td class="p-4 text-right space-x-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('guardians.show', $guardian) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    
                                    <a href="{{ route('guardians.edit', $guardian) }}" class="text-yellow-600 hover:text-yellow-900 ml-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>

                                    <form action="{{ route('guardians.destroy', $guardian) }}" method="POST" class="inline-block" onsubmit="return confirm('Tem certeza que deseja excluir este encarregado?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 ml-4">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">
            {{ $guardians->links() }}
        </div>
    </div>
</x-app-layout>