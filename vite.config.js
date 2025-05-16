import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/',                // URLs geradas → /build/assets/…
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,          // HMR durante `npm run dev`
        }),
    ],
    build: {
        outDir: 'public/build',     // destino dos arquivos compilados
        manifest: true,             // gera manifest.json
    },
});