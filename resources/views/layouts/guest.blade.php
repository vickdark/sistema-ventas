<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: "{{ session('status') }}",
                            confirmButtonColor: '#4e73df'
                        });
                    }
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: "{{ session('error') }}",
                            confirmButtonColor: '#4e73df'
                        });
                    }
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Error de validación',
                            text: "{{ $errors->first() }}",
                            confirmButtonColor: '#4e73df'
                        });
                    }
                });
            </script>
        @endif
    </head>
    <body class="{{ $bodyClass ?? 'd-flex align-items-center min-vh-100' }}">
        @hasSection('content')
            <main class="container py-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                        <div class="p-4 p-md-5 bg-white border rounded-4 shadow-soft">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </main>
        @else
            @yield('body')
        @endif
    </body>
</html>
