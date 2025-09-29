@props([
    'title' => '',
    'value' => '',
    'hint'  => null,
    'icon'  => null,     // svg inline (opcional)
    'accent'=> 'indigo', // indigo|cyan|purple|emerald|amber|rose
    'href'  => null,     // deixa o card clicável
])

@php
$accent = match($accent) {
    'cyan'    => 'from-cyan-50 to-white dark:from-cyan-900/20',
    'purple'  => 'from-purple-50 to-white dark:from-purple-900/20',
    'emerald' => 'from-emerald-50 to-white dark:from-emerald-900/20',
    'amber'   => 'from-amber-50 to-white dark:from-amber-900/20',
    'rose'    => 'from-rose-50 to-white dark:from-rose-900/20',
    default   => 'from-indigo-50 to-white dark:from-indigo-900/20',
};
$container = "bg-gradient-to-b $accent rounded-2xl shadow-sm border border-black/5 dark:border-white/5 p-4 hover:shadow-md transition";
$content = '
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-gray-500 dark:text-gray-300 font-medium">'.$title.'</div>
            <div class="mt-1 text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">'.$value.'</div>
            '.($hint ? '<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">'.$hint.'</div>' : '').'
        </div>
        <div class="shrink-0 opacity-80">
            '.($icon ?? '').'
        </div>
    </div>
';
@endphp

@if($href)
    <a href="{{ $href }}" class="{{ $container }}">
        {!! $content !!}
    </a>
@else
    <div class="{{ $container }}">
        {!! $content !!}
    </div>
@endif
