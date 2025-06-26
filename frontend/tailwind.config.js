/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#fef2f0',
          100: '#fde4e0',
          200: '#fcccc6',
          300: '#f9a89e',
          400: '#f47866',
          500: '#ED6F55',
          600: '#e55a41',
          700: '#d34626',
          800: '#b03621',
          900: '#92301f',
          950: '#50170d',
        },
        secondary: {
          50: '#f7f6f9',
          100: '#efebf3',
          200: '#e1dbe9',
          300: '#cbbfd8',
          400: '#af9bc2',
          500: '#957aac',
          600: '#7d6195',
          700: '#6b507e',
          800: '#5a4369',
          900: '#3B3157',
          950: '#2a1f3a',
        },
        youtube: {
          red: '#FF0000',
        },
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-in': 'slideIn 0.3s ease-out',
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
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
