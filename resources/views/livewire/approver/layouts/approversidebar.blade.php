<!-- Hamburger toggle (visible on mobile only) -->
<button class="btn btn-outline-secondary d-md-none m-3 position-fixed" type="button" onclick="toggleMobileSidebar()" style="z-index: 1050;">
    <i class="bi bi-list"></i>
</button>

<aside class="sidebar bg-white border-end shadow vh-100 position-fixed top-0 start-0 d-flex flex-column p-0"
    style="width: 250px; z-index: 1040; transition: transform 0.3s ease-in-out;">

    <!-- Branding -->
    <div class="d-flex flex-column align-items-center justify-content-center border-bottom py-3 bg-light text-center">
        <img src="{{ asset('images/gmall.png') }}" alt="Gaisano Malls Logo" class="img-fluid" style="max-height: 40px; width: 170px;">
        <p class="text-muted fw-semibold mt-1 small mb-0">Booking System</p>
    </div>


    <!-- Menu Header -->
    <div class="px-4 pt-3 text-muted fw-semibold small">MENU</div>

    <!-- Navigation -->
    <nav class="flex-grow-1 mt-2">
        <ul class="nav flex-column px-2">

            <li class="nav-item mb-1">
                <a href="{{ route('approver.dashboard') }}"
                    class="nav-link d-flex align-items-center gap-2 rounded px-3 py-2 transition
                       {{ request()->routeIs('approver.dashboard') ? 'bg-primary text-white fw-semibold' : 'text-dark hover-bg-light' }}">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="{{ route('approver.booking-management') }}"
                    class="nav-link d-flex align-items-center gap-2 rounded px-3 py-2 transition
        {{ request()->routeIs('approver.booking-management') || request()->routeIs('approver.vehicle-booking-management') ? 'bg-primary text-white fw-semibold' : 'text-dark hover-bg-light' }}">
                    <i class="bi bi-calendar-week"></i> Booking Management
                </a>
            </li>



            {{-- <li class="nav-item mb-1">
                <a href="#" class="nav-link d-flex align-items-center gap-2 rounded px-3 py-2 text-dark hover-bg-light">
                    <i class="bi bi-person-badge"></i> Client Management
                </a>
            </li>

            <li class="nav-item mb-1">
                <a href="#" class="nav-link d-flex align-items-center gap-2 rounded px-3 py-2 text-dark hover-bg-light">
                    <i class="bi bi-chat-dots"></i> Feedback
                </a>
            </li> --}}

        </ul>
    </nav>
</aside>

<style>
    .hover-bg-light:hover {
        background-color: #f8f9fa;
    }

    .transition {
        transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
    }
</style>
