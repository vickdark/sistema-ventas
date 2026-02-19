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
            injectRegister: null, // Registro manual en pwa-handler.js
            scope: '/',
            base: '/',
            // Apuntar el SW generado a la carpeta public/ para que se sirva en la raíz
            outDir: 'public',
            // No emitir index.html de Vite (lo maneja Laravel)
            filename: 'sw.js',
            manifestFilename: 'manifest.webmanifest',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png'],
            workbox: {
                // El SW vive en public/, apuntar al directorio correcto de assets
                swDest: 'public/sw.js',
                globDirectory: 'public',
                additionalManifestEntries: [
                    { url: '/offline.html', revision: '2' }
                ],
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
                // Sin modifyURLPrefix: Vite ya genera URLs absolutas correctas como /build/assets/xxx.js
                globPatterns: [
                    'build/assets/*.{js,css,woff,woff2}',
                    'img/*.{png,jpg,ico,svg}',
                    'offline.html',
                ],
                // navigateFallback muestra offline.html cuando no hay red NI caché
                navigateFallback: '/offline.html',
                navigateFallbackDenylist: [
                    /^\/api\//,
                    /^\/build\//,
                    /^\/storage\//,
                    /^\/central\//,
                ],
                runtimeCaching: [
                    // Rutas que SIEMPRE necesitan red
                    {
                        urlPattern: /^\/central/,
                        handler: 'NetworkOnly',
                    },
                    {
                        urlPattern: /^\/api/,
                        handler: 'NetworkOnly',
                    },
                    {
                        urlPattern: /^\/storage/,
                        handler: 'NetworkOnly',
                    },
                    // PÁGINAS HTML: NetworkFirst con caché de 24h
                    // Intenta red primero; si falla usa caché; si tampoco hay caché → offline.html
                    {
                        urlPattern: ({ request }) => request.mode === 'navigate',
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'pages-cache',
                            networkTimeoutSeconds: 5, // Si no responde en 5s, usa caché
                            expiration: {
                                maxEntries: 200,
                                maxAgeSeconds: 24 * 60 * 60, // 24 horas
                            },
                            cacheableResponse: {
                                statuses: [200], // Solo cachear respuestas 200 OK (no redirecciones al login)
                            },
                            precacheFallback: {
                                fallbackURL: '/offline.html',
                            },
                        },
                    },
                    // IMÁGENES: CacheFirst (30 días)
                    {
                        urlPattern: ({ request }) => request.destination === 'image',
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'tenant-images-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 30 * 24 * 60 * 60,
                            },
                            cacheableResponse: {
                                statuses: [0, 200],
                            },
                        },
                    },
                    // JS/CSS dinámicos no incluidos en precache (e.g. carga diferida)
                    {
                        urlPattern: ({ request }) =>
                            request.destination === 'script' || request.destination === 'style',
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'static-resources',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 7 * 24 * 60 * 60,
                            },
                        },
                    },
                ],
                maximumFileSizeToCacheInBytes: 5 * 1024 * 1024,
            },
            manifest: {
                start_url: '/',
                scope: '/',
                name: 'Sistema de Ventas',
                short_name: 'VentasPOS',
                description: 'Sistema de Ventas Robusto con soporte Offline',
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
