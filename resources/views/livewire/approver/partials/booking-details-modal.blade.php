<!-- Booking Details Modal -->
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
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Requested By:</div>
                            <div class="col-7">{{ $selectedBooking->user->first_name }} {{
                                $selectedBooking->user->last_name }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Department:</div>
                            <div class="col-7">{{ $selectedBooking->user->department->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Branch:</div>
                            <div class="col-7">{{ $selectedBooking->user->branch->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Date Requested:</div>
                            <div class="col-7">{{ \Carbon\Carbon::parse($selectedBooking->created_at)->format('M d, Y
                                h:i A') }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Type of Asset:</div>
                            <div class="col-7">{{ $selectedBooking->assetType->name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Purpose:</div>
                            <div class="col-7">{{ $selectedBooking->purpose ?? '—' }}</div>
                        </div>
                        <div class="mb-4 row">
                            <div class="col-5 text-end fw-semibold">No. of Seats:</div>
                            <div class="col-7">{{ $selectedBooking->no_of_seats ?? '—' }}</div>
                        </div>

                        <h6 class="fw-bold mb-3">Approval Information</h6>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">1st Approver:</div>
                            <div class="col-7">
                                {{ $selectedBooking->first_approver_name ?? '—' }}
                                @if ($selectedBooking->first_approved_at)
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($selectedBooking->first_approved_at)->format('M d, Y h:i
                                    A') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">2nd Approver:</div>
                            <div class="col-7">
                                {{ $selectedBooking->second_approver_name ?? '—' }}
                                @if ($selectedBooking->second_approved_at)
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($selectedBooking->second_approved_at)->format('M d, Y h:i
                                    A') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Center Column -->
                    <div class="col-md-4 border-start">
                        <h6 class="fw-bold mb-3">Schedule Information</h6>
                        @php
                        $asset = $selectedBooking->assetDetail;
                        $image = $asset?->files?->first()?->file_attachments;
                        @endphp

                        {{-- Asset Image --}}
                        @if ($image)
                        <div class="mb-3 text-center">
                            <img src="{{ asset('storage/' . $image) }}" alt="Asset Image"
                                class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                        </div>
                        @endif
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Asset Name:</div>
                            <div class="col-7">{{ $selectedBooking->assetDetail->asset_name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Location:</div>
                            <div class="col-7">{{ $selectedBooking->assetDetail->location ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">No. of Seats:</div>
                            <div class="col-7">{{ $selectedBooking->assetDetail->number_of_seats ?? '—' }}</div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-4 border-start">
                        <h6 class="fw-bold mb-3">Project/Event</h6>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Name:</div>
                            <div class="col-7">{{ $selectedBooking->asset_name ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Purpose:</div>
                            <div class="col-7">{{ $selectedBooking->purpose ?? '—' }}</div>
                        </div>
                        <div class="mb-2 row">
                            <div class="col-5 text-end fw-semibold">Date & Time:</div>
                            <div class="col-7">
                                {{ \Carbon\Carbon::parse($selectedBooking->scheduled_date)->format('F d, Y') }} |
                                {{ \Carbon\Carbon::parse($selectedBooking->time_from)->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($selectedBooking->time_to)->format('h:i A') }}

                            </div>
                        </div>
                        <div class="mb-4 row">
                            <label class="col-5 text-end fw-semibold col-form-label">Notes:</label>
                            <div class="col-7">
                                <textarea class="form-control" rows="3"
                                    readonly>{{ $selectedBooking->notes ?? '—' }}</textarea>
                            </div>
                        </div>


                        <h6 class="fw-bold mb-2">Guests</h6>
                        @if ($selectedBooking->bookedGuests->isNotEmpty())
                        <ul class="list-unstyled ms-3">
                            @foreach ($selectedBooking->bookedGuests as $guest)
                            <li><i class="bi bi-person"></i> {{ $guest->email }}</li>
                            @endforeach
                        </ul>
                        @else
                        <p class="ms-3">No guests listed.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-danger"
                    wire:click="openDisapproveModal({{ $selectedBooking->id }})">
                    Reject
                </button>
                <button type="button" class="btn btn-success" wire:click="approveBooking({{ $selectedBooking->id }})">
                    Approve
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Disapprove Reason Modal -->
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