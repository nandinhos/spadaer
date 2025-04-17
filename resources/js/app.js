// resources/js/app.js
import './bootstrap';
import Alpine from 'alpinejs';

// Sua função layout() definida aqui
function layout() {
    return {
        // ... (todo o conteúdo da função layout() que definimos antes)
        sidebarOpen: localStorage.getItem('sidebarOpen') ? localStorage.getItem('sidebarOpen') === 'true' : true,
        darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        showFilters: false,
        showModal: false,
        selectedDocument: {},
        loadingModal: false,

        init() {
            this.updateDarkModeClass(); // Apply dark mode on initial load
            // Add watcher for darkMode changes
            this.$watch('darkMode', (value) => {
                localStorage.setItem('darkMode', value);
                this.updateDarkModeClass();
            });
            // Add watcher for sidebarOpen changes
            this.$watch('sidebarOpen', (value) => {
                localStorage.setItem('sidebarOpen', value);
            });
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            // localStorage update is handled by the watcher now
        },
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            // localStorage update and class update are handled by the watcher
        },
        updateDarkModeClass() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        async openDocumentModal(documentId) { /* ... lógica AJAX ... */ },
        closeModal() { /* ... */ },
        formatDate(dateString) { /* ... */ },
        // getSortUrl e getClearFiltersUrl podem ser removidos se você gerar as URLs no Blade
    }
}

// Registrar o componente Alpine globalmente
document.addEventListener('alpine:init', () => {
    Alpine.data('layout', layout);
});

window.Alpine = Alpine;
Alpine.start();