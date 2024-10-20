/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        'zu-red': '#FF3D00',
        'zu-black': '#222222',
        'zu-light-black': '#3D3D3D',
        'zu-white': '#F1F2F6',
        'zu-gray': '#7F7F7F',
        'zu-light-gray': '#E5E7EB',
        // games
        'won': '#538D4E',
        'in-progress': '#F6AE2D',
        'lost': '#DA2C38',
        // guess
        'correct': '#538D4E',
        'present': '#B59F3B',
        'absent': '#3A3A3C',
        // keyboard keys unused
        'unused-key': '#9CA3AF',
      },
    },
  },
  plugins: [
  ],
}
