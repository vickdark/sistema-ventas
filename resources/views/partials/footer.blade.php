            </div>
        <footer class="app-footer">
            <span>{{ config('app.name', 'Laravel') }} {{ date('Y') }}</span>
            <span>Hecho con Bootstrap 5</span>
        </footer>
    </main>
</div>
@include('partials.alerts')

<script>
    // Prevenir que el navegador muestre JSON al presionar "Atrás"
    // IMPORTANTE: Solo recargar si hay conexión — en offline esto rompería la navegación
    (function() {
        function shouldReload() {
            // Si estamos offline, el Service Worker puede servir la página desde caché.
            // Recargar sin conexión causaría un error de red. No lo hacemos.
            if (!navigator.onLine) return false;
            return true;
        }

        // Detectar si la página se cargó desde el bfcache (botón atrás/adelante)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted && shouldReload()) {
                console.log('Página cargada desde bfcache, recargando (online)...');
                window.location.reload();
            }
        });

        // Método moderno (Performance Navigation Timing API)
        if (performance.getEntriesByType) {
            const navEntries = performance.getEntriesByType('navigation');
            if (navEntries.length > 0 && navEntries[0].type === 'back_forward' && shouldReload()) {
                console.log('Back/Forward detectado, recargando (online)...');
                window.location.reload();
            }
        }
    })();
</script>
@stack('scripts')
</body>
</html>
