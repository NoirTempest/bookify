<!-- Hamburger toggle (visible on mobile only) -->
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">

<!-- Hamburger toggle (visible on mobile only) -->
<button class="btn btn-outline-light d-md-none m-3 position-fixed" type="button" onclick="toggleMobileSidebar()"
    style="z-index: 1050;">
    <i class="bi bi-list"></i>
</button>

<div style="display: flex;">
    <!-- Sidebar (Fixed Left) -->
    <aside style="
        width: 250px;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        background-color: #1f364a;
        color: white;
        overflow-y: auto;
        z-index: 1040;
    " class="d-flex flex-column justify-content-start">
        <!-- Branding -->
        <div class="logo d-flex flex-column align-items-center text-center pt-4 pb-2 mb-3">
            <img src="{{ asset('images/gmall.png') }}" alt="Gaisano Malls Logo">
            <p class="text-white fw-semibold small mt-1 mb-0">Booking System</p>
        </div>

        <!-- Menu Title -->
        <p class="text-uppercase fw-bold mb-3 ps-3 text-white"
            style="font-size: 0.85rem; margin-bottom: -10px !important; margin-left:-20px !important;">Menu</p>

        <!-- Navigation -->
        <nav class="d-flex flex-column">
            <a href="{{ route('requester.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('requester.dashboard') ? 'active' : '' }}">
                <i class="fa fa-tachometer me-2"></i> Dashboard
            </a>

            <a href="{{ route('requester.bookings') }}"
                class="sidebar-link {{ request()->routeIs('requester.bookings') ? 'active' : '' }}">
                <i class="fa fa-calendar-check me-2"></i> Booking Management
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div style="margin-left: 250px; padding: 20px; width: 100%;">
        @yield('content')
        <!-- Or your Livewire component here -->
    </div>
</div>