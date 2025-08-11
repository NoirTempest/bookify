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
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa fa-user-o me-2"></i> Dashboard
            </a>

            <a href="{{ route('admin.account-management') }}"
               class="sidebar-link {{ request()->routeIs('admin.account-management') ? 'active' : '' }}">
                <i class="fa fa-laptop me-2"></i> Account Management
            </a>

            <a href="{{ route('admin.organization') }}"
               class="sidebar-link {{ request()->routeIs('admin.organization') ? 'active' : '' }}">
                <i class="fa fa-clone me-2"></i> Org. Management
            </a>

            <a href="{{ route('admin.assets') }}"
               class="sidebar-link {{ request()->routeIs('admin.assets') ? 'active' : '' }}">
                <i class="fa fa-star-o me-2"></i> Assets Management
            </a>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div style="margin-left: 250px; padding: 20px; width: 100%;">
        @yield('content') <!-- Or your Livewire component here -->
    </div>
</div>
