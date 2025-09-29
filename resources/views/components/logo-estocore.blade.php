<!-- resources/views/components/logo-estocore.blade.php -->
<svg viewBox="0 0 60 60" width="60" height="60" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-[0_2px_16px_#23F6F899]">
  <!-- Fundo gradiente circular -->
  <defs>
    <radialGradient id="bg" cx="50%" cy="50%" r="60%" fx="50%" fy="50%">
      <stop offset="0%" stop-color="#232046"/>
      <stop offset="100%" stop-color="#19172c"/>
    </radialGradient>
    <linearGradient id="neon" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#23F6F8"/>
      <stop offset="100%" stop-color="#6648E0"/>
    </linearGradient>
    <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
      <feGaussianBlur stdDeviation="2.5" result="coloredBlur"/>
      <feMerge>
        <feMergeNode in="coloredBlur"/>
        <feMergeNode in="SourceGraphic"/>
      </feMerge>
    </filter>
  </defs>
  <rect x="3" y="3" width="54" height="54" rx="14" fill="url(#bg)" stroke="url(#neon)" stroke-width="3" filter="url(#glow)"/>
  <!-- Letra E gótica (Fraktur) -->
  <text x="16" y="43" font-size="32" font-family="'UnifrakturCook', serif" font-weight="700" fill="url(#neon)">E</text>
  <!-- Letra C semiaberta (circuito) -->
  <path d="M48,30 a18,18 0 1,1 -8,-15" stroke="url(#neon)" stroke-width="3" fill="none" filter="url(#glow)"/>
  <!-- LED animado -->
  <circle cx="51" cy="14" r="2.3" fill="#23F6F8">
    <animate attributeName="opacity" values="1;0.25;1" dur="1.2s" repeatCount="indefinite"/>
  </circle>
</svg>