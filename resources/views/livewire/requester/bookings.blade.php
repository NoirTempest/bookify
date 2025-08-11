<div>
    <livewire:requester.notification.notification-header />
    <div class="container py-5">

        {{-- Header with Status Filter, Search, and + Reservation Button --}}
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-md-auto d-flex align-items-center">
                {{-- Status Filter Dropdown --}}
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <li>
                            <a class="dropdown-item {{ request('status') === null ? 'active' : '' }}"
                                href="{{ url()->current() }}">
                                All
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('status') === 'pending' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">
                                Pending
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('status') === 'approved' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">
                                Approved
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request('status') === 'rejected' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">
                                Rejected
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="col-md-auto flex-grow-1 d-flex justify-content-end">
                {{-- Live Search Input --}}
                <input type="search" id="searchInput" class="form-control form-control-sm w-auto"
                    style="min-width: 180px; max-width: 300px;" placeholder="Search bookings..."
                    aria-label="Search bookings" />
            </div>

            <div class="col-md-auto ms-3">
                <button class="btn text-white" style="background-color: #1f364a;" data-bs-toggle="modal"
                    data-bs-target="#bookingModal">
                    + Asset Reservation
                </button>
            </div>
        </div>

        {{-- Booking Table --}}
        <div class="row justify-content-center mt-3">
            <div class="col-lg-12">
                <div class="row justify-content-center mt-3">
                    <div class="col-lg-12">

                        {{-- Card: Conference Bookings --}}
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-0">
                                <h5 class="mt-3 ps-4">Conference Bookings</h5>
                                @php $conferenceBookings = $bookings->where('asset_type_id', 1); @endphp

                                @unless($conferenceBookings->isEmpty())
                                <table class="table table-hover align-middle mb-0" style="border-spacing: 0 0.6rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No.</th>
                                            <th>Asset Name</th>
                                            <th>Venue</th>
                                            <th>Status</th>
                                            <th>Reason</th> <!-- NEW -->
                                            <th>Last Modified</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($conferenceBookings as $index => $booking)
                                        <tr
                                            style="background:white; box-shadow:0 2px 6px rgba(0,0,0,0.08); border-radius:0.375rem;">
                                            <td class="ps-4">{{ $loop->iteration }}</td>
                                            <td>{{ $booking->assetType->name }}</td>
                                            <td>{{ $booking->assetDetail->location ?? '—' }}</td>
                                            <td>
                                                @switch($booking->status)
                                                @case('approved')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-success"
                                                    style="background-color:#d1e7dd; border:1px solid #badbcc;">Approved</span>
                                                @break
                                                @case('pending')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-primary"
                                                    style="background-color:#cfe2ff; border:1px solid #9ec5fe;">Pending</span>
                                                @break
                                                @case('rejected')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-danger"
                                                    style="background-color:#f8d7da; border:1px solid #f5c2c7;">Rejected</span>
                                                @break
                                                @default
                                                <span class="badge bg-secondary">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($booking->disapprove_reason)
                                                <button
                                                    class="btn btn-sm text-primary d-flex align-items-center justify-content-center mx-auto"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reasonModal{{ $booking->id }}">
                                                    <i class="bi bi-hand-thumbs-down-fill text-danger"></i>
                                                </button>
                                                @else
                                                <i
                                                    class="bi bi-hand-thumbs-up-fill text-success d-flex justify-content-center"></i>
                                                @endif
                                            </td>
                                            <td>{{ $booking->updated_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light border-0" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                wire:click.prevent="deleteBooking({{ $booking->id }})">Delete</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            @if ($booking->disapprove_reason)
                                            <!-- Reason Modal -->
                                            <div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1"
                                                aria-labelledby="reasonModalLabel{{ $booking->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header text-white"
                                                            style="background-color: #dc3545;">
                                                            <h5 class="modal-title"
                                                                id="reasonModalLabel{{ $booking->id }}">
                                                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                                                Rejection Reason
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body bg-light">
                                                            <div class="card border-danger border-2 shadow-sm">
                                                                <div class="card-body text-dark">
                                                                    <p class="mb-0">
                                                                        <i
                                                                            class="bi bi-chat-left-text-fill text-danger me-2"></i>
                                                                        {{ $booking->disapprove_reason }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div class="ps-4 py-3 text-muted">No conference bookings.</div>
                                @endunless
                            </div>
                        </div>

                        {{-- Card: Vehicle Bookings --}}
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-0">
                                <h5 class="mt-3 ps-4">Vehicle Bookings</h5>
                                @php $vehicleBookings = $bookings->where('asset_type_id', 2); @endphp

                                @unless($vehicleBookings->isEmpty())
                                <table class="table table-hover align-middle mb-0" style="border-spacing: 0 0.6rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4">No.</th>
                                            <th>Asset Name</th>
                                            <th>Destination</th>
                                            <th>Status</th>
                                            <th>Reason</th> <!-- NEW -->
                                            <th>Last Modified</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vehicleBookings as $index => $booking)
                                        <tr
                                            style="background:white; box-shadow:0 2px 6px rgba(0,0,0,0.08); border-radius:0.375rem;">
                                            <td class="ps-4">{{ $loop->iteration }}</td>
                                            <td>{{ $booking->assetType->name }}</td>
                                            <td>{{ $booking->destination ?? '—' }}</td>
                                            <td>
                                                @switch($booking->status)
                                                @case('approved')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-success"
                                                    style="background-color:#d1e7dd; border:1px solid #badbcc;">Approved</span>
                                                @break
                                                @case('pending')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-primary"
                                                    style="background-color:#cfe2ff; border:1px solid #9ec5fe;">Pending</span>
                                                @break
                                                @case('rejected')
                                                <span class="px-3 py-1 rounded-pill fw-bold text-danger"
                                                    style="background-color:#f8d7da; border:1px solid #f5c2c7;">Rejected</span>
                                                @break
                                                @default
                                                <span class="badge bg-secondary">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td class="text-center align-middle">
                                                @if ($booking->disapprove_reason)
                                                <button
                                                    class="btn btn-sm text-primary d-flex align-items-center justify-content-center mx-auto"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reasonModal{{ $booking->id }}">
                                                    <i class="bi bi-hand-thumbs-down-fill text-danger"></i>
                                                </button>
                                                @else
                                                <i
                                                    class="bi bi-hand-thumbs-up-fill text-success d-flex justify-content-center"></i>
                                                @endif
                                            </td>
                                            <td>{{ $booking->updated_at->format('M d, Y h:i A') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light border-0" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                wire:click.prevent="deleteBooking({{ $booking->id }})">Delete</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            @if ($booking->disapprove_reason)
                                            <!-- Reason Modal -->
                                            <div class="modal fade" id="reasonModal{{ $booking->id }}" tabindex="-1"
                                                aria-labelledby="reasonModalLabel{{ $booking->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header text-white"
                                                            style="background-color: #dc3545;">
                                                            <h5 class="modal-title"
                                                                id="reasonModalLabel{{ $booking->id }}">
                                                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                                                Rejection Reason
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body bg-light">
                                                            <div class="card border-danger border-2 shadow-sm">
                                                                <div class="card-body text-dark">
                                                                    <p class="mb-0">
                                                                        <i
                                                                            class="bi bi-chat-left-text-fill text-danger me-2"></i>
                                                                        {{ $booking->disapprove_reason }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <div class="ps-4 py-3 text-muted">No vehicle bookings.</div>
                                @endunless
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-3 px-4">
                            {{ $bookings->links() }}
                        </div>
                    </div>

                    {{-- Modal --}}
                    @include('livewire.requester.modal.bookingModal')
                </div>
            </div>

            {{-- Modal --}}
            @include('livewire.requester.modal.bookingModal')
        </div>


        {{-- Modal Partial --}}
        @include('livewire.requester.modal.bookingModal')
        <!-- Custom Scripts -->
        <script src="{{ asset('js/custom.js') }}"></script>
        {{-- Live Search Script --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('searchInput');
                    const tableRows = document.querySelectorAll('tbody tr');

                    searchInput.addEventListener('input', function() {
                        const query = this.value.toLowerCase().trim();

                        tableRows.forEach(row => {
                            const rowText = row.textContent.toLowerCase();

                            if (rowText.includes(query)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                });
        </script>

    </div>
</div>