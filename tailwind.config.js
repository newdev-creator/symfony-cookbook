/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./assets/**/*.js", "./templates/**/*.html.twig"],
  theme: {
    fontFamily: {
      serif: ["Playfair Display", "serif"],
      sans: ["Inter", "sans-serif"],
      title: ["Great Vibes", "cursive"],
    },
    extend: {},
  },
  plugins: [],
};
