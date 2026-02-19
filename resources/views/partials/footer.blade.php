            </div>
        <footer class="app-footer">
            <span>{{ config('app.name', 'Laravel') }} {{ date('Y') }}</span>
            <span>Made with ❤️ by Mambacode &copy; all rights reserved</span>
        </footer>
    </main>
</div>
@include('partials.alerts')

<script>
    // Prevenir que el navegador muestre JSON al presionar "Atrás/Adelante" (bfcache)
    (function() {
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        if (performance.getEntriesByType) {
            const navEntries = performance.getEntriesByType('navigation');
            if (navEntries.length > 0 && navEntries[0].type === 'back_forward') {
                window.location.reload();
            }
        }
    })();
</script>
@stack('scripts')
</body>
</html>
