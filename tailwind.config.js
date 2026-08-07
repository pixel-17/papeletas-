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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#eef4ff',
                    100: '#dfeaff',
                    200: '#c1d6ff',
                    300: '#93b8ff',
                    400: '#5e91ff',
                    500: '#3b6cf6',
                    600: '#2549ea',
                    700: '#1e39d1',
                    800: '#2032a8',
                    900: '#202f84',
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            boxShadow: {
                glass: '0 8px 32px 0 rgba(31, 38, 135, 0.15)',
                'glass-lg': '0 20px 50px -12px rgba(31, 38, 135, 0.25)',
                'glass-hover': '0 12px 40px 0 rgba(31, 38, 135, 0.22)',
                'inner-glass': 'inset 0 1px 0 0 rgba(255,255,255,0.4)',
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-14px)' },
                },
                'gradient-shift': {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-500px 0' },
                    '100%': { backgroundPosition: '500px 0' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 0.5s ease-out both',
                'fade-in': 'fade-in 0.4s ease-out both',
                'scale-in': 'scale-in 0.25s ease-out both',
                float: 'float 6s ease-in-out infinite',
                'float-slow': 'float 9s ease-in-out infinite',
                'gradient-shift': 'gradient-shift 12s ease infinite',
                shimmer: 'shimmer 2s infinite linear',
            },
            transitionTimingFunction: {
                'soft-bounce': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            },
        },
    },

    plugins: [forms],
};
