import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // ESTA LÍNEA ES LA QUE HACE QUE EL BOTÓN FUNCIONE
    darkMode: 'class', 

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'observatorio-dark': '#0f172a',
                'observatorio-card': '#1e293b',
                cosmos: {
                    950: '#02040a',
                    primary: '#3b82f6',
                    neon: '#22d3ee',
                    violet: '#7c3aed',
                },
            },
            fontFamily: {
                cinzel: ['Cinzel', ...defaultTheme.fontFamily.serif],
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [forms],
};
