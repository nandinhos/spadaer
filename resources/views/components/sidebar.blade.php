<div
    :class="{'w-64': sidebarOpen, 'w-16': !sidebarOpen}"
    class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out flex flex-col h-full">
    
    <!-- Logo Section -->
    <div class="p-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            <span x-show="sidebarOpen" class="ml-2 text-xl font-semibold text-gray-800 dark:text-gray-200 transition-opacity duration-300">
                {{ config('app.name', 'Laravel') }}
            </span>
        </a>
        <button
            @click="sidebarOpen = !sidebarOpen; localStorage.setItem('sidebarOpen', sidebarOpen)"
            class="p-1 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none">
            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-2 py-4 space-y-1">
        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Dashboard</span>
        </a>

        <!-- Documents Menu -->
        <a href="{{ route('documents.index') }}"
           class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('documents.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Documentos</span>
        </a>

        <!-- Create Document -->
        <a href="{{ route('documents.create') }}"
           class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('documents.create') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Novo Documento</span>
        </a>

        <!-- Profile Link -->
        <a href="{{ route('profile.edit') }}"
           class="flex items-center px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg {{ request()->routeIs('profile.edit') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Perfil</span>
        </a>
    </nav>

    <!-- User Info & Logout -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-4">
        <div class="flex items-center" x-show="sidebarOpen">
            <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="sidebarOpen" class="ml-3 transition-opacity duration-300">Sair</span>
            </button>
        </form>
    </div>
</div>