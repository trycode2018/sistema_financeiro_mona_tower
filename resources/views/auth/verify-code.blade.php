<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900">
            Verificar código
        </h1>

        <p class="mt-3 text-sm text-gray-500 leading-relaxed">
            Digite o código enviado para o seu e-mail.
        </p>
    </div>

    <form method="POST" action="{{ route('password.verify.code') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="email" value="{{ session('reset_email') }}">

        <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">
                Código
            </label>

            <input
                type="text"
                name="code"
                maxlength="6"
                required
                autofocus
                placeholder="000000"
                class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-center text-2xl tracking-[10px] text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-500"
            >

            @error('code')
                <p class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-2xl bg-gray-900 py-3 text-sm font-medium text-white transition hover:bg-gray-800">
            Verificar código
        </button>
    </form>
</x-guest-layout>