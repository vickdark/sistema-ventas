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
    (function() {
        // Detectar si la página se cargó desde el caché (botón atrás/adelante)
        window.addEventListener('pageshow', function(event) {
            // event.persisted = true significa que vino del bfcache (back-forward cache)
            if (event.persisted) {
                console.log('Página cargada desde caché (botón atrás), recargando...');
                window.location.reload();
            }
        });

        // Método alternativo usando Performance API
        if (performance.navigation.type === 2) {
            // type 2 = navegación via back/forward
            console.log('Navegación detectada via botón atrás, recargando...');
            window.location.reload();
        }

        // Método moderno (Performance Navigation Timing API)
        if (performance.getEntriesByType) {
            const navEntries = performance.getEntriesByType('navigation');
            if (navEntries.length > 0 && navEntries[0].type === 'back_forward') {
                console.log('Back/Forward detectado (API moderna), recargando...');
                window.location.reload();
            }
        }
    })();
</script>
@stack('scripts')
</body>
</html>
