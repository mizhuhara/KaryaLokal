import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        screens: {
            'xs': '475px',
            ...defaultTheme.screens,
        },
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                kl: {
                    primary: '#E8531D',
                    'primary-dark': '#C4401A',
                    'primary-light': '#FF7043',
                    secondary: '#2D6A4F',
                    accent: '#F4A261',
                    warm: '#FFFBF7',
                    border: '#F0E8E0',
                },
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            boxShadow: {
                'kl': '0 4px 24px rgba(232, 83, 29, 0.08)',
                'kl-lg': '0 8px 40px rgba(232, 83, 29, 0.14)',
            },
        },
    },

    plugins: [forms],
};
