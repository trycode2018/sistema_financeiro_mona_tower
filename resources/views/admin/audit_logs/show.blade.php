<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Registos de Auditoria</h1>
                <p class="text-gray-600">Lista de todos os registos de auditoria</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho do Detalhe -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Registo de Auditoria: {{ $auditLog->action }}</h2>
                    <p class="text-sm text-indigo-600 font-medium">Histórico detalhado da operação no sistema</p>
                </div>
                <a href="{{ route('audit-logs') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 flex items-center gap-1">
                    ← Voltar à lista
                </a>
            </div>

            <!-- Cards de Informação (3 colunas) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Responsável -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-3">RESPONSÁVEL</p>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <span class="text-xs font-bold text-gray-700">
                                {{ strtoupper(substr($auditLog->user->name ?? 'S', 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">{{ $auditLog->user->name ?? 'Secretária' }}</p>
                            <p class="text-xs text-gray-500">{{ $auditLog->user->email ?? 'secretaria@gmail.com' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Entidade Afetada -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-3">ENTIDADE AFETADA</p>
                    <p class="text-sm font-bold text-gray-800">{{ $auditLog->auditable->invoice_number ?? $auditLog->auditable->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-500 mt-1">ID do Registo: {{ $auditLog->auditable->id ?? 'N/A' }}</p>
                </div>

                <!-- Data e Hora -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-3">DATA E HORA</p>
                    <p class="text-sm font-bold text-gray-800">{{ $auditLog->created_at->format('d/m/Y') ?? '26/04/2026' }}</p>
                    <p class="text-xs text-gray-500">{{ $auditLog->created_at->format('H:i:s') ?? '13:50:48' }} (UTC)</p>
                </div>
            </div>

            <!-- Tabela Comparativa -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700">Comparativo de Dados</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Campo</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Antes (OLD)</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-green-600 uppercase tracking-wider">Depois (NEW)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $oldValues = $auditLog->old_values ?? [
                                    'total_amount' => '28000.00',
                                    'updated_at' => '2026-04-26 13:47:24'
                                ];
                                $newValues = $auditLog->new_values ?? [
                                    'total_amount' => '28500.00',
                                    'updated_at' => '2026-04-26 13:50:48'
                                ];
                                $fields = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                            @endphp

                            @foreach($fields as $field)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-mono font-semibold text-indigo-600 bg-gray-50/30">
                                        {{ $field }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 italic">
                                        {{ $oldValues[$field] ?? '---' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium bg-green-50">
                                        {{ $newValues[$field] ?? '---' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Rodapé com informações adicionais (igual primeira imagem) -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-between items-center text-xs text-gray-500">
                    <div class="flex items-center gap-4">
                        <span><i class="far fa-id-card mr-1"></i> Log ID: #{{ $auditLog->id ?? 'N/A' }}</span>
                        <span><i class="far fa-user mr-1"></i> {{ $auditLog->user->name ?? 'Administrador' }} ({{ $auditLog->user->email ?? 'admin@gmail.com' }})</span>
                    </div>
                    <span><i class="far fa-calendar-alt mr-1"></i> {{ $auditLog->created_at->format('d/m/Y H:i:s') ?? '26/04/2026 13:50:48' }}</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>