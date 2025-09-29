import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import colors from 'tailwindcss/colors';

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
        // Fraktur Bold (UnifrakturCook) para o logo, nome, slogans especiais
        fraktur: ['UnifrakturCook', 'serif'],
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
      colors: {
        // Paleta Estocore — neon, escuro, roxo
        'estocore-dark': '#161433',
        'estocore-mid': '#232046',
        'estocore-neon': '#23F6F8',
        'estocore-purple': '#6648E0',
        'primary': colors.blue[600],
        'primary-dark': colors.blue[700],
        // ...adicione mais se quiser!
      },
      boxShadow: {
        // Glow azul neon
        'estocore': '0 0 12px #23F6F8, 0 0 32px #6648E0',
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
        'glow': {
          '0%, 100%': { filter: 'drop-shadow(0 0 8px #23F6F8aa)' },
          '50%': { filter: 'drop-shadow(0 0 16px #6648E0bb)' },
        },
        'shake': {
          '10%, 90%': { transform: 'translateX(-2px)' },
          '20%, 80%': { transform: 'translateX(4px)' },
          '30%, 50%, 70%': { transform: 'translateX(-8px)' },
          '40%, 60%': { transform: 'translateX(8px)' },
        },
      },
      animation: {
        'fade-in-out': 'fade-in-out 3s ease-in-out',
        'slide-up': 'slide-up 0.4s ease-out forwards',
        'glow': 'glow 2.5s ease-in-out infinite',
        'shake': 'shake 0.5s',
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
          borderTopColor: colors['estocore-neon'] ?? colors.blue[400],
          animation: 'spin 0.8s linear infinite',
        },
        // Glow azul neon — pode usar em SVG, logo etc.
        '.logo-glow': {
          filter: 'drop-shadow(0 0 8px #23F6F8bb) drop-shadow(0 0 16px #6648E080)',
        },
      });
    },
  ],
};