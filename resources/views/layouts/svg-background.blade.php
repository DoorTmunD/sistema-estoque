<svg viewBox="0 0 900 1200" width="100%" height="100%" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
  <defs>
    <linearGradient id="fadeBlue" x1="0" y1="0" x2="900" y2="1200" gradientUnits="userSpaceOnUse">
      <stop stop-color="#A5B4FC"/>
      <stop offset="1" stop-color="#93C5FD"/>
    </linearGradient>
    <filter id="softglow" x="-30%" y="-30%" width="160%" height="160%">
      <feGaussianBlur stdDeviation="14" result="blur"/>
      <feMerge>
        <feMergeNode in="blur"/>
        <feMergeNode in="SourceGraphic"/>
      </feMerge>
    </filter>
    <style>
      .nn-link { stroke: url(#fadeBlue); stroke-width: 2.2; opacity: .13; filter: url(#softglow);}
      .nn-dot  { fill: url(#fadeBlue); filter: url(#softglow); opacity: .21;}
    </style>
  </defs>
  <polyline class="nn-link" points="120,200 240,340 420,160 680,200 800,320 600,600 260,420 160,880"/>
  <polyline class="nn-link" points="300,900 200,600 620,440 780,700 430,1040"/>
  <polyline class="nn-link" points="780,110 560,330 620,570 750,900 580,1100"/>
  <polyline class="nn-link" points="200,150 320,440 330,660 100,1100"/>
  <polyline class="nn-link" points="340,300 640,300 460,900 600,1060"/>
  <circle class="nn-dot" cx="120" cy="200" r="11"/>
  <circle class="nn-dot" cx="800" cy="320" r="8"/>
  <circle class="nn-dot" cx="340" cy="300" r="7"/>
  <circle class="nn-dot" cx="780" cy="700" r="9"/>
  <circle class="nn-dot" cx="100" cy="1100" r="11"/>
  <circle class="nn-dot" cx="580" cy="1100" r="8"/>
  <circle class="nn-dot" cx="430" cy="1040" r="7"/>
</svg>
