// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
      buildDirectory: 'build', // gera manifest em public/build/manifest.json
    }),
  ],
  // 🔥 Garantia: arquivos de saída vão para public/build (não 'dist')
  build: {
    outDir: 'public/build',
    manifest: true,
    emptyOutDir: true,
  },
})
