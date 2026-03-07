import { fileURLToPath } from 'url'
import path from 'path'

const __dirname = path.dirname(fileURLToPath(import.meta.url))
const root = __dirname.replace(/\\/g, '/')

export default {
  content: [
    `${root}/index.html`,
    `${root}/src/**/*.{vue,js,ts,jsx,tsx}`,
  ],
  theme: {
    extend: {
      colors: {
        primary: '#166534',
        secondary: '#B45309',
      },
    },
  },
  plugins: [],
}
