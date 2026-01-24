import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    dark: '#030303',
                    gray: '#121212',
                    surface: '#1E1E1E',
                    primary: '#7C3AED', // Violet 600
                    secondary: '#EC4899', // Pink 500
                    accent: '#06B6D4', // Cyan 500
                }
            }
        },
    },

    plugins: [forms],
};
