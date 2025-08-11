<div>
    <div class="container py-5">
        <h4 class="mb-4">Booking Management (Admin Staff)</h4>

        {{-- Header: Filter/Search --}}
        <div class="row justify-content-between align-items-center mb-3">
            <div class="col-md-auto d-flex align-items-center">
                <div class="dropdown me-3">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="statusDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Filter by Status
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="statusDropdown">
                        <li><a class="dropdown-item {{ request('status') === null ? 'active' : '' }}"
                                href="{{ url()->current() }}">All</a></li>
                        <li><a class="dropdown-item {{ request('status') === 'pending' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a></li>
                        <li><a class="dropdown-item {{ request('status') === 'approved' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Approved</a></li>
                        <li><a class="dropdown-item {{ request('status') === 'rejected' ? 'active' : '' }}"
                                href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-md-auto flex-grow-1 d-flex justify-content-end">
                <input type="search" id="searchInput" class="form-control form-control-sm w-auto"
                    style="min-width: 180px; max-width: 300px;" placeholder="Search bookings..." />
            </div>
        </div>

        {{-- Booking Table --}}
        <div class="row justify-content-center mt-3">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0 align-middle"
                            style="border-collapse: separate; border-spacing: 0 0.6rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">No.</th>
                                    <th>Asset Type</th>
                                    <th>Requested By</th>
                                    <th>Destination</th>
                                    <th>Status</th>
                                    <th>Last Modified</th>
                                    <th class="pe-4 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bookings as $index => $booking)
                                <tr
                                    style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                                    <td class="ps-4">{{ $bookings->firstItem() + $index }}</td>
                                    <td>{{ $booking->assetType->name ?? 'N/A' }}</td>
                                    <td>{{ $booking->user->full_name ?? 'N/A' }}</td>
                                    <td>{{ $booking->destination ?? 'N/A' }}</td>
                                    <td>
                                        @switch($booking->status)
                                        @case('approved')
                                        <span class="px-3 py-1 rounded-pill fw-bold text-success"
                                            style="background-color: #d1e7dd; border: 1px solid #badbcc;">
                                            Approved
                                        </span>
                                        @break

                                        @case('pending')
                                        <span class="px-3 py-1 rounded-pill fw-bold text-primary"
                                            style="background-color: #cfe2ff; border: 1px solid #9ec5fe;">
                                            Pending
                                        </span>
                                        @break

                                        @case('rejected')
                                        <span class="px-3 py-1 rounded-pill fw-bold text-danger"
                                            style="background-color: #f8d7da; border: 1px solid #f5c2c7;">
                                            Rejected
                                        </span>
                                        @break

                                        @default
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endswitch
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($booking->updated_at)->format('M d, Y h:i A') }}</td>
                                    <td class="pe-4 text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border-0" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical fs-5"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <button class="dropdown-item"
                                                        wire:click="viewBookingDetails({{ $booking->id }})">
                                                        View Details
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin-staff.bookings.print', $booking->id) }}"
                                                        target="_blank">
                                                        <i class="bi bi-printer"></i> Print Ticket
                                                    </a>

                                                </li>
                                            </ul>

                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No bookings found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="mt-3 px-4">
                            {{ $bookings->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Component --}}
        @include('livewire.admin-staff.layouts.staff-modal')
        @include('livewire.admin-staff.layouts.allocate-modal')

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
                if (window.Livewire) {
                    Livewire.on('open-details-modal', () => {
                        const modal = new bootstrap.Modal(document.getElementById('staffDetailsModal'));
                        modal.show();
                    });
                }
            });
</script>
@endpush