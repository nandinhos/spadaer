<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Documentos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#5D5CDE',
                        'primary-dark': '#4949B3',
                        'primary-light': '#7F7EE6',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideInLeft: {
                            '0%': { transform: 'translateX(-10px)', opacity: '0' },
                            '100%': { transform: 'translateX(0)', opacity: '1' },
                        },
                        pulse: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0.5' },
                        }
                    },
                    animation: {
                        fadeIn: 'fadeIn 0.3s ease-in-out',
                        slideInLeft: 'slideInLeft 0.3s ease-out',
                        pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                },
            },
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-200">
    <div x-data="documentSystem()" class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <div 
            class="flex flex-col bg-gray-100 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 min-h-screen sticky top-0"
            :class="sidebarOpen ? 'w-64' : 'w-16'"
        >
            <!-- Logo -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-box text-primary text-xl"></i>
                    <h2 
                        class="font-bold whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                    >
                        DocBox
                    </h2>
                </div>
                <button 
                    @click="toggleSidebar()" 
                    class="text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary-light"
                >
                    <i 
                        class="fas transition-transform duration-300" 
                        :class="sidebarOpen ? 'fa-chevron-left' : 'fa-chevron-right'"
                    ></i>
                </button>
            </div>
            
            <!-- Menu Items -->
            <nav class="flex-grow py-4 px-2">
                <ul class="space-y-2">
                    <li>
                        <a 
                            href="#" 
                            class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                            :class="{'justify-center': !sidebarOpen}"
                        >
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span 
                                class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                            >
                                Dashboard
                            </span>
                        </a>
                    </li>
                    <li>
                        <a 
                            href="#" 
                            class="flex items-center space-x-2 p-2 rounded-lg bg-primary bg-opacity-10 text-primary dark:text-primary-light"
                            :class="{'justify-center': !sidebarOpen}"
                        >
                            <i class="fas fa-file-alt w-5 text-center"></i>
                            <span 
                                class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                            >
                                Documentos
                            </span>
                        </a>
                    </li>
                    <li>
                        <a 
                            href="#" 
                            class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                            :class="{'justify-center': !sidebarOpen}"
                        >
                            <i class="fas fa-box w-5 text-center"></i>
                            <span 
                                class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                            >
                                Caixas
                            </span>
                        </a>
                    </li>
                    <li>
                        <a 
                            href="#" 
                            class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                            :class="{'justify-center': !sidebarOpen}"
                        >
                            <i class="fas fa-project-diagram w-5 text-center"></i>
                            <span 
                                class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                            >
                                Projetos
                            </span>
                        </a>
                    </li>
                    <li>
                        <a 
                            href="#" 
                            class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                            :class="{'justify-center': !sidebarOpen}"
                        >
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span 
                                class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                            >
                                Configurações
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <!-- Perfil do Usuário -->
            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                <div 
                    class="flex items-center space-x-2 p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700"
                    :class="{'justify-center': !sidebarOpen}"
                >
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white">
                        <i class="fas fa-user"></i>
                    </div>
                    <div 
                        class="whitespace-nowrap overflow-hidden transition-opacity duration-300" 
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'"
                    >
                        <p class="font-medium text-sm">Admin</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Administrador</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="flex flex-col flex-grow min-h-screen w-full">
            <!-- Cabeçalho -->
            <header class="bg-primary dark:bg-primary-dark text-white p-4 shadow-md">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Sistema de Gerenciamento de Documentos</h1>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <button class="text-white flex items-center space-x-1">
                                <i class="fas fa-bell"></i>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                            </button>
                        </div>
                        
                        <div class="flex items-center space-x-2">
                            <div class="text-right hidden md:block">
                                <p class="font-medium text-sm">João Silva</p>
                                <p class="text-xs text-gray-200">Administrador</p>
                            </div>
                            <button @click="toggleDarkMode()" class="p-2 text-white hover:text-gray-200 rounded-full">
                                <i x-bind:class="darkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

        <!-- Conteúdo Principal -->
        <main class="flex-grow container mx-auto p-4 md:p-6 overflow-hidden flex flex-col">
            <!-- Filtros Avançados -->
            <div class="mb-6 bg-gray-100 dark:bg-gray-800 rounded-lg p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2 cursor-pointer" @click="showFilters = !showFilters">
                    <h2 class="text-xl font-semibold">Filtros Avançados</h2>
                    <button class="text-primary dark:text-primary-light">
                        <i x-bind:class="showFilters ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
                    </button>
                </div>
                
                <div x-show="showFilters" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Número da Caixa</label>
                        <input 
                            type="text" 
                            x-model="filters.caixa" 
                            @input="applyFilters()"
                            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-base"
                            placeholder="Filtrar por caixa"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Projeto</label>
                        <select 
                            x-model="filters.projeto" 
                            @change="applyFilters()"
                            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-base"
                        >
                            <option value="">Todos os projetos</option>
                            <template x-for="projeto in projetos" :key="projeto">
                                <option :value="projeto" x-text="projeto"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Ano</label>
                        <select 
                            x-model="filters.ano" 
                            @change="applyFilters()"
                            class="w-full p-2 rounded border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-base"
                        >
                            <option value="">Todos os anos</option>
                            <template x-for="ano in anos" :key="ano">
                                <option :value="ano" x-text="ano"></option>
                            </template>
                        </select>
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button 
                            @click="resetFilters()"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                        >
                            Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filtros Ativos -->
            <div x-show="hasActiveFilters" class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6 animate-fadeIn">
                <div class="flex flex-wrap items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-filter text-blue-500 dark:text-blue-300 mr-2"></i>
                        <span class="font-medium text-blue-800 dark:text-blue-200">Filtros Ativos:</span>
                        
                        <div class="flex flex-wrap ml-2 gap-2">
                            <template x-if="filters.caixa">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    Caixa: <span class="font-bold ml-1" x-text="filters.caixa"></span>
                                </span>
                            </template>
                            
                            <template x-if="filters.projeto">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    Projeto: <span class="font-bold ml-1" x-text="filters.projeto"></span>
                                </span>
                            </template>
                            
                            <template x-if="filters.ano">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    Ano: <span class="font-bold ml-1" x-text="filters.ano"></span>
                                </span>
                            </template>
                            
                            <template x-if="searchTerm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">
                                    Pesquisa: <span class="font-bold ml-1" x-text="searchTerm"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    <button 
                        @click="resetFilters()"
                        class="mt-2 sm:mt-0 px-3 py-1 text-sm text-blue-800 dark:text-blue-200 bg-blue-100 dark:bg-blue-800 rounded hover:bg-blue-200 dark:hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                    >
                        <i class="fas fa-times-circle mr-1"></i> Limpar Filtros
                    </button>
                </div>
            </div>
            
            <!-- Estatísticas Rápidas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-primary">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span x-text="hasActiveFilters ? 'Documentos Filtrados' : 'Total de Documentos'"></span>
                        </span>
                        <span class="text-primary dark:text-primary-light"><i class="fas fa-file-alt"></i></span>
                    </div>
                    <div class="flex items-end mt-2">
                        <p class="text-2xl font-bold" x-text="filteredDocuments.length"></p>
                        <p x-show="hasActiveFilters" class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                            de <span x-text="documentos.length"></span>
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-green-500">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span x-text="hasActiveFilters ? 'Caixas Filtradas' : 'Total de Caixas'"></span>
                        </span>
                        <span class="text-green-500"><i class="fas fa-box"></i></span>
                    </div>
                    <div class="flex items-end mt-2">
                        <p class="text-2xl font-bold" x-text="getFilteredCaixas()"></p>
                        <p x-show="hasActiveFilters" class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                            de <span x-text="getTotalCaixas()"></span>
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-yellow-500">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span x-text="hasActiveFilters ? 'Projetos Filtrados' : 'Total de Projetos'"></span>
                        </span>
                        <span class="text-yellow-500"><i class="fas fa-project-diagram"></i></span>
                    </div>
                    <div class="flex items-end mt-2">
                        <p class="text-2xl font-bold" x-text="getFilteredProjetos()"></p>
                        <p x-show="hasActiveFilters" class="text-sm text-gray-500 dark:text-gray-400 ml-2">
                            de <span x-text="projetos.length"></span>
                        </p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 border-l-4 border-blue-500">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            <span x-text="hasActiveFilters ? 'Intervalo de Anos Filtrados' : 'Intervalo de Anos'"></span>
                        </span>
                        <span class="text-blue-500"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <div class="flex items-end mt-2">
                        <p class="text-2xl font-bold" x-text="getYearRange(filteredDocuments)"></p>
                    </div>
                </div>
            </div>

            <!-- Resultados da Pesquisa -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow flex-grow overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
                        <h2 class="text-xl font-semibold">
                            Documentos 
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400" x-text="`(${filteredDocuments.length} de ${documentos.length})`"></span>
                        </h2>
                        
                        <!-- Campo de Pesquisa -->
                        <div class="relative flex-grow max-w-md">
                            <input 
                                type="text" 
                                x-model="searchTerm" 
                                @input="searchDocuments()"
                                placeholder="Pesquisar documentos..." 
                                class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 dark:bg-gray-700 text-base focus:outline-none focus:ring-2 focus:ring-primary-light"
                            >
                            <button 
                                @click="searchTerm = ''; searchDocuments()" 
                                x-show="searchTerm" 
                                class="absolute right-10 top-2.5 text-gray-500 dark:text-gray-400 hover:text-primary"
                            >
                                <i class="fas fa-times-circle"></i>
                            </button>
                            <span class="absolute right-3 top-2.5 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        
                        <div>
                            <label class="text-sm mr-2">Documentos por página:</label>
                            <select 
                                x-model="itemsPerPage" 
                                @change="currentPage = 1"
                                class="p-1 border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700"
                            >
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Botões de Ações Rápidas -->
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button class="px-3 py-1.5 bg-primary dark:bg-primary-dark text-white rounded text-sm flex items-center hover:bg-primary-dark">
                            <i class="fas fa-plus-circle mr-1.5"></i> Adicionar
                        </button>
                        <button class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded text-sm flex items-center hover:bg-gray-300 dark:hover:bg-gray-600">
                            <i class="fas fa-file-export mr-1.5"></i> Exportar
                        </button>
                        <button class="px-3 py-1.5 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded text-sm flex items-center hover:bg-gray-300 dark:hover:bg-gray-600">
                            <i class="fas fa-print mr-1.5"></i> Imprimir
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto flex-grow">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <template x-for="(column, index) in columns" :key="index">
                                    <th 
                                        scope="col" 
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-650"
                                        @click="sortBy(column.key)"
                                    >
                                        <div class="flex items-center space-x-1">
                                            <span x-text="column.label"></span>
                                            <span x-show="sortColumn === column.key">
                                                <i 
                                                    class="fas" 
                                                    :class="sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down'"
                                                ></i>
                                            </span>
                                        </div>
                                    </th>
                                </template>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(documento, index) in paginatedDocuments" :key="index">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.caixa"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.item"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.codigo"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.descritor"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.numero"></td>
                                    <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate" x-text="documento.titulo"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="formatDate(documento.data)"></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': documento.sigilo === 'Confidencial',
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': documento.sigilo === 'Restrito',
                                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': documento.sigilo === 'Público'
                                            }"
                                            x-text="documento.sigilo"
                                        ></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.versao"></td>
                                    <td class="px-6 py-4 whitespace-nowrap" x-text="documento.copia ? 'Sim' : 'Não'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button 
                                            @click="openDocumentModal(documento)"
                                            class="text-primary dark:text-primary-light hover:text-primary-dark"
                                        >
                                            <i class="fas fa-eye"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredDocuments.length === 0">
                                <td colspan="11" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Nenhum documento encontrado. Tente ajustar os filtros.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Mostrando
                                <span class="font-medium" x-text="paginatedDocuments.length > 0 ? (currentPage - 1) * itemsPerPage + 1 : 0"></span>
                                a
                                <span class="font-medium" x-text="Math.min(currentPage * itemsPerPage, filteredDocuments.length)"></span>
                                de
                                <span class="font-medium" x-text="filteredDocuments.length"></span>
                                resultados
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <button
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    :disabled="currentPage === 1"
                                    :class="{'opacity-50 cursor-not-allowed': currentPage === 1}"
                                    @click="currentPage = Math.max(1, currentPage - 1)"
                                >
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <template x-for="page in getVisiblePages()" :key="page">
                                    <button
                                        @click="currentPage = page"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600"
                                        :class="currentPage === page ? 'z-10 bg-primary text-white dark:bg-primary-dark border-primary dark:border-primary-dark' : 'text-gray-500 dark:text-gray-300'"
                                        x-text="page"
                                    ></button>
                                </template>
                                <button
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    :disabled="currentPage === totalPages"
                                    :class="{'opacity-50 cursor-not-allowed': currentPage === totalPages}"
                                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                                >
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal de Detalhes do Documento -->
        <div
            x-show="showModal"
            @keydown.escape.window="showModal = false"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity"
                    @click="showModal = false"
                >
                    <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                
                <div
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
                    @click.away="showModal = false"
                >
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-light bg-opacity-20 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                                    Detalhes do Documento
                                </h3>
                                <div class="mt-4 space-y-3">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Caixa</p>
                                            <p class="font-medium" x-text="selectedDocument.caixa"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Item</p>
                                            <p class="font-medium" x-text="selectedDocument.item"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Código</p>
                                            <p class="font-medium" x-text="selectedDocument.codigo"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Descritor</p>
                                            <p class="font-medium" x-text="selectedDocument.descritor"></p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Número do Documento</p>
                                            <p class="font-medium" x-text="selectedDocument.numero"></p>
                                        </div>
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Título</p>
                                            <p class="font-medium" x-text="selectedDocument.titulo"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Data</p>
                                            <p class="font-medium" x-text="formatDate(selectedDocument.data)"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Projeto</p>
                                            <p class="font-medium" x-text="selectedDocument.projeto"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Sigilo</p>
                                            <p>
                                                <span
                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                    :class="{
                                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': selectedDocument.sigilo === 'Confidencial',
                                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': selectedDocument.sigilo === 'Restrito',
                                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': selectedDocument.sigilo === 'Público'
                                                    }"
                                                    x-text="selectedDocument.sigilo"
                                                ></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Versão</p>
                                            <p class="font-medium" x-text="selectedDocument.versao"></p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Cópia</p>
                                            <p class="font-medium" x-text="selectedDocument.copia ? 'Sim' : 'Não'"></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Localização Física</p>
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-box text-primary"></i>
                                            <p class="font-medium">Caixa <span x-text="selectedDocument.caixa"></span> - Item <span x-text="selectedDocument.item"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button 
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            @click="showModal = false"
                        >
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function documentSystem() {
            return {
                searchTerm: '',
                showFilters: false,
                sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false', // Por padrão a sidebar fica aberta
                darkMode: localStorage.getItem('darkMode') === 'true' || (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches),
                filters: {
                    caixa: '',
                    projeto: '',
                    ano: ''
                },
                sortColumn: 'caixa',
                sortDirection: 'asc',
                currentPage: 1,
                itemsPerPage: 10,
                showModal: false,
                selectedDocument: {},
                documentos: [
                    // Dados de exemplo
                    { caixa: '001', item: '01', codigo: 'ADM-001', descritor: 'Administrativo', numero: 'DOC2023-001', titulo: 'Relatório Anual de Atividades', data: '2023-12-15', projeto: 'Gestão Corporativa', sigilo: 'Público', versao: '1.0', copia: false },
                    { caixa: '001', item: '02', codigo: 'ADM-002', descritor: 'Administrativo', numero: 'DOC2023-002', titulo: 'Plano Estratégico 2023', data: '2023-01-10', projeto: 'Gestão Corporativa', sigilo: 'Restrito', versao: '2.1', copia: false },
                    { caixa: '001', item: '03', codigo: 'FIN-001', descritor: 'Financeiro', numero: 'DOC2023-010', titulo: 'Balancete Mensal - Janeiro', data: '2023-02-05', projeto: 'Finanças', sigilo: 'Restrito', versao: '1.0', copia: false },
                    { caixa: '002', item: '01', codigo: 'RH-001', descritor: 'Recursos Humanos', numero: 'DOC2023-015', titulo: 'Política de Contratação', data: '2023-03-20', projeto: 'RH', sigilo: 'Público', versao: '3.0', copia: true },
                    { caixa: '002', item: '02', codigo: 'RH-002', descritor: 'Recursos Humanos', numero: 'DOC2023-016', titulo: 'Manual do Funcionário', data: '2023-04-12', projeto: 'RH', sigilo: 'Público', versao: '2.5', copia: false },
                    { caixa: '002', item: '03', codigo: 'RH-003', descritor: 'Recursos Humanos', numero: 'DOC2022-050', titulo: 'Relatório de Desempenho Anual', data: '2022-12-10', projeto: 'RH', sigilo: 'Confidencial', versao: '1.0', copia: false },
                    { caixa: '003', item: '01', codigo: 'PROJ-001', descritor: 'Projetos', numero: 'DOC2022-100', titulo: 'Proposta Técnica - Expansão', data: '2022-06-15', projeto: 'Expansão Norte', sigilo: 'Confidencial', versao: '1.0', copia: false },
                    { caixa: '003', item: '02', codigo: 'PROJ-002', descritor: 'Projetos', numero: 'DOC2022-101', titulo: 'Cronograma de Implementação', data: '2022-07-01', projeto: 'Expansão Norte', sigilo: 'Restrito', versao: '2.0', copia: true },
                    { caixa: '003', item: '03', codigo: 'PROJ-003', descritor: 'Projetos', numero: 'DOC2022-102', titulo: 'Análise de Riscos', data: '2022-07-15', projeto: 'Expansão Norte', sigilo: 'Restrito', versao: '1.5', copia: false },
                    { caixa: '004', item: '01', codigo: 'TEC-001', descritor: 'Tecnologia', numero: 'DOC2021-200', titulo: 'Especificações Técnicas - Sistema ERP', data: '2021-11-10', projeto: 'Modernização TI', sigilo: 'Público', versao: '1.0', copia: false },
                    { caixa: '004', item: '02', codigo: 'TEC-002', descritor: 'Tecnologia', numero: 'DOC2021-201', titulo: 'Plano de Migração de Dados', data: '2021-12-05', projeto: 'Modernização TI', sigilo: 'Restrito', versao: '1.2', copia: false },
                    { caixa: '005', item: '01', codigo: 'JUR-001', descritor: 'Jurídico', numero: 'DOC2020-300', titulo: 'Contrato de Prestação de Serviços', data: '2020-05-20', projeto: 'Jurídico', sigilo: 'Confidencial', versao: '1.0', copia: true },
                    { caixa: '005', item: '02', codigo: 'JUR-002', descritor: 'Jurídico', numero: 'DOC2020-301', titulo: 'Parecer Legal - Propriedade Intelectual', data: '2020-06-15', projeto: 'Jurídico', sigilo: 'Confidencial', versao: '1.0', copia: false },
                    { caixa: '005', item: '03', codigo: 'JUR-003', descritor: 'Jurídico', numero: 'DOC2020-302', titulo: 'Política de Compliance', data: '2020-07-01', projeto: 'Jurídico', sigilo: 'Público', versao: '2.0', copia: false },
                    { caixa: '006', item: '01', codigo: 'MKT-001', descritor: 'Marketing', numero: 'DOC2022-400', titulo: 'Plano de Marketing 2022', data: '2022-01-15', projeto: 'Marketing Digital', sigilo: 'Restrito', versao: '1.0', copia: false },
                    { caixa: '006', item: '02', codigo: 'MKT-002', descritor: 'Marketing', numero: 'DOC2022-401', titulo: 'Análise de Mercado', data: '2022-02-10', projeto: 'Marketing Digital', sigilo: 'Público', versao: '1.0', copia: true },
                    { caixa: '006', item: '03', codigo: 'MKT-003', descritor: 'Marketing', numero: 'DOC2022-402', titulo: 'Estratégia de Mídias Sociais', data: '2022-03-05', projeto: 'Marketing Digital', sigilo: 'Público', versao: '2.1', copia: false },
                    { caixa: '007', item: '01', codigo: 'OPS-001', descritor: 'Operações', numero: 'DOC2021-500', titulo: 'Manual de Processos', data: '2021-05-10', projeto: 'Otimização', sigilo: 'Público', versao: '3.0', copia: false },
                    { caixa: '007', item: '02', codigo: 'OPS-002', descritor: 'Operações', numero: 'DOC2021-501', titulo: 'Protocolo de Segurança', data: '2021-06-15', projeto: 'Otimização', sigilo: 'Restrito', versao: '1.5', copia: false },
                    { caixa: '007', item: '03', codigo: 'OPS-003', descritor: 'Operações', numero: 'DOC2021-502', titulo: 'Relatório de Produtividade', data: '2021-07-01', projeto: 'Otimização', sigilo: 'Confidencial', versao: '1.0', copia: true }
                ],
                filteredDocuments: [],
                columns: [
                    { key: 'caixa', label: 'Caixa' },
                    { key: 'item', label: 'Item' },
                    { key: 'codigo', label: 'Código' },
                    { key: 'descritor', label: 'Descritor' },
                    { key: 'numero', label: 'Número' },
                    { key: 'titulo', label: 'Título' },
                    { key: 'data', label: 'Data' },
                    { key: 'sigilo', label: 'Sigilo' },
                    { key: 'versao', label: 'Versão' },
                    { key: 'copia', label: 'Cópia' }
                ],
                
                init() {
                    // Inicializar o modo escuro com base na preferência do sistema
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    
                    // Inicializar dados
                    this.filteredDocuments = [...this.documentos];
                    
                    // Detectar mudanças na preferência de tema do sistema
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                        this.darkMode = event.matches;
                        this.updateDarkMode();
                    });
                },
                
                // Método para alternar entre modo claro e escuro
                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                    this.updateDarkMode();
                },
                
                // Método para alternar a sidebar
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                    localStorage.setItem('sidebarOpen', this.sidebarOpen);
                },
                
                updateDarkMode() {
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                },
                
                // Obter lista única de projetos para o filtro
                get projetos() {
                    return [...new Set(this.documentos.map(doc => doc.projeto))].sort();
                },
                
                // Obter lista única de anos para o filtro
                get anos() {
                    return [...new Set(this.documentos.map(doc => new Date(doc.data).getFullYear()))].sort();
                },
                
                // Total de páginas baseado nos itens filtrados
                get totalPages() {
                    return Math.ceil(this.filteredDocuments.length / this.itemsPerPage);
                },
                
                // Obter documentos da página atual
                get paginatedDocuments() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredDocuments.slice(start, end);
                },
                
                // Verificar se existem filtros ativos
                get hasActiveFilters() {
                    return this.searchTerm || this.filters.caixa || this.filters.projeto || this.filters.ano;
                },
                
                // Obter número total de caixas únicas
                getTotalCaixas() {
                    return new Set(this.documentos.map(doc => doc.caixa)).size;
                },
                
                // Obter número de caixas nos documentos filtrados
                getFilteredCaixas() {
                    return new Set(this.filteredDocuments.map(doc => doc.caixa)).size;
                },
                
                // Obter número de projetos nos documentos filtrados
                getFilteredProjetos() {
                    return new Set(this.filteredDocuments.map(doc => doc.projeto)).size;
                },
                
                // Obter intervalo de anos para um conjunto de documentos
                getYearRange(docs) {
                    if (docs.length === 0) return '—';
                    const years = docs.map(doc => new Date(doc.data).getFullYear());
                    const minYear = Math.min(...years);
                    const maxYear = Math.max(...years);
                    return minYear === maxYear ? `${minYear}` : `${minYear} - ${maxYear}`;
                },
                
                // Filtrar documentos com base na pesquisa e nos filtros
                searchDocuments() {
                    // Resetar para a primeira página ao pesquisar
                    this.currentPage = 1;
                    this.applyFilters();
                },
                
                // Aplicar filtros aos documentos
                applyFilters() {
                    // Resetar para a primeira página ao aplicar filtros
                    this.currentPage = 1;
                    
                    // Aplicar filtros e pesquisa
                    this.filteredDocuments = this.documentos.filter(doc => {
                        // Verificar termo de pesquisa
                        const searchMatch = !this.searchTerm || 
                            Object.values(doc).some(value => 
                                value && value.toString().toLowerCase().includes(this.searchTerm.toLowerCase())
                            );
                        
                        // Verificar filtro de caixa
                        const caixaMatch = !this.filters.caixa || 
                            doc.caixa.toLowerCase().includes(this.filters.caixa.toLowerCase());
                        
                        // Verificar filtro de projeto
                        const projetoMatch = !this.filters.projeto || 
                            doc.projeto === this.filters.projeto;
                        
                        // Verificar filtro de ano
                        const anoMatch = !this.filters.ano || 
                            new Date(doc.data).getFullYear().toString() === this.filters.ano;
                        
                        return searchMatch && caixaMatch && projetoMatch && anoMatch;
                    });
                    
                    // Aplicar ordenação
                    this.sortDocuments();
                },
                
                // Resetar todos os filtros
                resetFilters() {
                    this.filters = {
                        caixa: '',
                        projeto: '',
                        ano: ''
                    };
                    this.searchTerm = '';
                    this.applyFilters();
                },
                
                // Ordenar documentos
                sortBy(column) {
                    if (this.sortColumn === column) {
                        // Inverte a direção se a mesma coluna for clicada novamente
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        // Define a nova coluna e a direção padrão
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                    
                    this.sortDocuments();
                },
                
                // Aplicar ordenação com base na coluna e direção atual
                sortDocuments() {
                    this.filteredDocuments.sort((a, b) => {
                        let valueA = a[this.sortColumn];
                        let valueB = b[this.sortColumn];
                        
                        // Ordenar datas corretamente
                        if (this.sortColumn === 'data') {
                            valueA = new Date(valueA);
                            valueB = new Date(valueB);
                        }
                        
                        // Ordenar valores numéricos corretamente
                        if (typeof valueA === 'string' && !isNaN(valueA) && typeof valueB === 'string' && !isNaN(valueB)) {
                            valueA = Number(valueA);
                            valueB = Number(valueB);
                        }
                        
                        if (valueA < valueB) {
                            return this.sortDirection === 'asc' ? -1 : 1;
                        }
                        if (valueA > valueB) {
                            return this.sortDirection === 'asc' ? 1 : -1;
                        }
                        return 0;
                    });
                },
                
                // Abrir modal de detalhes do documento
                openDocumentModal(document) {
                    this.selectedDocument = { ...document };
                    this.showModal = true;
                },
                
                // Formatar data para exibição
                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('pt-BR');
                },
                
                // Calcular páginas visíveis para paginação
                getVisiblePages() {
                    const delta = 2; // Número de páginas para mostrar antes e depois da página atual
                    const range = [];
                    const maxPages = 7; // Número máximo de páginas para mostrar
                    
                    if (this.totalPages <= maxPages) {
                        // Mostrar todas as páginas se tivermos menos que o máximo
                        for (let i = 1; i <= this.totalPages; i++) {
                            range.push(i);
                        }
                    } else {
                        // Sempre incluir a primeira página
                        range.push(1);
                        
                        // Calcular a faixa de páginas visíveis
                        const leftOffset = Math.max(1, this.currentPage - delta);
                        const rightOffset = Math.min(this.totalPages, this.currentPage + delta);
                        
                        // Adicionar ellipsis após a primeira página, se necessário
                        if (leftOffset > 2) {
                            range.push('...');
                        }
                        
                        // Adicionar páginas no intervalo
                        for (let i = leftOffset; i <= rightOffset; i++) {
                            if (i !== 1 && i !== this.totalPages) {
                                range.push(i);
                            }
                        }
                        
                        // Adicionar ellipsis antes da última página, se necessário
                        if (rightOffset < this.totalPages - 1) {
                            range.push('...');
                        }
                        
                        // Sempre incluir a última página
                        if (this.totalPages > 1) {
                            range.push(this.totalPages);
                        }
                    }
                    
                    return range;
                }
            };
        }
        
        // Detectar preferência de tema do sistema
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
</body>
</html>