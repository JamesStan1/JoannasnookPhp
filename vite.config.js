import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { fileURLToPath, URL } from 'url'

// Root-level Vite config used by Hostinger for deployment.
// Sets the project root to the `frontend/` subdirectory so all
// source files, index.html, and assets are resolved from there.
// Output is written to `dist/` at the repository root so Hostinger
// can serve it without additional configuration.
export default defineConfig({
  root: fileURLToPath(new URL('./frontend', import.meta.url)),
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
    outDir: fileURLToPath(new URL('./dist', import.meta.url)),
    emptyOutDir: true,
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./frontend/src', import.meta.url)),
    }
  }
})
