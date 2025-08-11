<div class="px-3 mt-3 text-start">
    <ul class="nav nav-pills d-inline-flex gap-2" id="approverTabs">
        <li class="nav-item">
            <a class="nav-link fw-semibold px-4 py-2 rounded small 
                {{ request()->routeIs('approver.booking-management') ? 'active-tab' : 'inactive-tab' }}"
                href="{{ route('approver.booking-management') }}">
                <i class="bi bi-building me-1"></i>
                Conference Room
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link fw-semibold px-4 py-2 rounded small 
                {{ request()->routeIs('approver.vehicle-booking-management') ? 'active-tab' : 'inactive-tab' }}"
                href="{{ route('approver.vehicle-booking-management') }}">
                <i class="bi bi-truck-front me-1"></i>
                Vehicle
            </a>
        </li>
    </ul>
    <style>
        .active-tab {
            background-color: #1f364a !important;
            color: white !important;
            border: 1px solid #1f364a;
            font-weight: normal !important;
        }

        .inactive-tab {
            background-color: transparent !important;
            color: black !important;
            border: 1px solid #1f364a;
            font-weight: normal !important;
        }

        .active-tab:hover,
        .inactive-tab:hover {
            filter: brightness(1.1);
        }
    </style>
</div>