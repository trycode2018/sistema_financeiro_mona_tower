<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Criar Novo Encarregado</h1>
                <p class="text-gray-600">Registar um novo encarregado de educação</p>
            </div>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('guardians.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-school-primary focus:border-school-primary">
            </div>
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700">Morada</label>
                <textarea name="address" id="address" rows="3" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-school-primary focus:border-school-primary">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="relationship" class="block text-sm font-medium text-gray-700">Parentesco (Ex: Pai, Mãe, Tio)</label>
                <input type="text" name="relationship" id="relationship" value="{{ old('relationship') }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-school-primary focus:border-school-primary">
                @error('relationship') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-school-primary focus:border-school-primary">
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                <input type="text" name="phone" id="phone" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-school-primary focus:border-school-primary">
            </div>
            <div class="flex justify-end space-x-4">
                <a href="{{ route('guardians.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-school-primary text-white rounded-md hover:bg-school-dark">Criar Encarregado</button>
            </div>
        </form>
    </div>
</x-app-layout>