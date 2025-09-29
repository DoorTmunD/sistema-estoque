<div class="relative" x-data="{ open: false }">
    <button class="relative mr-3 focus:outline-none group" @click="open = !open">
        <svg class="w-6 h-6 text-purple-400 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($notifications->count() > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full px-1 animate-bounce border-2 border-white dark:border-gray-800">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>
    <div 
        x-show="open"
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-900 border border-purple-600/30 rounded-xl shadow-2xl z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="p-3 font-bold border-b border-purple-600/10 text-purple-700 dark:text-purple-300">
            Notificações
        </div>
        <ul>
            @forelse($notifications as $n)
                <li class="border-b border-gray-100 dark:border-gray-800 hover:bg-purple-50 dark:hover:bg-gray-800 transition">
                    <div class="flex items-center p-3">
                        <div class="flex-1">
                            <div class="text-xs text-gray-500">{{ $n->created_at->diffForHumans() }}</div>
                            <div class="font-medium text-gray-700 dark:text-gray-100">{{ $n->data['title'] ?? 'Nova notificação' }}</div>
                            <div class="text-gray-500 dark:text-gray-300 text-sm">{{ $n->data['message'] ?? '' }}</div>
                        </div>
                        <button wire:click="markAsRead('{{ $n->id }}')" class="ml-2 text-xs text-purple-600 hover:underline">
                            Marcar como lida
                        </button>
                    </div>
                </li>
            @empty
                <li>
                    <div class="p-4 text-center text-gray-400 text-sm">Nenhuma notificação nova.</div>
                </li>
            @endforelse
        </ul>
    </div>
</div>