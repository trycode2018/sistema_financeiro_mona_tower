<x-app-layout>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Editar Serviço</h1>
        <p class="text-gray-600">Atualizar dados do serviço</p>
    </x-slot>

    <div class="bg-white p-6 rounded-lg border shadow-sm">

        <form method="POST" action="{{ route('services.update', $service) }}">
            @method('PUT')

            @include('services._form', ['service' => $service])

        </form>

    </div>

</x-app-layout>