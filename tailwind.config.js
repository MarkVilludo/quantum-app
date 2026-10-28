import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/flowbite/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Matches QuantumRealm app/res/values/colors.xml
                quantum: {
                    'bg-dark': '#0B1020',
                    'bg-mid': '#141B2D',
                    surface: '#1E293B',
                    'surface-up': '#243047',
                    primary: '#6366F1',
                    'primary-dark': '#4F46E5',
                    accent: '#22D3EE',
                    success: '#22C55E',
                    error: '#EF4444',
                    warning: '#F59E0B',
                    gold: '#FBBF24',
                    stroke: '#334155',
                    muted: '#64748B',
                    secondary: '#94A3B8',
                    'text-primary': '#F8FAFC',
                },
                brand: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    500: '#6366F1',
                    600: '#4F46E5',
                    700: '#4338ca',
                },
            },
        },
    },

    plugins: [forms, flowbite],
};
