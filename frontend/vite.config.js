import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    server: {
        host: true, // memungkinkan akses dari luar localhost
        port: 5173, // pastikan port tetap
        strictPort: true, // agar tidak pindah ke port lain
        hmr: {
            host: 'localhost', // biarkan default untuk local
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
