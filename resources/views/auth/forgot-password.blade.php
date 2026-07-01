<x-guest-layout>
    {{-- Logo / Título --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">
            Recuperar senha
        </h1>

        <p class="mt-3 text-sm leading-relaxed text-gray-500">
            Informe o seu e-mail para receber um código de recuperação.
        </p>
    </div>

    {{-- Status --}}
    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.send.code') }}" class="space-y-5">
        @csrf

        {{-- Campo Email --}}
        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                E-mail
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                placeholder="Digite o seu e-mail"
                class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 outline-none transition focus:border-gray-500 focus:ring-0"
            >

            @error('email')
                <p class="mt-2 text-sm text-red-500">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Botão --}}
        <button type="submit" class="w-full rounded-2xl bg-gray-900 py-3 text-sm font-medium text-white transition hover:bg-gray-800 active:scale-[0.99]">
            Enviar código
        </button>
    </form>

    {{-- Link Login --}}
    <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 transition hover:text-gray-900">
            Voltar ao login
        </a>
    </div>
</x-guest-layout>