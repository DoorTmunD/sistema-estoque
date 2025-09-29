<div class="p-6 bg-white rounded-xl shadow space-y-4 max-w-xl mx-auto">
    @if ($successMsg)
        <div class="text-green-600 font-bold mb-2">{{ $successMsg }}</div>
    @endif

    <form wire:submit.prevent="save">
        <div>
            <label class="font-medium">Produto:</label>
            <select wire:model="product_id" class="input input-bordered w-full">
                <option value="">Selecione</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name ?? $product->nome }}</option>
                @endforeach
            </select>
            @error('product_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="font-medium">Tipo de Movimentação:</label>
            <select wire:model="movement_type" class="input input-bordered w-full">
                <option value="">Selecione</option>
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
            </select>
            @error('movement_type') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="font-medium">Quantidade:</label>
            <input type="number" wire:model="quantity" class="input input-bordered w-full" min="1">
            @error('quantity') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="font-medium">Data/Hora:</label>
            <input type="datetime-local" wire:model="executed_at" class="input input-bordered w-full">
            @error('executed_at') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="font-medium">Observação:</label>
            <textarea wire:model="observation" class="input input-bordered w-full"></textarea>
            @error('observation') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="font-medium">Anexar arquivos (imagem ou PDF):</label>
            <input type="file" wire:model="files" multiple
                accept="{{ implode(',', array_map(fn($e) => '.' . $e, $allowed_extensions)) }}"
                class="input input-bordered w-full">
            @error('files') <span class="text-red-500">{{ $message }}</span> @enderror
            @error('files.*') <span class="text-red-500">{{ $message }}</span> @enderror

            <div class="flex gap-4 mt-2 flex-wrap">
                @foreach($files as $file)
                    <div class="relative">
                        @if (in_array($file->getClientOriginalExtension(), ['jpg','jpeg','png','webp','tiff']))
                            <img src="{{ $file->temporaryUrl() }}" class="w-20 h-20 object-cover rounded shadow" />
                        @elseif ($file->getClientOriginalExtension() == 'pdf')
                            <div class="w-20 h-20 flex items-center justify-center bg-gray-200 rounded">
                                <span>PDF</span>
                            </div>
                        @else
                            <div class="w-20 h-20 flex items-center justify-center bg-gray-100 rounded">
                                <span>{{ strtoupper($file->getClientOriginalExtension()) }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Salvar Movimentação</button>
    </form>
</div>