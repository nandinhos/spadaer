<div>
    <x-ui.card>
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Logs de <span class="text-primary">Auditoria</span>
            </h2>
            
            <div class="flex items-center gap-2">
                <select wire:model.live="event" class="text-sm border-none bg-gray-50 dark:bg-gray-800/50 rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-gray-300">
                    <option value="">Principais Movimentações</option>
                    <option value="created">Criação</option>
                    <option value="updated">Edição</option>
                    <option value="deleted">Exclusão</option>
                </select>
                
                <select wire:model.live="perPage" class="text-sm border-none bg-gray-50 dark:bg-gray-800/50 rounded-xl focus:ring-2 focus:ring-primary/20 dark:text-gray-300">
                    <option value="15">15 por página</option>
                    <option value="30">30 por página</option>
                    <option value="50">50 por página</option>
                </select>
            </div>
        </div>

        <!-- Busca -->
        <div class="mb-6">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400 group-focus-within:text-primary transition-colors"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por usuário, modelo ou ID..." 
                    class="block w-full pl-11 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 dark:text-white transition-all duration-200"
                >
            </div>
        </div>

        <x-ui.table>
            <x-slot name="head">
                <tr>
                    <x-ui.th>Data/Hora</x-ui.th>
                    <x-ui.th>Usuário</x-ui.th>
                    <x-ui.th>Evento</x-ui.th>
                    <x-ui.th>Modelo</x-ui.th>
                    <x-ui.th>ID</x-ui.th>
                    <x-ui.th>Modificações</x-ui.th>
                </tr>
            </x-slot>

            @forelse($logs as $log)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group text-xs">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 dark:text-gray-400">
                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                        {{ $log->user->name ?? 'Sistema/Tinker' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <x-ui.badge :variant="match($log->event) {
                            'created' => 'primary',
                            'updated' => 'warning',
                            'deleted' => 'danger',
                            default => 'gray'
                        }" class="uppercase text-[9px]">
                            {{ $log->event }}
                        </x-ui.badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-300 italic">
                        {{ str_replace('App\\Models\\', '', $log->auditable_type) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-primary">
                        #{{ $log->auditable_id }}
                    </td>
                    <td class="px-6 py-4">
                        @if($log->event === 'updated' || str_ends_with($log->event, '_synced') || str_ends_with($log->event, '_assigned'))
                            <div class="space-y-1">
                                @foreach($log->new_values as $key => $value)
                                    @continue($key === 'updated_at')
                                    <div class="text-[10px]">
                                        <span class="font-bold text-gray-500">{{ $key }}:</span>
                                        @if(isset($log->old_values[$key]))
                                            <span class="text-rose-500 line-through mr-1">{{ is_array($log->old_values[$key]) ? json_encode($log->old_values[$key]) : $log->old_values[$key] }}</span>
                                            <i class="fas fa-arrow-right mx-1 text-gray-300"></i>
                                        @endif
                                        <span class="text-emerald-500">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($log->event === 'created')
                             <span class="text-[10px] text-gray-400">Novo registro criado.</span>
                        @elseif($log->event === 'viewed')
                             <span class="text-[10px] text-blue-400 font-medium italic">Somente visualização.</span>
                        @elseif($log->event === 'deleted')
                             <span class="text-[10px] text-rose-400">Registro removido definitivamente.</span>
                        @else
                             <span class="text-[10px] text-gray-400">Evento registrado: {{ $log->event }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-gray-500 dark:text-gray-400">Nenhum log de auditoria encontrado.</p>
                    </td>
                </tr>
            @endforelse
        </x-ui.table>

        <div class="mt-6">
            {{ $logs->links() }}
        </div>
    </x-ui.card>
</div>
