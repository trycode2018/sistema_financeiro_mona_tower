import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/**/*.vue', // 👈 Adicionado da sugestão DeepSeek
    ],

    theme: {
        extend: {
            colors: {
                school: {
                    primary: '#1e40af',   // Azul escuro
                    secondary: '#3b82f6', // Azul médio
                    accent: '#f59e0b',    // Dourado
                    light: '#eff6ff',     // Azul claro
                    dark: '#1e3a8a',      // Azul muito escuro
                },
            },

            fontFamily: {
                // Mantém Figtree e adiciona Inter
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
    ],
};
