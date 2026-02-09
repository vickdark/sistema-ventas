<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="d-flex align-items-center min-vh-100">
        <main class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                    <div class="p-4 p-md-5 bg-white border rounded-4 shadow-soft">
                        @yield('content')
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
