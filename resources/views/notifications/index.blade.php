<x-app-layout>
    @section('title', 'Notificações')
    @section('header-title', 'Minhas Notificações')

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <x-ui.card>
                <div class="flex flex-col md:flex-row justify-between items-center gap-3 mb-5">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                            Histórico de <span class="text-primary ml-2 italic text-2xl">Notificações</span>
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Todas as suas notificações, das mais recentes para as mais antigas.</p>
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse($notifications as $notification)
                        <div class="flex items-center px-3 py-2.5 rounded-lg {{ is_null($notification->read_at) ? 'bg-primary/5 border border-primary/20' : 'bg-gray-50 dark:bg-gray-900/50 border border-transparent' }} gap-3">
                            <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }} text-sm"></i>
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-200 leading-tight">{{ $notification->data['title'] ?? 'Notificação' }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold shrink-0">{{ $notification->created_at->diffForHumans() }}</span>
                            @if(is_null($notification->read_at))
                                <span class="text-[10px] font-black uppercase text-primary shrink-0">Nova</span>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                            <i class="fas fa-bell-slash text-2xl opacity-20 mb-2"></i>
                            <span class="text-xs font-bold uppercase tracking-widest opacity-50">Nenhuma notificação</span>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
