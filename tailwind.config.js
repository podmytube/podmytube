module.exports = {
  purge: [
    './resources/views/*.blade.php',
    './resources/views/**/*.blade.php'
  ],
  theme: {
    extend: {
      spacing: {
        7: '1.75rem',
      },
    },
  },
  variants: {
    backgroundColor: ['responsive', 'hover', 'focus', 'active']
  },
  plugins: [],
}