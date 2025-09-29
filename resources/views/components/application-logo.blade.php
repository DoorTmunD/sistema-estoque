@props(['class' => 'h-12 w-12'])

<svg
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 48 48"
    {{ $attributes->merge(['class' => $class]) }}
    role="img" aria-label="EstoCORE"
>
  <!-- Glow leve -->
  <defs>
    <radialGradient id="g" cx="50%" cy="50%" r="60%">
      <stop offset="0%"  stop-color="#22d3ee" stop-opacity="1"/>
      <stop offset="100%" stop-color="#6366f1" stop-opacity=".85"/>
    </radialGradient>
    <filter id="soft" x="-50%" y="-50%" width="200%" height="200%">
      <feGaussianBlur stdDeviation="2.2" result="blur"/>
      <feMerge>
        <feMergeNode in="blur"/>
        <feMergeNode in="SourceGraphic"/>
      </feMerge>
    </filter>
  </defs>

  <!-- Medalhão -->
  <rect x="4" y="4" width="40" height="40" rx="12"
        fill="url(#g)" opacity=".18" />

  <!-- Escudo -->
  <path filter="url(#soft)"
        d="M24 6c4.7 2.7 8.2 3.1 12 3.7v10.3c0 9.1-6.7 14.3-12 16.5-5.3-2.2-12-7.4-12-16.5V9.7c3.8-.6 7.3-1 12-3.7z"
        fill="none"
        stroke="currentColor"
        class="text-cyan-400 dark:text-cyan-300"
        stroke-width="2"
        />

  <!-- Check -->
  <path filter="url(#soft)"
        d="M18.5 22.5l3.6 3.6 7.4-7.4"
        fill="none"
        stroke="currentColor"
        class="text-emerald-500 dark:text-emerald-400"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2.5"
        />
</svg>
