<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Central - Multitenancy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-xl font-bold text-indigo-600">Admin Central</span>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('central.tenants.index') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Inquilinos
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-700 mr-4">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('central.logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-grow py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t py-4">
            <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Multitenancy System. Laravel 12.
            </div>
        </footer>
    </div>
</body>
</html>
