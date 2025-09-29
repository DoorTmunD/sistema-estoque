@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
        <i class="fa-solid fa-arrow-right-arrow-left text-cyan-500"></i>
        Histórico de Movimentações
    </h2>
@endsection

@section('content')
<div class="bg-white/90 dark:bg-gray-900/90 shadow-2xl rounded-2xl p-8 border border-gray-100 dark:border-gray-800">
    {{-- FILTRO --}}
    <form method="GET" class="mb-8 flex flex-col md:flex-row md:items-end gap-4">
        <div class="flex-1">
            <label class="block text-sm font-bold mb-1 text-gray-600 dark:text-gray-300">Produto</label>
            <select name="product_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 py-2 px-3 focus:ring-2 focus:ring-cyan-400 transition-all">
                <option value="">Todos Produtos</option>
                @foreach(\App\Models\Product::orderBy('name')->get() as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                        {{ $product->name }} ({{ $product->supplier->name ?? '—' }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 mt-4 md:mt-0 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg shadow transition font-semibold">
            <i class="fa-solid fa-filter"></i> Filtrar
        </button>
        @if(request()->filled('product_id'))
            <a href="{{ route('movements.index') }}" class="inline-flex items-center gap-2 px-5 py-2 mt-4 md:mt-0 bg-gray-200 hover:bg-gray-400 text-gray-700 rounded-lg shadow transition font-semibold">
                <i class="fa-solid fa-xmark"></i> Limpar
            </a>
        @endif
    </form>

    {{-- TABELA --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-xs md:text-sm divide-y divide-gray-200 dark:divide-gray-700 rounded-xl shadow-lg">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Data</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Produto</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Serial</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qtd</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unitário</th>
                    <th class="px-4 py-3 text-right font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Executado por</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Colaborador</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Observações</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Anexos</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($movements as $move)
                    @php
                        $qty       = $move->qty ?? $move->quantity ?? 0;
                        $uCost     = $move->unit_cost ?? $move->unit_price ?? 0;
                        $tCost     = $move->total_cost ?? $move->total_price ?? ($uCost * $qty);
                        $serial    = $move->item->serial_internal ?? '—';
                        $typeLabel = $move->type_label ?? '—';
                        $typeColor = in_array($move->ui_kind, ['in']) ? 'text-green-600 dark:text-green-400'
                                    : (in_array($move->ui_kind, ['out']) ? 'text-red-600 dark:text-red-400'
                                    : 'text-amber-600 dark:text-amber-400');
                        $performer = $move->performer->name ?? $move->user->name ?? '—';
                        $hasFiles  = method_exists($move, 'files') && $move->files && count($move->files);
                    @endphp

                    <tr class="hover:bg-cyan-50 dark:hover:bg-cyan-950 transition-all group">
                        <td class="px-4 py-4 whitespace-nowrap text-gray-900 dark:text-gray-200 font-mono">
                            {{ optional($move->created_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-cyan-600 dark:text-cyan-300">
                            {{ $move->product->name ?? '—' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-mono">{{ $serial }}</td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold {{ $typeColor }}">{{ $typeLabel }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-right font-mono">{{ $qty }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-right font-mono">R$ {{ number_format($uCost, 2, ',', '.') }}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-right font-bold font-mono">R$ {{ number_format($tCost, 2, ',', '.') }}</td>
                        <td class="px-4 py-4 whitespace-nowrap"><span class="inline-flex items-center gap-1"><i class="fa-solid fa-user-circle"></i> {{ $performer }}</span></td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $move->collaborator->name ?? '—' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">{{ $move->notes ?: '—' }}</td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($hasFiles)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($move->files as $file)
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                           class="inline-flex items-center justify-center w-8 h-8 bg-cyan-100 dark:bg-cyan-800 border border-cyan-400 dark:border-cyan-600 rounded-lg shadow group-hover:scale-110 transition-all"
                                           title="{{ $file->original_name }}">
                                           @if(in_array($file->extension, ['jpg','jpeg','png','webp','tiff']))
                                               <img src="{{ asset('storage/' . $file->file_path) }}" class="w-7 h-7 object-cover rounded border border-purple-400" />
                                           @elseif($file->extension === 'pdf')
                                               <i class="fa-solid fa-file-pdf text-xl text-red-500"></i>
                                           @else
                                               <span class="font-mono text-xs text-cyan-900 dark:text-cyan-200">{{ strtoupper($file->extension) }}</span>
                                           @endif
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="py-14 text-center text-gray-400 dark:text-gray-500 text-lg">
                            <i class="fa-solid fa-inbox-open text-2xl mr-2"></i>
                            Nenhuma movimentação encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-end">
        {{ $movements->links() }}
    </div>
</div>
@endsection
