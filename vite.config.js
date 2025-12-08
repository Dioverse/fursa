import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  build: {
    chunkSizeWarningLimit: 600, // in kB – default is 500
  },
  plugins: [vue()],
  base: '/',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
  server: {
    host: '0.0.0.0', // This makes Vite listen on all network interfaces
    port: 5173,
    proxy: {
      '/api': {
        target: 'https://back.fursaenergy.com/public/api',
        // 'https://fursa.jarustraining.com.ng',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
