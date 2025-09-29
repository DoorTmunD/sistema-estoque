<div class="max-w-lg mx-auto p-6 bg-white rounded-xl shadow-md space-y-4">
    <h2 class="font-bold text-lg mb-2">Configurações de Upload</h2>

    <form wire:submit.prevent="save">
        <div>
            <label class="block font-medium mb-1">Tamanho máximo por arquivo (MB):</label>
            <input type="number" min="1" max="100" wire:model.defer="max_file_size" class="input input-bordered w-full" />
        </div>
        <div>
            <label class="block font-medium mb-1">Extensões permitidas:</label>
            <div class="flex flex-wrap gap-2">
                @foreach(['jpg','png','webp','tiff','pdf'] as $ext)
                <label>
                    <input type="checkbox" value="{{ $ext }}" wire:model="allowed_extensions"> .{{ $ext }}
                </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="block font-medium mb-1">Upload obrigatório de imagem do produto?</label>
            <input type="checkbox" wire:model="product_image_required" class="toggle">
        </div>
        <div>
            <label class="block font-medium mb-1">Máx. arquivos por movimentação:</label>
            <select wire:model.defer="max_files_per_movement" class="input input-bordered w-full">
                <option value="1">1 arquivo</option>
                <option value="2">2 arquivos</option>
                <option value="unlimited">Ilimitado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Salvar</button>
        @if (session()->has('success'))
            <div class="mt-2 text-green-600">{{ session('success') }}</div>
        @endif
    </form>
</div>