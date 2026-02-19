import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js'
            ],
            refresh: true,
            // detectTls: 'sistema-ventas.test', 
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: null, 
            scope: '/',
            base: '/',
            outDir: 'public',
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png'],
            workbox: {
                swDest: 'public/sw.js',
                globDirectory: 'public',
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                navigateFallback: null, // Evita el error de index.html no precacheado
                globPatterns: [
                    'build/assets/*.{js,css,woff,woff2}',
                    'img/*.{png,jpg,ico,svg}',
                ],
                globIgnores: ['**/manifest.webmanifest'], // Evita que el SW intente precachear el manifest y falle
            },
            manifest: {
                start_url: '/',
                scope: '/',
                name: 'Sistema de Ventas',
                short_name: 'VentasPOS',
                description: 'Sistema de Ventas Profesional',
                theme_color: '#4e73df',
                background_color: '#ffffff',
                display: 'standalone',
                icons: [
                    {
                        src: '/img/logo-pwa-192.png',
                        sizes: '192x192',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                    {
                        src: '/img/logo-pwa-512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
        })
    ],
    server: {
        host: '127.0.0.1',
        port: 5173,
        hmr: {
            host: 'sistema-ventas.test',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
