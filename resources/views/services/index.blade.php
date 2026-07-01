<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gestão de Serviços</h1>
                <p class="text-gray-600">Gerir serviços adicionais dos estudantes</p>
            </div>
            <a href="{{ route('services.create') }}" class="bg-school-primary text-white px-4 py-2 rounded-lg hover:bg-school-dark transition flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Novo Serviço</span>
            </a>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Serviço</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Tipo</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Preço</th>
                        <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Estado</th>
                        <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($services as $service)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-semibold text-gray-900">
                                    {{ $service->name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $service->description }}
                                </div>
                            </td>

                            <td class="p-4">
                                {{ $service->billing_type == 'monthly' ? 'Mensal' : 'Único' }}
                            </td>

                            <td class="p-4">
                                Kz {{ number_format($service->price, 2, ',', '.') }}
                            </td>

                            <td class="p-4">
                                <form action="{{ route('services.toggle-status', $service) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $service->is_active ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>

                            <td class="p-4 text-right space-x-3">
                                <a href="{{ route('services.edit', $service) }}" class="text-school-primary hover:text-school-dark">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>

                                <form action="{{ route('services.destroy', $service) }}" method="POST" class="inline" onsubmit="return confirm('Eliminar este serviço?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-500">
                                Nenhum serviço encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-6">
                {{ $services->links() }}
            </div>
        </div>
    </div>
</x-app-layout>