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
                    dark: '#17151A',     // Provided Dark
                    gray: '#201E26',     // Derived darker surface
                    surface: '#292730',  // Derived lighter surface
                    primary: '#0052E2',  // Provided Logo Blue
                    secondary: '#26A09E',// Provided Teal
                    accent: '#4170A1',   // Provided Steel Blue
                    purple: '#42437A',   // Provided Purple
                }
            }
        },
    },

    plugins: [forms],
};
