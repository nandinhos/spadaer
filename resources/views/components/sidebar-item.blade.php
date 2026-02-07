@props(['href', 'icon', 'label', 'active' => false])

<li>
    <a href="{{ $href }}" wire:navigate
        wire:navigate
        class="group flex items-center px-4 py-2.5 rounded-xl transition-all duration-200 ease-in-out {{ $active 
            ? 'bg-primary text-white shadow-md shadow-primary/20 font-bold' 
            : 'text-gray-500 dark:text-gray-400 hover:bg-primary/5 hover:text-primary' }}"
        :class="sidebarOpen ? '' : 'justify-center px-0'"
    >
        <div class="flex items-center justify-center min-w-[32px]">
            <i class="fa-solid {{ $icon }} text-lg transition-transform duration-200 group-hover:scale-110"></i>
        </div>
        
        <span 
            x-show="sidebarOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-2"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="ml-3 whitespace-nowrap text-sm tracking-tight"
        >
            {{ $label }}
        </span>

        {{-- Tooltip para quando a sidebar estiver fechada --}}
        <div 
            x-show="!sidebarOpen" 
            class="fixed left-20 bg-gray-900 text-white text-[10px] px-2 py-1 rounded shadow-xl pointer-events-none z-50 opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap uppercase tracking-widest font-black"
        >
            {{ $label }}
        </div>
    </a>
</li>