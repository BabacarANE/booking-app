import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(), // ✅ Tailwind v4
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
   server: {
    allowedHosts: [
      'retrieval-zesty-favoring.ngrok-free.dev',
      '.ngrok-free.dev'  // Autorise tous les sous-domaines ngrok
    ]
  }
})
