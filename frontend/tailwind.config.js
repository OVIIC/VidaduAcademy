import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  darkMode: 'class', // Enable dark mode with 'dark' class
  theme: {
    screens: {
      'xs': '375px',    // iPhone SE
      'sm': '640px',    // Small tablets
      'md': '768px',    // Large tablets
      'lg': '1024px',   // Desktop
      'xl': '1280px',   // Large desktop
      '2xl': '1536px',  // Extra large
      // Custom responsive breakpoints
      'mobile': {'max': '639px'},     // Mobile-only styles
      'tablet': {'min': '640px', 'max': '1023px'}, // Tablet-only styles
      'desktop': {'min': '1024px'},   // Desktop and up
    },
    extend: {
      colors: {
        primary: {
          50: '#fff5f4',
          100: '#ffe8e5',
          200: '#ffd4ce',
          300: '#ffb3a8',
          400: '#ff8575',
          500: '#f26b55', // Brand Orange
          600: '#df4c34',
          700: '#ba321d',
          800: '#992b1b',
          900: '#7f281c',
          950: '#45110a',
        },
        secondary: {
          50: '#f4f3ff',
          100: '#ebe9fe',
          200: '#d9d6fe',
          300: '#bdb4fe',
          400: '#9d8bfd',
          500: '#6c5dd3', // Brand Purple
          600: '#5a49c4',
          700: '#4c3ba5',
          800: '#3f3285',
          900: '#1e1b2e', // Brand Dark Background
          950: '#12101c',
        },
        dark: {
          50: '#f6f6f7',
          100: '#eef0f2',
          200: '#dce0e5',
          300: '#c0c6d0',
          400: '#9fa8b8',
          500: '#7e899f',
          600: '#606a7f',
          700: '#495162',
          800: '#1e1b2e', // Match Brand Dark
          900: '#13111c', // Deepest Dark
          950: '#0a090f',
        },
        brand: {
          yellow: '#f9c851',
          green: '#90e0c4',
          blue: '#5d9cec',
          pink: '#ff8fb1',
        },
        youtube: {
          red: '#FF0000',
        },
      },
      fontFamily: {
        sans: ['SetupSans', 'Inter', 'system-ui', 'sans-serif'],
        setup: ['SetupSans', 'Inter', 'system-ui', 'sans-serif'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '128': '32rem',
        // Mobile-friendly spacings
        'safe-top': 'env(safe-area-inset-top)',
        'safe-bottom': 'env(safe-area-inset-bottom)',
        'safe-left': 'env(safe-area-inset-left)',
        'safe-right': 'env(safe-area-inset-right)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-in': 'slideIn 0.3s ease-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'bounce-in': 'bounceIn 0.6s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
        'scale-in': 'scaleIn 0.2s ease-out',
        'wiggle': 'wiggle 1s ease-in-out infinite',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideIn: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideDown: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        bounceIn: {
          '0%': { transform: 'scale(0.9)', opacity: '0' },
          '50%': { transform: 'scale(1.05)', opacity: '0.8' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        scaleIn: {
          '0%': { transform: 'scale(0.95)', opacity: '0' },
          '100%': { transform: 'scale(1)', opacity: '1' },
        },
        wiggle: {
          '0%, 100%': { transform: 'rotate(-3deg)' },
          '50%': { transform: 'rotate(3deg)' },
        },
      },
      typography: {
        DEFAULT: {
          css: {
            maxWidth: 'none',
            p: {
              marginTop: '1em',
              marginBottom: '1em',
            },
          },
        },
      },
    },
  },
  plugins: [
    forms,
    typography,
  ],
}
