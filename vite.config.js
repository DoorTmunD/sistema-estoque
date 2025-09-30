// vite.config.js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  // NÃO use base aqui; o plugin cuida disso
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
  build: {
    outDir: 'public/build',   // arquivos em public/build
    manifest: true,           // gera manifest
    manifestDir: '.',         // <-- manifest em public/build/manifest.json
    emptyOutDir: true,
  },
})
