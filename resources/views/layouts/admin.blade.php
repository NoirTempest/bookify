<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Bookify</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/link.png') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"
        crossorigin="anonymous">

    <!-- FullCalendar CSS -->
    <link href="https://unpkg.com/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"
        integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <!-- Vite (your app styles and JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>

<style>
    header.sticky-top {
        z-index: 1030;
        background-color: white;
        margin-top: -60px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    body{
        background-color: #fdf9f9
    }
</style>

<body style="font-family: 'Poppins', sans-serif;" class="bg-gray-600 text-dark">
    <!-- Sidebar -->
    @include('livewire.admin.layouts.adminsidebar')

    <!-- Main Content Wrapper -->
    <div class="min-vh-100 d-flex flex-column" style="margin-left: 250px;" id="main-content-wrapper">
        <header class="bg-white border-bottom shadow-sm py-2 px-4 sticky-top">
            @include('livewire.admin.layouts.navigation')
            <!-- Notification Header -->
            <livewire:requester.notification.notification-header />
        </header>


        <!-- Mobile Header -->
        <div
            class="bg-white d-md-none d-flex justify-content-between align-items-center border-bottom shadow-sm px-3 py-2">
            <button class="btn btn-outline-dark btn-sm" onclick="toggleMobileSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <span class="fw-semibold">Schedulink</span>
        </div>


        <!-- Page Content -->
        <main class="flex-fill px-3 px-md-4 pt-3" id="main-content" style="margin-top: 30px;">
            {{ $slot }}
        </main>

        @stack('scripts')
    </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    <!-- FullCalendar JS -->
    <script src="https://unpkg.com/fullcalendar@6.1.8/index.global.min.js"></script>

    <!-- Lottie Player -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <!-- SweetAlert / Notyf or Toast -->
    <x-scripts.notyf />

    <!-- Livewire Scripts (must come before Alpine.js for Livewire to initialize properly) -->
    @livewireScripts

    <!-- Alpine.js (after Livewire) -->
</body>

</html>