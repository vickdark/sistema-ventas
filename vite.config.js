import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/pages/pos.css'
            ],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            injectRegister: 'auto',
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff,woff2}'],
                // Aseguramos que las rutas de Laravel funcionen offline
                navigateFallback: '/',
                // Excluimos explícitamente el panel central y APIs de la caché offline
                navigateFallbackDenylist: [
                    /^\/api/, 
                    /^\/storage/,
                    /^\/central/, // Excluir rutas que empiecen con /central
                    /^\/admin-central/ // Por si acaso usas otro prefijo para el panel central
                ],
                // No cachear dinámicamente rutas del panel central
                runtimeCaching: [
                    {
                        urlPattern: /^\/central/,
                        handler: 'NetworkOnly', // El panel central SIEMPRE requiere internet
                    },
                    {
                        urlPattern: /\.(?:png|jpg|jpeg|svg|gif)$/,
                        handler: 'CacheFirst', // Cachear imágenes primero (logos, productos)
                        options: {
                            cacheName: 'tenant-images-cache',
                            expiration: {
                                maxEntries: 100,
                                maxAgeSeconds: 30 * 24 * 60 * 60, // 30 días
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    },
                    {
                        urlPattern: /^\/storage\//, // Cachear cualquier cosa en la carpeta storage (logos subidos)
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'tenant-storage-cache',
                            expiration: {
                                maxEntries: 50,
                                maxAgeSeconds: 7 * 24 * 60 * 60, // 7 días
                            },
                            cacheableResponse: {
                                statuses: [0, 200]
                            }
                        }
                    }
                ],
                maximumFileSizeToCacheInBytes: 5 * 1024 * 1024, // Aumentar a 5MB por el tamaño del bundle
            },
            manifest: {
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
                        type: 'image/png'
                    },
                    {
                        src: '/img/logo-pwa-512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ]
            }
        })
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
