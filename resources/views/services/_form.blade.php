@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Nome --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nome do Serviço
        </label>
        <input type="text"
               name="name"
               value="{{ old('name', $service->name ?? '') }}"
               required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Preço --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Preço (Kz)
        </label>
        <input type="number"
               step="0.01"
               name="price"
               value="{{ old('price', $service->price ?? '') }}"
               required
               class="w-full px-4 py-3 border border-gray-300 rounded-lg">
        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Tipo --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Tipo de Facturação
        </label>
        <select name="billing_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            <option value="monthly"
                {{ old('billing_type', $service->billing_type ?? '') == 'monthly' ? 'selected' : '' }}>
                Mensal
            </option>
            <option value="one_time"
                {{ old('billing_type', $service->billing_type ?? '') == 'one_time' ? 'selected' : '' }}>
                Único
            </option>
        </select>
    </div>

    {{-- Status (Ativo/Inactivo) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Status do Serviço
        </label>
        <select name="is_active" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
            <option value="1"
                {{ old('is_active', $service->is_active ?? true) == '1' ? 'selected' : '' }}>
                Activo
            </option>
            <option value="0"
                {{ old('is_active', $service->is_active ?? true) == '0' ? 'selected' : '' }}>
                Inactivo
            </option>
        </select>
    </div>
</div>

{{-- Descrição --}}
<div class="mt-6">
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Descrição
    </label>
    <textarea name="description"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg"
              rows="4">{{ old('description', $service->description ?? '') }}</textarea>
</div>

{{-- Botões --}}
<div class="mt-8 flex gap-4 justify-end">
    <a href="{{ route('services.index') }}"
       class="px-6 py-3 border border-gray-300 rounded-lg">
        Cancelar
    </a>
    <button type="submit" class="px-6 py-3 bg-school-primary text-white rounded-lg">
        {{ isset($service) ? 'Actualizar Serviço' : 'Guardar Serviço' }}
    </button>
</div>