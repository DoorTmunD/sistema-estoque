<div class="py-10 max-w-4xl mx-auto">
    <h2 class="text-3xl font-extrabold mb-8 flex items-center gap-3 text-purple-600 dark:text-purple-300 select-none">
        <i class="fa-solid fa-bolt-lightning animate-pulse"></i>
        Linha do Tempo de Movimentações
    </h2>

    <div class="relative border-l-4 border-purple-400/40 pl-10">
        @forelse($movements as $m)
            @php
                $isIn   = in_array($m->ui_kind, ['in']);
                $qty    = $m->qty ?? $m->quantity ?? 0;
                $icon   = $isIn ? 'fa-arrow-down text-green-400' : 'fa-arrow-up text-red-400';
                $badgeC = $isIn ? 'text-green-500' : 'text-red-400';
            @endphp

            <div class="mb-12 flex items-center group relative">
                <div class="absolute -left-7 w-10 h-10 rounded-full bg-gradient-to-tr from-purple-700 via-fuchsia-600 to-purple-400 shadow-[0_0_24px_#a21caf88] flex items-center justify-center ring-4 ring-purple-400/40 group-hover:ring-purple-300/70 transition-all animate-pulse" style="z-index:2">
                    <i class="fa-solid {{ $icon }} text-2xl animate-bounce"></i>
                </div>

                <div class="bg-white/80 dark:bg-gray-900/80 border border-purple-400/20 rounded-2xl px-8 py-5 shadow-xl w-full backdrop-blur-lg group-hover:scale-[1.025] transition-all relative z-10">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-base {{ $badgeC }}">
                            {{ $isIn ? '+ ' : '- ' }}{{ $m->type_label }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">{{ optional($m->created_at)->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="text-lg font-bold text-purple-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-cube"></i>
                        {{ $m->product->name ?? '—' }}
                        <span class="ml-2 text-sm text-gray-500 dark:text-white/60">({{ $qty }})</span>
                    </div>

                    <div class="text-xs text-purple-500 mt-1 mb-2">
                        Responsável: {{ $m->performer->name ?? $m->user->name ?? '—' }}
                    </div>

                    @if(!empty($m->notes))
                        <div class="mb-2 text-gray-500 dark:text-gray-300 text-xs">{{ $m->notes }}</div>
                    @endif

                    {{-- Anexos (opcional, só se existir relação "files") --}}
                    @php $hasFiles = method_exists($m, 'files') && $m->files && count($m->files); @endphp
                    @if($hasFiles)
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($m->files as $file)
                                <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                   class="flex items-center gap-2 px-2 py-1 bg-purple-100/60 dark:bg-purple-800/80 hover:bg-purple-300/90 dark:hover:bg-purple-700 rounded-lg text-sm text-purple-900 dark:text-purple-200 font-semibold shadow-sm transition-all"
                                   title="{{ $file->original_name }}">
                                    @if(in_array($file->extension, ['jpg','jpeg','png','webp','tiff']))
                                        <img src="{{ asset('storage/' . $file->file_path) }}" class="w-8 h-8 object-cover rounded border border-purple-400 shadow" />
                                    @elseif($file->extension === 'pdf')
                                        <i class="fa-solid fa-file-pdf text-lg text-red-500"></i>
                                    @else
                                        <span class="font-mono text-xs text-purple-600 dark:text-purple-100">{{ strtoupper($file->extension) }}</span>
                                    @endif
                                    <span class="truncate max-w-[140px]">{{ \Illuminate\Support\Str::limit($file->original_name, 18) }}</span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 dark:text-gray-500 py-14">
                <i class="fa-solid fa-inbox-open text-2xl mr-2"></i>
                Nenhuma movimentação encontrada.
            </div>
        @endforelse

        <div class="absolute top-0 left-0 h-full w-2">
            <div class="h-full w-1 mx-auto bg-gradient-to-b from-purple-300 via-purple-500 to-fuchsia-400 opacity-70 animate-pulse"></div>
        </div>
    </div>
</div>
