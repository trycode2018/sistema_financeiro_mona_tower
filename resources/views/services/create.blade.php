<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Criar Serviço</h1>
        <p class="text-gray-600">Adicionar novo serviço ao sistema</p>
    </x-slot>

    <div class="bg-white p-6 rounded-lg border shadow-sm">

        <form method="POST" action="{{ route('services.store') }}">
            @include('services._form')
        </form>

    </div>

</x-app-layout>