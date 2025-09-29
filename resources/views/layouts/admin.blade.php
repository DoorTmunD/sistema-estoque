<!DOCTYPE html>
<html lang="pt-BR" class="h-full antialiased">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Painel Administrativo | {{ config('app.name') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
  @stack('styles')

  <!-- FontAwesome (cdn livre) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" crossorigin="anonymous" defer></script>

  <style>
    [x-cloak]{display:none!important}
    .theme-transition { transition: background-color .5s cubic-bezier(.5,1.4,.5,1), color .5s; }
    .drop-shadow-neon {
      filter: drop-shadow(0 0 6px rgba(35,246,248,.5)) drop-shadow(0 0 12px rgba(102,72,224,.15));
    }
    .sidebar-glass { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
  </style>
</head>
<body class="h-full bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
  <div class="flex h-screen">
    <!-- SIDEBAR -->
    <aside class="relative w-64 flex flex-col bg-gradient-to-b from-gray-100 via-slate-200 to-indigo-100 dark:from-gray-900 dark:via-slate-900 dark:to-indigo-950 sidebar-glass z-20 overflow-hidden">
      <!-- SVG decorativo -->
      <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-35 z-0 select-none" viewBox="0 0 260 900" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <defs>
          <linearGradient id="nn-gradient" x1="0" y1="0" x2="260" y2="900" gradientUnits="userSpaceOnUse">
            <stop stop-color="#0ffcf7"/><stop offset="1" stop-color="#8247f7"/>
          </linearGradient>
          <style>
            .nn-conn { stroke: url(#nn-gradient); stroke-width:2.3; opacity:.45; filter:url(#neonG);}
            .nn-dot  { fill: url(#nn-gradient); filter:url(#neonG);}
            .nn-dot.ani { animation: node-blink 1.5s infinite alternate; }
            @keyframes node-blink { 0%,100%{opacity:1} 50%{opacity:.55} 80%{opacity:.9} }
          </style>
          <filter id="neonG" x="-30%" y="-30%" width="160%" height="160%">
            <feGaussianBlur stdDeviation="6" result="blur"/>
            <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
          </filter>
        </defs>
        <polyline class="nn-conn" points="40,60 80,120 110,190 150,120 180,80 220,180 140,400 80,600 60,850"/>
        <polyline class="nn-conn" points="200,30 170,170 180,400 210,700"/>
        <polyline class="nn-conn" points="90,70 60,180 130,350 170,500 150,700"/>
        <polyline class="nn-conn" points="220,80 170,250 90,480 90,880"/>
        <circle class="nn-dot ani" cx="40" cy="60" r="8"/>
        <circle class="nn-dot ani" cx="80" cy="120" r="5"/>
        <circle class="nn-dot" cx="110" cy="190" r="5"/>
        <circle class="nn-dot ani" cx="150" cy="120" r="7"/>
        <circle class="nn-dot" cx="180" cy="80" r="4"/>
        <circle class="nn-dot ani" cx="220" cy="180" r="8"/>
        <circle class="nn-dot" cx="140" cy="400" r="7"/>
        <circle class="nn-dot" cx="80" cy="600" r="6"/>
        <circle class="nn-dot ani" cx="60" cy="850" r="6"/>
        <circle class="nn-dot" cx="200" cy="30" r="7"/>
        <circle class="nn-dot ani" cx="170" cy="170" r="6"/>
        <circle class="nn-dot" cx="180" cy="400" r="5"/>
        <circle class="nn-dot" cx="210" cy="700" r="7"/>
        <circle class="nn-dot ani" cx="90" cy="70" r="5"/>
        <circle class="nn-dot" cx="60" cy="180" r="4"/>
        <circle class="nn-dot ani" cx="130" cy="350" r="6"/>
        <circle class="nn-dot" cx="170" cy="500" r="6"/>
        <circle class="nn-dot" cx="150" cy="700" r="6"/>
        <circle class="nn-dot ani" cx="220" cy="80" r="6"/>
        <circle class="nn-dot" cx="170" cy="250" r="6"/>
        <circle class="nn-dot" cx="90" cy="480" r="5"/>
        <circle class="nn-dot ani" cx="90" cy="880" r="6"/>
      </svg>

      <!-- LOGO -->
      <div class="flex items-center px-6 py-5 border-b border-indigo-100 dark:border-indigo-800 z-10 relative">
        <x-application-logo class="h-8 w-8 text-cyan-400 drop-shadow-neon"/>
        <span class="ml-2 font-bold text-lg text-cyan-500 drop-shadow-neon select-none">EstoCORE</span>
      </div>

      <!-- NAV -->
      <nav class="flex-1 px-4 py-6 space-y-1 z-10 relative">
        @php
          $items = [
            ['route'=>'admin.dashboard','icon'=>'fa-gauge-high','label'=>'Painel'],
            ['route'=>'admin.users.index','icon'=>'fa-users','label'=>'Usuários'],
            ['route'=>'admin.logs.index','icon'=>'fa-list','label'=>'Logs'],
            ['route'=>'admin.settings','icon'=>'fa-gear','label'=>'Config'],
          ];
        @endphp
        @foreach($items as $item)
          <a href="{{ route($item['route']) }}"
             class="flex items-center px-3 py-2 rounded-lg transition-colors duration-200 font-medium
                    {{ request()->routeIs($item['route'].'*')
                        ? 'bg-indigo-200 dark:bg-indigo-900 text-indigo-900 dark:text-white border-l-4 border-cyan-400 drop-shadow-neon'
                        : 'hover:bg-indigo-100 dark:hover:bg-indigo-800 text-gray-700 dark:text-gray-200' }}">
            <i class="fa-solid {{ $item['icon'] }} w-5"></i>
            <span class="ml-3">{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <!-- FOOTER -->
      <div class="px-6 py-4 border-t border-indigo-100 dark:border-indigo-800 z-10 relative">
        <div class="flex items-center justify-between">
          @php $nivel = auth()->user()->nivel; @endphp
          <span class="inline-flex items-center gap-1 px-3 py-1 text-sm font-bold uppercase rounded shadow-lg select-none
                       {{ $nivel==='super-admin' ? 'bg-indigo-500 text-white'
                          : ($nivel==='admin' ? 'bg-green-600 text-white' : 'bg-gray-600 text-white') }}">
            @if($nivel==='super-admin') <i class="fa-solid fa-crown text-yellow-300"></i>
            @elseif($nivel==='admin')   <i class="fa-solid fa-user-shield text-white"></i>
            @else                       <i class="fa-solid fa-user text-white"></i>
            @endif
            {{ strtoupper(strtok($nivel,'-')) }}
          </span>
          <div class="flex items-center gap-2">
            <a href="{{ route('dashboard') }}"
               class="px-3 py-1 bg-indigo-500 hover:bg-indigo-600 text-white text-sm rounded shadow transition"
               title="Ir para Dashboard">
              <i class="fa-solid fa-home mr-1"></i> Início
            </a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"
                      class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-sm rounded shadow transition"
                      title="Sair">
                <i class="fa-solid fa-right-from-bracket mr-1"></i> Sair
              </button>
            </form>
          </div>
        </div>
      </div>
    </aside>

    <!-- MAIN -->
    <main class="relative flex-1 overflow-y-auto p-8 space-y-6 theme-transition z-10">
      <button
        x-data="{ dark: (localStorage.getItem('dark') === 'true') }"
        @click="
          dark = !dark;
          document.documentElement.classList.toggle('dark', dark);
          localStorage.setItem('dark', dark ? 'true' : 'false');
        "
        :title="dark ? 'Modo Claro' : 'Modo Escuro'"
        class="absolute top-4 right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:shadow-lg transition"
        aria-label="Alternar tema"
      >
        <i x-show="!dark" class="fa-solid fa-moon"></i>
        <i x-show="dark"  class="fa-solid fa-sun"></i>
      </button>

      @yield('content')
    </main>
  </div>

  @livewireScripts
  @livewireScriptConfig(['navigate' => false])

  @stack('scripts')

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>
    // Garantir tema consistente ao carregar (chave: 'dark')
    (function () {
      const saved = localStorage.getItem('dark');
      document.documentElement.classList.toggle('dark', saved === 'true');
    })();
  </script>
</body>
</html>
