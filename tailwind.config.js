import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';  // 👈 adicione isso

/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      keyframes: {
        'fade-in-out': {
          '0%, 100%': { opacity: '0' },
          '10%, 90%': { opacity: '1' },
        },
        'slide-up': {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
      },
      animation: {
        'fade-in-out': 'fade-in-out 3s ease-in-out',
        'slide-up': 'slide-up 0.4s ease-out forwards',
      },
      colors: {
        primary: colors.blue[600],        // 👈 use colors.blue
        'primary-dark': colors.blue[700], // 👈 e aqui também
      },
    },
  },
  plugins: [
    forms,

    // Customizações para NProgress e spinner
    function({ addBase, addComponents }) {
      addBase({
        '.nprogress': { pointerEvents: 'none' },
        '.nprogress .bar': {
          backgroundColor: colors.blue[500],
          height: '3px',
        },
        '.nprogress .peg': {
          boxShadow: `0 0 10px ${colors.blue[500]}, 0 0 5px ${colors.blue[500]}`,
        },
      });

      addComponents({
        '.spinner': {
          borderTopWidth: '2px',
          borderRightWidth: '2px',
          borderBottomWidth: '2px',
          borderLeftWidth: '2px',
          borderRadius: '50%',
          borderColor: colors.gray[200],
          borderTopColor: colors.primary,
          animation: 'spin 0.8s linear infinite',
        },
      });
    },
  ],
};