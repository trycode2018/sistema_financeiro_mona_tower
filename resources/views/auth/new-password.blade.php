<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">
            Nova senha
        </h1>

        <p class="mt-3 text-sm text-gray-500">
            Crie uma nova senha para a sua conta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update.custom') }}" class="space-y-5">
        @csrf

        {{-- Senha --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Nova senha
            </label>

            <input
                type="password"
                name="password"
                required
                placeholder="Digite a nova senha"
                class="w-full rounded-2xl border border-gray-300 px-4 py-3 outline-none focus:border-gray-500"
            >

            @error('password')
                <p class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirmar --}}
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Confirmar senha
            </label>

            <input
                type="password"
                name="password_confirmation"
                required
                placeholder="Confirme a nova senha"
                class="w-full rounded-2xl border border-gray-300 px-4 py-3 outline-none focus:border-gray-500"
            >
        </div>

        <button type="submit" class="w-full rounded-2xl bg-gray-900 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
            Atualizar senha
        </button>
    </form>
</x-guest-layout>