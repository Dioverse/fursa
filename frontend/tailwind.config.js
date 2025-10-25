/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      screens: {
        'xs': { 'min': '490px', 'max': '640px' },
        'xxs': { 'min': '370px', 'max': '490px' },
      },
      colors: {
        primary: '#b8974f',
        mprimary: {
          50: '#f5efe3',
          100: '#eadfc7',
          500: '#b8974f',
          600: '#9e7e3a',
        },
        secondary: '#2c2c2c',
        gold: {
          50:  '#fff9e6', // very light hint
          100: '#ffedbf', // light gold
          500: '#d4942a', // your base color
          600: '#b67a1f', // darker shade for hover/focus
        },
        'light-gold': '#f0b442',
      },
      fontFamily: {
        sans: ['Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
