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
        extend: {
            fontFamily: {
                sans: ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                serif: ['"Source Serif 4"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                paper: {
                    DEFAULT: '#f3f5f7',
                    elevated: '#ffffff',
                    dark: '#12151a',
                    'dark-elevated': '#1a1f26',
                },
                ink: {
                    DEFAULT: '#1a1f26',
                    muted: '#5c6570',
                    soft: '#8b939e',
                    invert: '#e8eaed',
                },
                accent: {
                    DEFAULT: '#0f766e',
                    hover: '#0d9488',
                    muted: '#ccfbf1',
                    dark: '#2dd4bf',
                    'dark-hover': '#5eead4',
                },
            },
        },
    },

    plugins: [forms],
};
