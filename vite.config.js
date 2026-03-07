import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

// Root-level Vite config — used by Hostinger's Node.js hosting to detect
// the Vue + Vite framework and run the build.
// The project root is set to `frontend/` so index.html and all source files
// are resolved from there.  Output goes to `dist/` at the repository root,
// which Hostinger serves directly.
export default defineConfig({
  root: path.resolve(__dirname, 'frontend'),
  plugins: [vue()],
  server: {
    port: 5173,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      }
    }
  },
  build: {
    outDir: path.resolve(__dirname, 'dist'),
    emptyOutDir: true,
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'frontend/src'),
    }
  }
})
