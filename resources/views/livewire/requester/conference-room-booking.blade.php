<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dateModalLabel">Asset Reservation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
                        <div class="modal-body">
                <form wire:submit.prevent="submit" id="bookingFormLivewire">
                    <div class="row g-4">
                        <!-- Left Column: Asset details -->
                        <div class="col-md-6">
                            <!-- Asset Type -->
                            <div class="mb-3">
                                <label class="form-label">Asset Type</label>
                                <select id="asset_type_id" class="form-select" wire:model="asset_type_id">
                                    <option value="">-- Select --</option>
                                    @foreach ($assetTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                @error('asset_type_id') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            @if($asset_type_id && $vehicle_type_id && (int)$asset_type_id === (int)$vehicle_type_id)
                            <!-- Vehicle-specific fields -->
                            <!-- Removed Purpose field per requirements -->
                            <div class="mb-3">
                                <label class="form-label">Destination</label>
                                <input type="text" class="form-control" wire:model="destination" required />
                                @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. of Seats</label>
                                <input type="number" class="form-control" wire:model="no_of_seats" min="1" />
                                @error('no_of_seats') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                            @else
                            <!-- Conference Room fields -->
                            <div class="mb-3">
                                <label class="form-label">Select Conference Room</label>
                                <select class="form-select" wire:model="asset_detail_id" id="asset_detail_id_select">
                                    <option value="">-- Select --</option>
                                    @foreach ($assetDetails as $detail)
                                    <option value="{{ $detail->id }}" data-location="{{ $detail->location }}">{{
                                        $detail->asset_name }}</option>
                                    @endforeach
                                </select>
                                @error('asset_detail_id') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Venue</label>
                                <select class="form-select" wire:model="destination" id="destination_select">
                                    <option value="">-- Select Venue --</option>
                                    @php
                                    $venues = collect($assetDetails)->pluck('location')->filter()->unique();
                                    @endphp
                                    @foreach ($venues as $venue)
                                    <option value="{{ $venue }}">{{ $venue }}</option>
                                    @endforeach
                                </select>
                                @error('destination') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. of Seats</label>
                                <input type="number" class="form-control" wire:model="no_of_seats" min="1" />
                                @error('no_of_seats') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            @endif

                            <!-- Notes (applies to both types) -->
                            <div class="mb-3">
                                <label class="form-label">Notes (Optional)</label>
                                <textarea class="form-control" rows="2" wire:model="notes"></textarea>
                                @error('notes') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <!-- Guests -->
                            <div class="mb-3">
                                <label class="form-label">Add guest</label>
                                <div class="">
                                    @foreach($guests as $index => $email)
                                        <div class="row g-2 align-items-center mb-2" wire:key="guest-row-{{ $index }}">
                                            <div class="col-9">
                                                <input type="email" class="form-control" placeholder="guest@example.com" wire:model.defer="guests.{{ $index }}">
                                            </div>
                                            <div class="col-3 d-grid">
                                                <button type="button" class="btn btn-outline-danger" wire:click="removeGuest({{ $index }})" @if(count($guests) <= 1) disabled @endif>-</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                        wire:click="addGuest">+ Add another guest</button>
                                </div>
                                @error('guests.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Right Column: Date and Time -->
                        <div class="col-md-6">
                            <h6 class="mb-3">Pick a Date & Time</h6>

                            <!-- Selected Date -->
                            <div class="mb-3">
                                <label for="selectedDateOnly" class="form-label">Date</label>
                                <input type="date" class="form-control" id="selectedDateOnly"
                                    wire:model="scheduled_date">
                                @error('scheduled_date') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="selectedTimeOnly" class="form-label">Time From</label>
                                    <input type="time" class="form-control" id="selectedTimeOnly"
                                        wire:model="time_from">
                                    @error('time_from') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="timeTo" class="form-label">Time To</label>
                                    <input type="time" class="form-control" id="timeTo" wire:model="time_to">
                                    @error('time_to') <div class="text-danger small">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="form-text mt-2">Tip: Click a date on the calendar to auto-fill the date/time.
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            Submit Reservation
                            <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm ms-2"
                                role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // When a conference room is selected, auto-set the Venue from data-location like the reference modal
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'asset_detail_id_select') {
            const opt = e.target.selectedOptions[0];
            if (!opt) return;
            const loc = opt.getAttribute('data-location');
            const dest = document.getElementById('destination_select');
            if (dest && loc) {
                // If the venue option exists, select it; otherwise set Livewire model by dispatching input
                const match = Array.from(dest.options).find(o => o.value === loc);
                if (match) {
                    dest.value = loc;
                    dest.dispatchEvent(new Event('input', { bubbles: true }));
                    dest.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        }
    });
</script>
@endpush