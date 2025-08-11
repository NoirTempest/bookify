<!-- vehicle Details Modal -->
<div wire:ignore.self class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            @if ($selectedBooking)
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-3">Request Information</h6>
                        @php $user = $selectedBooking->user; @endphp
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Requested By:</label>
                            <div class="col-7">{{ $user->first_name }} {{ $user->last_name }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Department:</label>
                            <div class="col-7">{{ $user->department->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Branch:</label>
                            <div class="col-7">{{ $user->branch->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Date Requested:</label>
                            <div class="col-7">{{ \Carbon\Carbon::parse($selectedBooking->created_at)->format('M d, Y
                                h:i A') }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Type of Asset:</label>
                            <div class="col-7">{{ $selectedBooking->assetType->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">Purpose:</label>
                            <div class="col-7">{{ $selectedBooking->purpose ?? '—' }}</div>
                        </div>
                        <div class="mb-4 row"><label class="col-5 fw-semibold">No. of Seats:</label>
                            <div class="col-7">{{ $selectedBooking->no_of_seats ?? '—' }}</div>
                        </div>

                        <h6 class="fw-bold mb-3">Approval Information</h6>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">1st Approver:</label>
                            <div class="col-7">
                                {{ $selectedBooking->first_approver_name ?? '—' }}
                                @if ($selectedBooking->first_approved_at)
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($selectedBooking->first_approved_at)->format('M d, Y h:i
                                    A') }}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 row"><label class="col-5 fw-semibold">2nd Approver:</label>
                            <div class="col-7">
                                {{ $selectedBooking->second_approver_name ?? '—' }}
                                @if ($selectedBooking->second_approved_at)
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($selectedBooking->second_approved_at)->format('M d, Y h:i
                                    A') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Center Column -->
                    <div class="col-md-4 border-start">
                        <h6 class="fw-bold mb-3">Vehicle Information</h6>
                        @php $vehicle = $selectedBooking->vehicleAssignment?->assetDetail;
                        $image = $vehicle?->files?->first()?->file_attachments;
                        @endphp

                        {{-- Vehicle Image --}}
                        @if ($image)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('storage/' . $image) }}" alt="Vehicle Image"
                                class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                        </div>
                        @endif

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
                        @php $assign = $selectedBooking->vehicleAssignment; $drv = $assign?->driver; @endphp
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
                            <i class="bi bi-calendar-event me-1 text-secondary"></i> Project/Event
                            <div class="col-7 small fw-normal">
                                {{ $selectedBooking->purpose ?? '—' }}
                            </div>
                        </h6>

                        <div class="mb-2 row">
                            <label class="col-5 fw-semibold">
                                <i class="bi bi-clock me-1 text-secondary"></i> Date & Time:
                            </label>
                            <div class="col-7">
                                {{ \Carbon\Carbon::parse($selectedBooking->scheduled_date)->format('l, F d') }}
                                &bull;
                                {{ \Carbon\Carbon::parse($selectedBooking->time_from)->format('g:i A') }}
                                –
                                {{ \Carbon\Carbon::parse($selectedBooking->time_to)->format('g:i A') }}
                            </div>
                        </div>

                        <div class="mb-4 row">
                            <label class="col-5 fw-semibold">
                                <i class="bi bi-stickies me-1 text-secondary"></i> Notes:
                            </label>
                            <div class="col-7">
                                <textarea class="form-control" rows="3"
                                    readonly>{{ $selectedBooking->notes ?? '—' }}</textarea>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-2">
                            <i class="bi bi-people-fill me-1 text-secondary"></i> Guests
                        </h6>
                        @if ($selectedBooking->bookedGuests->isNotEmpty())
                        <ul class="list-unstyled ms-3">
                            @foreach ($selectedBooking->bookedGuests as $guest)
                            <li><i class="bi bi-person me-1 text-secondary"></i> {{ $guest->email }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="ms-3 text-muted">No guests listed.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-danger"
                    wire:click="openDisapproveModal({{ $selectedBooking->id }})">
                    Disapprove
                </button>
                <button type="button" class="btn btn-success" wire:click="approveBooking({{ $selectedBooking->id }})">
                    Approve
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Disapprove Modal -->
<div wire:ignore.self class="modal fade" id="disapproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disapprove Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <label for="reason">Reason for disapproval:</label>
                <textarea wire:model.defer="disapproveReason" class="form-control" rows="4" id="reason"></textarea>
                @error('disapproveReason')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" wire:click="submitDisapproval">Submit</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>