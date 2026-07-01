<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $guardian->name }}</h1>
                <p class="text-gray-600">Detalhes do encarregado</p>
            </div>
            
            <div class="flex space-x-2">
                <a href="{{ route('guardians.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Voltar
                </a>
                <a href="{{ route('guardians.edit', $guardian) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                    Editar
                </a>
                <form action="{{ route('guardians.destroy', $guardian) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition" 
                            onclick="return confirm('Tem certeza que deseja eliminar este encarregado?')">
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
                    <p class="font-medium">{{ $guardian->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Parentesco</p>
                    <p class="font-medium">{{ $guardian->relationship }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Morada</p>
                    <p class="font-medium">{{ $guardian->address }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-medium">{{ $guardian->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Telefone</p>
                    <p class="font-medium">{{ $guardian->phone }}</p>
                </div>
            </div>
        </div>
        <!-- Estudantes Associados -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Estudantes Associados</h2>
            @if($guardian->students->isEmpty())
                <p class="text-gray-600">Nenhum estudante associado a este encarregado.</p>
            @else
                <ul class="space-y-2">
                    @foreach($guardian->students as $student)
                        <li class="flex justify-between items-center">
                            <span>{{ $student->name }}</span>
                            <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-900">Ver Detalhes</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</x-app-layout>