@if(config('estocore.global_search_enabled'))
<div x-data="{ open: false }"
     @keydown.window.ctrl.k.prevent="open = true; $nextTick(() => $refs.input?.focus())"
     @keydown.escape.window="open = false">
    <button @click="open = true; $nextTick(() => $refs.input?.focus())"
            class="fixed bottom-4 right-4 z-50 p-4 bg-purple-600 rounded-full shadow-lg hover:scale-110 transition-all">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8" />
            <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
    </button>

    <div x-show="open"
         class="fixed inset-0 bg-black/60 flex items-center justify-center z-50"
         x-cloak
         @click.self="open = false">
        <div class="bg-white/90 p-6 rounded-xl shadow-xl w-full max-w-md"
             @keydown.enter.prevent="$wire.goTo(selectedType, selectedId)">
            <input x-ref="input"
                   wire:model.debounce.350ms="query"
                   class="w-full px-4 py-2 rounded border-2 border-purple-400 focus:ring focus:ring-purple-300 outline-none"
                   placeholder="Buscar em todo o sistema... (Ctrl+K)">

            <div class="mt-4 space-y-2">
                @if(strlen($query) < 2)
                    <div class="text-gray-400 text-center">Digite pelo menos 2 caracteres...</div>
                @elseif($results && count($results))
                    @foreach($results as $type => $items)
                        <div>
                            <div class="text-xs font-semibold text-purple-700 mb-1 uppercase">{{ $type }}</div>
                            <ul>
                                @foreach($items as $item)
                                    <li>
                                        <a href="#" 
                                           wire:click.prevent="goTo('{{ $type }}', {{ $item->id }})"
                                           class="block px-4 py-2 rounded bg-purple-50 hover:bg-purple-200 text-purple-700 transition">
                                            {{ method_exists($item, 'getLabelForSearch') ? $item->getLabelForSearch() : ($item->name ?? $item->title ?? $item->email ?? $item->id) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                @else
                    <div class="text-gray-400 text-center">Nenhum resultado encontrado.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
<!-- Busca Global desativada -->
@endif