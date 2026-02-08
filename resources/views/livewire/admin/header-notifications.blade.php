<div x-data="{ open: false }" class="relative">
    <button 
        @click="open = !open"
        class="relative p-2 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-all duration-200"
    >
        <i class="fas fa-bell"></i>
        @if($notifications->count() > 0)
            <span class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center border-2 border-primary shadow-sm">
                {{ $notifications->count() }}
            </span>
        @endif
    </button>
    
    {{-- Dropdown Notificações --}}
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
        x-cloak
    >
        <div class="p-4 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
            <h4 class="text-xs font-black uppercase tracking-widest text-gray-400">Notificações</h4>
            @if($notifications->count() > 0)
                <button wire:click="markAllAsRead" class="text-[10px] font-bold text-primary hover:underline">Marcar todas como lidas</button>
            @endif
        </div>
        
        <div class="max-h-96 overflow-y-auto custom-scrollbar">
            @forelse($notifications as $notification)
                <div class="p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors flex gap-3 items-start relative group">
                    <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }}"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <p class="text-xs font-bold text-gray-700 leading-tight">{{ $notification->data['title'] ?? 'Notificação' }}</p>
                        <p class="text-[10px] text-gray-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                        <p class="text-[9px] text-gray-400 mt-1 uppercase font-bold">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <button 
                        wire:click="markAsRead('{{ $notification->id }}')"
                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-300 hover:text-primary transition-all"
                        title="Marcar como lida"
                    >
                        <i class="fas fa-check"></i>
                    </button>
                </div>
            @empty
                <div class="py-12 px-4 text-center">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-200">
                        <i class="fas fa-bell-slash text-xl"></i>
                    </div>
                    <p class="text-xs text-gray-400 italic font-medium">Você não tem novas notificações.</p>
                </div>
            @endforelse
        </div>

        @if($notifications->count() > 0)
            <div class="p-3 bg-gray-50/50 text-center border-t border-gray-50">
                <a href="#" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-primary transition-colors">Ver histórico completo</a>
            </div>
        @endif
    </div>
</div>
