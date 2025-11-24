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
      boxShadow: {
        'highlight': 'inset 0 1px 2px #ffffff30, 0 1px 2px #00000030, 0 2px 4px #00000015',
      },
      colors: {
        primary: {
          50: '#fff2f0',
          100: '#ffe1dd',
          200: '#ffc5bf',
          300: '#ff9d94',
          400: '#ff6b5e',
          500: '#f05a45', // Vibrant Orange from button
          600: '#dd3e2b',
          700: '#ba2d1d',
          800: '#992518',
          900: '#7f2016',
          950: '#450d08',
        },
        secondary: {
          50: '#f5f4f7',
          100: '#e8e6eb',
          200: '#d0cdd9',
          300: '#aba6bf',
          400: '#837da1',
          500: '#645d85',
          600: '#4e4869',
          700: '#3e3954', // Card background
          800: '#322e4b', // Main background
          900: '#252238', // Darker background
          950: '#151321',
          surface: '#585070', // Custom surface color
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
          800: '#322e4b', // Match Main Background
          900: '#252238', // Match Darker Background
          950: '#151321',
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
