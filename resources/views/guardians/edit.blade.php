<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Editar Encarregado
            </h1>
            <p class="text-gray-600">
                Actualizar dados do encarregado
            </p>
        </div>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('guardians.update', $guardian) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nome --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo
                    </label>
                    <input type="text" name="name" value="{{ old('name', $guardian->name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $guardian->email) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                </div>

                {{-- Telefone com código de país --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Telefone
                    </label>
                    <div class="flex gap-2">
                        <select id="country_code" class="w-32 px-3 py-3 border border-gray-300 rounded-lg bg-white">
                            <option value="+244" {{ strpos(old('phone', $guardian->phone), '+244') === 0 ? 'selected' : '' }}>🇦🇴 +244 (Angola)</option>
                            <option value="+55" {{ strpos(old('phone', $guardian->phone), '+55') === 0 ? 'selected' : '' }}>🇧🇷 +55 (Brasil)</option>
                            <option value="+351" {{ strpos(old('phone', $guardian->phone), '+351') === 0 ? 'selected' : '' }}>🇵🇹 +351 (Portugal)</option>
                            <option value="+1" {{ strpos(old('phone', $guardian->phone), '+1') === 0 ? 'selected' : '' }}>🇺🇸 +1 (EUA)</option>
                            <option value="+44" {{ strpos(old('phone', $guardian->phone), '+44') === 0 ? 'selected' : '' }}>🇬🇧 +44 (Reino Unido)</option>
                            <option value="+33" {{ strpos(old('phone', $guardian->phone), '+33') === 0 ? 'selected' : '' }}>🇫🇷 +33 (França)</option>
                            <option value="+49" {{ strpos(old('phone', $guardian->phone), '+49') === 0 ? 'selected' : '' }}>🇩🇪 +49 (Alemanha)</option>
                            <option value="+34" {{ strpos(old('phone', $guardian->phone), '+34') === 0 ? 'selected' : '' }}>🇪🇸 +34 (Espanha)</option>
                            <option value="+39" {{ strpos(old('phone', $guardian->phone), '+39') === 0 ? 'selected' : '' }}>🇮🇹 +39 (Itália)</option>
                            <option value="+86" {{ strpos(old('phone', $guardian->phone), '+86') === 0 ? 'selected' : '' }}>🇨🇳 +86 (China)</option>
                            <option value="+81" {{ strpos(old('phone', $guardian->phone), '+81') === 0 ? 'selected' : '' }}>🇯🇵 +81 (Japão)</option>
                            <option value="+82" {{ strpos(old('phone', $guardian->phone), '+82') === 0 ? 'selected' : '' }}>🇰🇷 +82 (Coreia do Sul)</option>
                            <option value="+7" {{ strpos(old('phone', $guardian->phone), '+7') === 0 ? 'selected' : '' }}>🇷🇺 +7 (Rússia)</option>
                            <option value="+61" {{ strpos(old('phone', $guardian->phone), '+61') === 0 ? 'selected' : '' }}>🇦🇺 +61 (Austrália)</option>
                            <option value="+27" {{ strpos(old('phone', $guardian->phone), '+27') === 0 ? 'selected' : '' }}>🇿🇦 +27 (África do Sul)</option>
                            <option value="+258" {{ strpos(old('phone', $guardian->phone), '+258') === 0 ? 'selected' : '' }}>🇲🇿 +258 (Moçambique)</option>
                            <option value="+238" {{ strpos(old('phone', $guardian->phone), '+238') === 0 ? 'selected' : '' }}>🇨🇻 +238 (Cabo Verde)</option>
                            <option value="+239" {{ strpos(old('phone', $guardian->phone), '+239') === 0 ? 'selected' : '' }}>🇸🇹 +239 (São Tomé)</option>
                            <option value="+245" {{ strpos(old('phone', $guardian->phone), '+245') === 0 ? 'selected' : '' }}>🇬🇼 +245 (Guiné-Bissau)</option>
                        </select>
                        <input type="tel" 
                               id="phone_number" 
                               name="phone" 
                               value="{{ old('phone', preg_replace('/^\+?\d+/', '', $guardian->phone)) }}" 
                               required 
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-lg"
                               maxlength="12"
                               placeholder="923 456 789">
                    </div>
                    <input type="hidden" id="full_phone" name="full_phone">
                </div>

                {{-- Parentesco --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Parentesco
                    </label>
                    <select name="relationship" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <option value="">Selecione o parentesco</option>
                        <option value="Pai" {{ old('relationship', $guardian->relationship) == 'Pai' ? 'selected' : '' }}>Pai</option>
                        <option value="Mãe" {{ old('relationship', $guardian->relationship) == 'Mãe' ? 'selected' : '' }}>Mãe</option>
                        <option value="Tio" {{ old('relationship', $guardian->relationship) == 'Tio' ? 'selected' : '' }}>Tio</option>
                        <option value="Tia" {{ old('relationship', $guardian->relationship) == 'Tia' ? 'selected' : '' }}>Tia</option>
                        <option value="Avô" {{ old('relationship', $guardian->relationship) == 'Avô' ? 'selected' : '' }}>Avô</option>
                        <option value="Avó" {{ old('relationship', $guardian->relationship) == 'Avó' ? 'selected' : '' }}>Avó</option>
                        <option value="Irmão" {{ old('relationship', $guardian->relationship) == 'Irmão' ? 'selected' : '' }}>Irmão</option>
                        <option value="Irmã" {{ old('relationship', $guardian->relationship) == 'Irmã' ? 'selected' : '' }}>Irmã</option>
                        <option value="Primo" {{ old('relationship', $guardian->relationship) == 'Primo' ? 'selected' : '' }}>Primo</option>
                        <option value="Prima" {{ old('relationship', $guardian->relationship) == 'Prima' ? 'selected' : '' }}>Prima</option>
                        <option value="Outro" {{ old('relationship', $guardian->relationship) == 'Outro' ? 'selected' : '' }}>Outro</option>
                    </select>
                </div>

                {{-- Morada --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Morada
                    </label>
                    <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">{{ old('address', $guardian->address) }}</textarea>
                </div>
            </div>

            {{-- Botões --}}
            <div class="flex justify-end space-x-4 mt-8">
                <a href="{{ route('guardians.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg">
                    Actualizar Encarregado
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
// Máscara de telefone: formata automaticamente de 3 em 3 números
document.getElementById('phone_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é número
    let formattedValue = '';
    
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 3 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    
    e.target.value = formattedValue;
});

// Previne a entrada de espaços e caracteres não numéricos
document.getElementById('phone_number').addEventListener('keydown', function(e) {
    if (e.key === ' ' || e.key === 'Space') {
        e.preventDefault();
    }
});

// Combina código do país com número antes de enviar
document.querySelector('form').addEventListener('submit', function(e) {
    const countryCode = document.getElementById('country_code').value;
    const phoneNumber = document.getElementById('phone_number').value.replace(/\s/g, '');
    document.getElementById('full_phone').value = countryCode + phoneNumber;
    
    // Remove o campo phone_number para não enviar duplicado
    document.getElementById('phone_number').disabled = true;
});
</script>