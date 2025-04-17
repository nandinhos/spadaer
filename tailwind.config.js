import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js', // Adicione se usar Alpine/Vue/React em arquivos JS
    ],

    darkMode: 'class', // Breeze já configura isso

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: { // Adicione ou modifique esta seção
                primary: '#5D5CDE',
                primaryDark: '#4A49B8', // Você pode usar isso para hover:bg-primaryDark
                secondary: '#3F3F46',
                accent: '#0066cc',
                orange: colors.orange,
                // Cores padrão do Breeze/Tailwind ainda estarão disponíveis
                // Ex: gray, red, green, etc.
            },
        },
    },

    plugins: [forms],
};