<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Registos de Auditoria</h1>
                <p class="text-gray-600">Lista de todos os registos de auditoria</p>
            </div>
        </div>
    </x-slot>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="mb-6 flex flex-wrap gap-4">
                <select class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                    <option value="">Todas as Acções</option>
                    <option value="create">Criação</option>
                    <option value="update">Actualização</option>
                    <option value="delete">Eliminação</option>
                </select>
                
                <select class="border-gray-300 rounded-lg shadow-sm focus:border-school-primary focus:ring focus:ring-school-primary focus:ring-opacity-50">
                    <option value="">Todos os Módulos</option>
                    <option value="Invoice">Faturas</option>
                    <option value="Payment">Pagamentos</option>
                    <option value="User">Utilizadores</option>
                </select>
            </div>

            <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data e Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilizador</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase text-center">Acção</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3 border border-indigo-200">
                                        <span class="text-xs font-bold text-indigo-700">
                                            {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Sistema' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full 
                                    {{ $log->action === 'Criou' ? 'bg-green-100 text-green-700 border border-green-200' : 
                                    ($log->action === 'Atualizou' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 
                                    ($log->action === 'Eliminou' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-gray-100 text-gray-700')) }}">
                                    {{ strtoupper($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('audit-logs.show', $log->id) }}" class="inline-flex items-center text-blue-600 hover:text-blue-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">Nenhum registo encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>