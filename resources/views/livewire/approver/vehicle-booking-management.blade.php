<div> {{-- ✅ SINGLE ROOT WRAPPER FOR LIVEWIRE COMPONENT --}}

    {{-- Table Card --}}
    <livewire:approver.notification.navigation-header />
    <div class="card shadow-sm border-0">


        {{-- Table Header --}}
        <div class="card-header bg-white d-flex justify-content-between align-items-center mt-3">
            <h5 class="mb-0 fw-bold">Vehicle</h5>
            <input type="search" id="bookingSearch" class="form-control form-control-sm w-auto" placeholder="Search...">
        </div>

        {{-- Table --}}
        <div class="card-body p-0">
            <table id="bookingTable" class="table table-hover mb-0 align-middle"
                style="border-collapse: separate; border-spacing: 0 0.6rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Asset Type</th>
                        <th>Requested</th>
                        <th>Distination</th>
                        <th>Status</th>
                        <th>Last Modified</th>
                        <th class="pe-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $filtered = $bookings->filter(fn($b) => $b->asset_type_id === 2);
                    @endphp

                    @forelse ($filtered as $index => $booking)
                    <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                        <td class="ps-4">{{ $loop->iteration }}</td>
                        <td>{{ $booking->assetType->name ?? 'N/A' }}</td>
                        <td>{{ $booking->user->first_name }} {{ $booking->user->last_name }}</td>
                        <td>{{ $booking->destination ?? '—' }}</td>
                        <td>
                            @switch($booking->status)
                            @case('approved')
                            <span class="px-3 py-1 rounded-pill fw-bold text-success"
                                style="background-color: #d1e7dd; border: 1px solid #badbcc;">Approved</span>
                            @break
                            @case('pending')
                            <span class="px-3 py-1 rounded-pill fw-bold text-primary"
                                style="background-color: #cfe2ff; border: 1px solid #9ec5fe;">Pending</span>
                            @break
                            @case('rejected')
                            @case('disapproved')
                            <span class="px-3 py-1 rounded-pill fw-bold text-danger"
                                style="background-color: #f8d7da; border: 1px solid #f5c2c7;">Rejected</span>
                            @break
                            @default
                            <span class="badge bg-secondary">Unknown</span>
                            @endswitch
                        </td>
                        <td>{{ \Carbon\Carbon::parse($booking->updated_at)->format('M d, Y h:i A') }}</td>
                        <td class="pe-4 align-middle text-center">
                            <div class="dropdown d-inline">
                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a href="#" wire:click.prevent="viewBookingDetails({{ $booking->id }})"
                                            class="dropdown-item">
                                            View Details
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No conference room bookings found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($bookings->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small" id="bookingCount">
                    Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }}
                    results
                </div>
                <div>
                    {{ $bookings->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- ✅ INCLUDE MODAL VIEW --}}
    @include('livewire.approver.partials.vehicle-details-modal')

</div>