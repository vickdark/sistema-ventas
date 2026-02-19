<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4e73df">
    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <link rel="apple-touch-icon" href="/img/logo-pwa-192.png">

    <script>
        window.TenantConfig = {
            businessName: "{{ function_exists('tenant') && tenant() ? (tenant('business_name') ?? 'Sistema') : config('app.name') }}",
            csrfToken: "{{ csrf_token() }}"
        };
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    @if (session('status'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (window.Swal) {
                    let text = "{{ session('status') }}";
                    if (text === 'verified') text = '¡Tu correo ha sido verificado!';
                    
                    window.Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: text,
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
<body class="app-shell">
<div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>
<div class="app-layout">
    @include('partials.aside')

    <main class="app-main">
        @include('partials.navbar')
        <div class="app-content">
