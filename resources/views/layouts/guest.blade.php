<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Schedulink</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/link.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body style="font-family: 'Poppins', sans-serif;" class="bg-gray-600 text-dark">
    <div class="container-fluid vh-100 d-flex flex-column flex-md-row p-0">
        <!-- Left: Login Form -->

        <div class="col-12 col-md-6 d-flex align-items-center justify-content-center p-4">
            <div class="w-100" style="max-width: 400px;">
                {{ $slot }}
            </div>
        </div>

        <!-- Right: Image -->
        <!-- Lottie animation -->
        <div class="d-none d-md-block col-md-6 p-0"">
            <lottie-player src=" {{ asset('images/login.json') }}" background="transparent" speed="1"
            style="width: 100%; height: 100%; object-fit: cover;" loop autoplay>
            </lottie-player>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    @livewireScripts

    {{-- ✅ ADD THIS LINE --}}
    @stack('scripts')
</body>

</html>