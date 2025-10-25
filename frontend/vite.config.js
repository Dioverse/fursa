import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  base: './',
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
  server: {
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
