import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js', // Adicione se seus JS contêm classes Tailwind
    ],

    darkMode: 'class', // Habilitado pelo Breeze

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: { // Adicione suas cores customizadas
                primary: '#5D5CDE',
                'primary-dark': '#4949B3',
                'primary-light': '#7F7EE6',
                // Adicione outras cores customizadas se houver
            },
            keyframes: { // Adicione seus keyframes
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
            animation: { // Adicione suas animações
                fadeIn: 'fadeIn 0.3s ease-in-out',
                slideInLeft: 'slideInLeft 0.3s ease-out',
                pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            }
        },
    },

    plugins: [forms],
};