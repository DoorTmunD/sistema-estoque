<div class="relative min-h-screen overflow-x-hidden">
    {{-- BG suave --}}
    <svg class="fixed inset-0 w-full h-full pointer-events-none z-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 900" preserveAspectRatio="none">
        <defs>
            <linearGradient id="bgGrad1" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#7DD3FC" stop-opacity="0.10"/>
                <stop offset="100%" stop-color="#C4B5FD" stop-opacity="0.07"/>
            </linearGradient>
            <radialGradient id="bgGrad2" cx="80%" cy="20%" r="70%">
                <stop offset="0%" stop-color="#FDE68A" stop-opacity="0.13"/>
                <stop offset="100%" stop-color="#FFF" stop-opacity="0"/>
            </radialGradient>
        </defs>
        <rect width="1440" height="900" fill="url(#bgGrad1)" />
        <ellipse cx="1200" cy="120" rx="320" ry="100" fill="url(#bgGrad2)" />
        <ellipse cx="200" cy="800" rx="250" ry="80" fill="#7DD3FC10" />
        <ellipse cx="700" cy="600" rx="450" ry="120" fill="#C4B5FD10" />
    </svg>

    <div class="max-w-7xl mx-auto px-4 py-10 relative z-20">

        {{-- Saudação + filtro de período --}}
        @php
            // normaliza o período (evita undefined e valores fora da lista)
            $p = (int) ($periodDays ?? request('period', 30));
            $p = in_array($p, [7, 30, 180, 365], true) ? $p : 30;
        @endphp

        <div class="flex items-center justify-between mb-6">
            <div class="text-slate-400 text-base">
                Bem-vindo(a), <span class="font-semibold text-sky-500">{{ auth()->user()->name }}</span>!
            </div>

            <div class="inline-flex rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden">
                @foreach([7=>'7d',30=>'30d',180=>'6m',365=>'12m'] as $days => $label)
                    <a href="{{ route('dashboard', ['period' => $days]) }}"
                       class="px-3 py-1 text-sm {{ $p === $days ? 'bg-indigo-600 text-white' : 'hover:bg-gray-100 dark:hover:bg-white/10' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <h1 class="text-4xl font-extrabold mb-8 bg-gradient-to-r from-sky-500 via-violet-400 to-amber-300 bg-clip-text text-transparent drop-shadow">
            Dashboard
        </h1>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            @php
                $borders = [
                    'estoque'    => 'border-sky-400 dark:border-sky-400 dark:shadow-[0_0_16px_2px_#7dd3fc77]',
                    'abaixo'     => 'border-emerald-400 dark:border-emerald-300 dark:shadow-[0_0_16px_2px_#34d39955]',
                    'categorias' => 'border-violet-400 dark:border-violet-300 dark:shadow-[0_0_16px_2px_#c4b5fd55]',
                    'fornec'     => 'border-amber-300 dark:border-amber-200 dark:shadow-[0_0_16px_2px_#fde68a55]',
                ];
            @endphp

            {{-- Estoque total --}}
            <div class="bg-white/70 dark:bg-slate-800/80 {{ $borders['estoque'] }} rounded-2xl p-6 shadow backdrop-blur-xl hover:-translate-y-1 transition">
                <lottie-player src="https://lottie.host/18c15992-8a04-40c9-90ea-bbc8e400f849/2N90EEiLSA.json" background="transparent" speed="1" style="width:28px;height:28px;margin-bottom:.3rem" loop autoplay></lottie-player>
                <div class="text-sm text-slate-600 dark:text-slate-100">Estoque Total (R$)</div>
                <div class="text-3xl font-extrabold text-sky-500 dark:text-sky-300 mt-1">
                    R$ {{ number_format($totalStock ?? 0, 2, ',', '.') }}
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">Custo médio ponderado</div>
            </div>

            {{-- Abaixo do ideal (hover = lista) --}}
            <div x-data="{ open:false }" @mouseenter="open=true" @mouseleave="open=false"
                 class="relative bg-white/70 dark:bg-slate-800/80 {{ $borders['abaixo'] }} rounded-2xl p-6 shadow backdrop-blur-xl hover:-translate-y-1 transition">
                <div class="text-sm text-slate-600 dark:text-slate-100">Produtos abaixo do ideal</div>
                <div class="text-3xl font-extrabold text-emerald-500 dark:text-emerald-200 mt-1">{{ $belowIdeal ?? 0 }}</div>

                @if(($belowIdeal ?? 0) === 0)
                    <div class="text-emerald-500 dark:text-emerald-300 text-sm mt-1">Tudo em dia 🎉</div>
                @endif

                @if(($belowIdeal ?? 0) > 0 && !empty($produtosAbaixoIdeal))
                    <div x-show="open" x-transition
                         class="absolute left-1/2 top-full mt-2 -translate-x-1/2 min-w-[280px] bg-white dark:bg-slate-900/90 rounded-xl shadow-2xl border border-emerald-200 dark:border-emerald-400 p-4 z-50"
                         style="display:none">
                        <div class="font-semibold text-emerald-600 dark:text-emerald-300 mb-2 text-center">Produtos abaixo do ideal</div>
                        <ul class="text-sm space-y-2 max-h-64 overflow-auto pr-1">
                            @foreach($produtosAbaixoIdeal as $prod)
                                <li class="flex flex-col">
                                    <span class="font-semibold text-slate-700 dark:text-slate-100">{{ $prod['name'] }}</span>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        Atual: <span class="font-semibold text-emerald-700 dark:text-emerald-300">{{ $prod['current'] }}</span>
                                        <span class="mx-1 text-slate-400">|</span>
                                        Ideal: <span class="font-semibold text-orange-600 dark:text-orange-300">{{ $prod['ideal'] }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Categorias --}}
            <div class="bg-white/70 dark:bg-slate-800/80 {{ $borders['categorias'] }} rounded-2xl p-6 shadow backdrop-blur-xl hover:-translate-y-1 transition">
                <lottie-player src="https://lottie.host/11fd5a9b-9b23-4980-9b24-1a67c431bf1f/k1TAwGQXBG.json" background="transparent" speed="1" style="width:28px;height:28px;margin-bottom:.3rem" loop autoplay></lottie-player>
                <div class="text-sm text-slate-600 dark:text-slate-100">Categorias</div>
                <div class="text-3xl font-extrabold text-violet-500 dark:text-violet-200 mt-1">{{ $categoryCount ?? 0 }}</div>
            </div>

            {{-- Fornecedores --}}
            <div class="bg-white/70 dark:bg-slate-800/80 {{ $borders['fornec'] }} rounded-2xl p-6 shadow backdrop-blur-xl hover:-translate-y-1 transition">
                <lottie-player src="https://lottie.host/195ceff7-dc4e-4c39-9bc0-fcff7f13f30b/K9QkD0dXDv.json" background="transparent" speed="1" style="width:28px;height:28px;margin-bottom:.3rem" loop autoplay></lottie-player>
                <div class="text-sm text-slate-600 dark:text-slate-100">Fornecedores</div>
                <div class="text-3xl font-extrabold text-amber-500 dark:text-amber-200 mt-1">{{ $supplierCount ?? 0 }}</div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white/70 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-600 rounded-2xl p-6 backdrop-blur-sm shadow">
                <div class="font-semibold mb-3 text-slate-800 dark:text-slate-100">
                    Movimentação — últimos {{ $p }} dias
                </div>
                <div id="chart-movements"></div>
            </div>

            <div class="bg-white/70 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-600 rounded-2xl p-6 backdrop-blur-sm shadow">
                <div class="font-semibold mb-3 text-slate-800 dark:text-slate-100">
                    Estoque por Categoria (R$)
                </div>
                <div id="chart-category"></div>
            </div>
        </div>

        <div class="bg-white/70 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-600 rounded-2xl p-6 backdrop-blur-sm shadow">
            <div class="font-semibold mb-3 text-slate-800 dark:text-slate-100">Top 5 Produtos por Valor em Estoque</div>
            @if(!empty($topProductsByValue))
                <div id="chart-top-products"></div>
            @else
                <div class="flex flex-col items-center py-8 opacity-60">
                    <svg class="w-12 h-12 mb-2 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-dasharray="4 4" />
                        <path d="M8 12l2 2 4-4" stroke-linecap="round" />
                    </svg>
                    <span class="text-base">Sem dados suficientes.</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Scripts --}}
    @push('scripts')
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener('livewire:load', function () {
                // Movimentação
                new ApexCharts(
                    document.querySelector("#chart-movements"),
                    {
                        chart: { type: 'bar', height: 300, animations: { enabled: true }, toolbar: { show:false } },
                        series: [
                            { name: 'Entradas', data: @json($movEntries ?? []) },
                            { name: 'Saídas',   data: @json($movExits   ?? []) }
                        ],
                        xaxis: { categories: @json($movLabels ?? []) },
                        colors: ['#22d3ee', '#f59e0b'],
                        plotOptions: { bar: { columnWidth: '55%', borderRadius: 6 } },
                        dataLabels: { enabled: false },
                        grid: { strokeDashArray: 4 },
                        legend: { position: 'top' }
                    }
                ).render();

                // Categorias (donut em R$)
                new ApexCharts(
                    document.querySelector("#chart-category"),
                    {
                        chart: { type: 'donut', height: 300, animations: { enabled: true }, toolbar: { show:false } },
                        series: @json(collect($stockPerCategory ?? [])->pluck('value')),
                        labels: @json(collect($stockPerCategory ?? [])->pluck('name')),
                        dataLabels: { enabled: false },
                        legend: { position: 'bottom' }
                    }
                ).render();

                // Top produtos por valor (R$)
                new ApexCharts(
                    document.querySelector("#chart-top-products"),
                    {
                        chart: { type: 'bar', height: 280, toolbar: { show:false } },
                        plotOptions: { bar: { horizontal: true, borderRadius: 4 } },
                        series: [{
                            name: 'Valor (R$)',
                            data: @json(collect($topProductsByValue ?? [])->pluck('value'))
                        }],
                        xaxis: { categories: @json(collect($topProductsByValue ?? [])->pluck('name')) },
                        colors: ['#7DD3FC'],
                        dataLabels: { enabled: false },
                        grid: { strokeDashArray: 4 }
                    }
                ).render();
            });
        </script>
    @endpush
</div>
