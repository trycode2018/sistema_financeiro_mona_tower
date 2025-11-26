<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
                <p class="text-gray-600">Detalhes do estudante</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('students.edit', $student) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Editar
                </a>
                <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition" 
                            onclick="return confirm('Tem certeza que deseja eliminar este estudante?')">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informações Pessoais -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações Pessoais</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Código</p>
                    <p class="font-medium">{{ $student->student_code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium">{{ $student->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Turma</p>
                    <p class="font-medium">{{ $student->class }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Ano Lectivo</p>
                    <p class="font-medium">{{ $student->academic_year }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Transporte</p>
                    <p class="font-medium">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $student->transport_required ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $student->transport_required ? 'Sim' : 'Não' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Informações do Encarregado -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Encarregado</h2>
            @if($student->guardian)
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Nome</p>
                    <p class="font-medium">{{ $student->guardian->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium">{{ $student->guardian->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Telefone</p>
                    <p class="font-medium">{{ $student->guardian->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Relacionamento</p>
                    <p class="font-medium">{{ $student->guardian->relationship }}</p>
                </div>
            </div>
            @else
            <p class="text-gray-500">Nenhum encarregado associado.</p>
            @endif
        </div>

        <!-- Faturas Recentes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Faturas Recentes</h2>
            <div class="space-y-3">
                @forelse($student->invoices->take(5) as $invoice)
                <div class="flex justify-between items-center p-3 border border-gray-200 rounded-lg">
                    <div>
                        <p class="font-medium">#{{ $invoice->invoice_number }}</p>
                        <p class="text-sm text-gray-600">{{ $invoice->due_date->format('d/m/Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">Kz {{ number_format($invoice->total_amount, 2, ',', ' ') }}</p>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : 
                               ($invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                               'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-gray-500">Nenhuma fatura encontrada.</p>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('invoices.create', ['student_id' => $student->id]) }}" class="text-school-primary hover:text-school-dark text-sm font-medium">
                    + Adicionar Nova Fatura
                </a>
            </div>
        </div>
    </div>
</x-app-layout>