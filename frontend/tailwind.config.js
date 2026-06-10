/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './components/**/*.{js,vue,ts}',
    './layouts/**/*.vue',
    './pages/**/*.vue',
    './plugins/**/*.{js,ts}',
    './app.vue',
    './error.vue',
  ],
  theme: {
    extend: {
      colors: {
        bpjs: {
          50:  '#e6f3fb',
          100: '#cce7f7',
          200: '#99ceef',
          300: '#66b6e7',
          400: '#339ddf',
          500: '#0085d7', // BPJS primary blue
          600: '#006aac',
          700: '#005081',
          800: '#003556',
          900: '#001b2b',
        },
      },
      fontFamily: {
        inter: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}
