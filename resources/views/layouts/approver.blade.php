<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Bookify</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/link.png') }}">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Notyf Notifications -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    {{--
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"> --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- FullCalendar -->
    <link href="https://unpkg.com/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<style>
    body {
        background-color: #f1efef;
    }
</style>

<body style="font-family: 'Poppins', sans-serif;" class="bg-gray-600 text-dark">
    @include('livewire.approver.layouts.approversidebar')

    <div class="min-vh-100 d-flex flex-column" style="margin-left: 250px;" id="main-content-wrapper">
        @include('livewire.approver.layouts.navigation')

        <div
            class="bg-white d-md-none d-flex justify-content-between align-items-center border-bottom shadow-sm px-3 py-2">
            <button class="btn btn-outline-dark btn-sm" onclick="toggleMobileSidebar()">
                <i class="bi bi-list"></i>
            </button>
            <span class="fw-semibold">Schedulink</span>
        </div>
        <livewire:requester.notification.notification-header />
        <main class="flex-fill px-3 px-md-4 pt-3" id="main-content">
            {{ $slot }}
        </main>

        @stack('scripts')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Scripts -->
    <script src="https://unpkg.com/fullcalendar@6.1.8/index.global.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script> --}}
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/booking-table-v2.js') }}"></script>

    <!-- Livewire -->
    @livewireScripts
    <!-- SweetAlert2 & Modal Event Handlers -->
</body>

</html>