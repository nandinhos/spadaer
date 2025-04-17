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

        init() { /* ... inicialização ... */ },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        },
        toggleDarkMode() { /* ... */ },
        updateDarkModeClass() { /* ... */ },
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