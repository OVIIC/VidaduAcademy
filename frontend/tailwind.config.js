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
          50: '#fef6f5',
          100: '#fdecea',
          200: '#fad9d5',
          300: '#f6bbb2',
          400: '#f0918e',
          500: '#ED6F55',  // Hlavná farba pre buttony a zvýraznenie
          600: '#e55a3f',
          700: '#d6472a',
          800: '#b23c23',
          900: '#93351e',
          950: '#511b0e',
        },
        secondary: {
          50: '#f8f7fa',
          100: '#f1eff4',
          200: '#e5e1eb',
          300: '#d1c9dd',
          400: '#b8a9c9',
          500: '#9d89b3',
          600: '#866d9b',
          700: '#735983',
          800: '#604a6c',
          900: '#3B3157',  // Sekundárne zvýraznenie
          950: '#2d2442',
        },
        dark: {
          50: '#f7f7f7',
          100: '#e3e3e3',
          200: '#c8c8c8',
          300: '#a4a4a4',
          400: '#818181',
          500: '#666666',
          600: '#515151',
          700: '#434343',
          800: '#383838',
          900: '#000000',  // Čierne pozadie
          950: '#000000',
        },
        youtube: {
          red: '#FF0000',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
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
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
