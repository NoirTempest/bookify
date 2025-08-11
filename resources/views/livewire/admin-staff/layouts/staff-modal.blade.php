<!-- Booking Details Modal -->
<div wire:ignore.self class="modal fade" id="staffDetailsModal" tabindex="-1" aria-labelledby="staffDetailsModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            @if ($selectedBooking)
            <div class="modal-header">
                <h5 class="modal-title" id="staffDetailsModalLabel">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Inline Vehicle Allocation Panel --}}
                @include('livewire.admin-staff.layouts.allocate-modal')

                {{-- Booking Details Content --}}
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-3">Request Information</h6>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Requested by:</label>
                            <div class="col-7">{{ $selectedBooking->user->first_name }} {{
                                $selectedBooking->user->last_name }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Department:</label>
                            <div class="col-7">{{ $selectedBooking->user->department->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Branch:</label>
                            <div class="col-7">{{ $selectedBooking->user->branch->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Date:</label>
                            <div class="col-7">{{ \Carbon\Carbon::parse($selectedBooking->scheduled_date)->format('M d,
                                Y') }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Purpose:</label>
                            <div class="col-7">{{ $selectedBooking->purpose }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Type:</label>
                            <div class="col-7">{{ $selectedBooking->assetType->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">No. of Seats:</label>
                            <div class="col-7">{{ $selectedBooking->no_of_seats }}</div>
                        </div>

                        <h6 class="fw-bold mt-4 mb-3">Approval Information</h6>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">1st Approver:</label>
                            <div class="col-7">{{ $selectedBooking->first_approver_name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">2nd Approver:</label>
                            <div class="col-7">{{ $selectedBooking->second_approver_name ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Center Column -->
                    <div class="col-md-4 border-start">
                        <h6 class="fw-bold mb-3">Vehicle Information</h6>
                        @php
                        $vehicle = $selectedBooking->vehicleAssignment?->assetDetail;
                        $image = $vehicle?->files?->first()?->file_attachments;
                        @endphp

                        {{-- Vehicle Image --}}
                        @if ($image)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('storage/' . $image) }}" alt="Vehicle Image"
                                class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                        </div>
                        @endif

                        {{-- Vehicle Details --}}
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Name:</label>
                            <div class="col-7">{{ $vehicle->asset_name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Brand:</label>
                            <div class="col-7">{{ $vehicle->brand ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Model:</label>
                            <div class="col-7">{{ $vehicle->model ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Seating Capacity:</label>
                            <div class="col-7">{{ $vehicle->number_of_seats ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Plate #:</label>
                            <div class="col-7">{{ $vehicle->plate_number ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Color:</label>
                            <div class="col-7">{{ $vehicle->color ?? '—' }}</div>
                        </div>

                        <h6 class="fw-bold mt-4 mb-3">Odometer & Driver</h6>
                        @php
                        $assign = $selectedBooking->vehicleAssignment;
                        $drv = $assign?->driver;
                        @endphp
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Driver:</label>
                            <div class="col-7">{{ $drv?->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Odometer Start:</label>
                            <div class="col-7">{{ $assign->odometer_start ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Odometer End:</label>
                            <div class="col-7">{{ $assign->odometer_end ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-4 border-start">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-calendar-event me-1"></i> Event Details
                            <div class="col-7 small fw-normal">
                                {{ $selectedBooking->purpose ?? '—' }}
                            </div>

                        </h6>



                        <div class="mb-2 row">
                            <h6 class="fw-bold mt-3">
                                <i class="bi bi-clock me-1"></i> Date & Time
                            </h6>
                            <div class="col-12 ps-2">
                                {{ \Carbon\Carbon::parse($selectedBooking->scheduled_date)->format('l, F d') }}
                                &bull;
                                {{ \Carbon\Carbon::parse($selectedBooking->time_from)->format('g:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($selectedBooking->time_to)->format('g:i A') }}
                            </div>
                        </div>


                        <h6 class="fw-bold mt-3">
                            <i class="bi bi-stickies me-1"></i> Notes
                        </h6>
                        <div class="mb-2">
                            <textarea class="form-control" rows="3"
                                readonly>{{ $selectedBooking->notes ?? '—' }}</textarea>
                        </div>

                        <h6 class="fw-bold mt-3">
                            <i class="bi bi-geo-alt me-1"></i> Destination
                        </h6>
                        <div class="mb-2 row">
                            <div class="col-12">{{ $selectedBooking->destination ?? '—' }}</div>
                        </div>

                        <h6 class="fw-bold mt-3">
                            <i class="bi bi-people me-1"></i> Guests
                        </h6>
                        <div class="mb-2 row">
                            <div class="col-12">
                                <ul class="mb-0 ps-3">
                                    @forelse($selectedBooking->bookedGuests as $guest)
                                    <li>{{ $guest->email }}</li>
                                    @empty
                                    <li>No guests listed.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer justify-content-end">
                    <button class="btn btn-secondary" onclick="showInlineVehicleForm()"
                        style="background-color: #172736; color: white;">Allocate Vehicle</button>
                    <button class="btn btn-success" onclick="doneAndReload()"
                        style="background-color: #172736; color: white;">Done</button>
                </div>
            </div>

            @else
            <div class="modal-body">
                <p>Loading details...</p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        window.vehicles = @json($vehicles);
    
        function doneAndReload() {
            Swal.fire({
                icon: 'success',
                title: 'All Set!',
                text: 'Booking details confirmed.',
                confirmButtonColor: '#172736',
                confirmButtonText: 'Close',
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        }
    </script>
    @endpush

</div>