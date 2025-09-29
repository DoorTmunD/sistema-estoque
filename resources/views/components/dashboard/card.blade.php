<div {{ $attributes->merge(['class' => '
    relative group bg-gradient-to-br from-slate-600 to-slate-700
    border border-slate-600 rounded-2xl shadow-lg p-6 flex flex-col
    items-center justify-center transition-transform hover:scale-105
    hover:shadow-[0_0_24px_rgba(125,211,252,0.5)] overflow-hidden
']) }}>
  {{-- SVG abstrato de background --}}
  <svg class="absolute -top-16 -right-16 w-56 opacity-10 rotate-45 pointer-events-none">
    <circle cx="128" cy="128" r="128" fill="url(#gradSoft)" />
    <defs>
      <linearGradient id="gradSoft" x1="0%" y1="0%" x2="100%" y2="100%">
        <stop offset="0%" stop-color="#7DD3FC" stop-opacity="0.1"/>
        <stop offset="100%" stop-color="#C4B5FD" stop-opacity="0.1"/>
      </linearGradient>
    </defs>
  </svg>

  {{-- Ícone Lottie --}}
  <lottie-player
    src="/lottie/metrics-icon.json"
    background="transparent"
    speed="1"
    loop
    autoplay
    class="w-10 h-10 mb-3 opacity-0 group-hover:opacity-80 transition-opacity"
  ></lottie-player>

  <h3 class="text-lg font-bold text-slate-100 mb-1">{{ $title }}</h3>
  <span class="text-3xl font-extrabold text-sky-300">{{ $value }}</span>
</div>