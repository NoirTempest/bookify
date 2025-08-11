<div>
    {{-- Form --}}
    @if ($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="row g-2">
                    {{-- Asset Type (read-only) --}}
                    <div class="col-md-4">
                        <label class="form-label">Asset Type</label>
                        <select class="form-select" disabled>
                            <option>
                                {{ $assetTypes->find($assetTypeId)?->name ?? '-- Unknown --' }}
                            </option>
                        </select>
                        <input type="hidden" wire:model="assetTypeId">
                    </div>

                    {{-- Shared Fields --}}
                    {{-- Shared Fields --}}
                    @if (!is_null($assetTypeId) && in_array((int) $assetTypeId, [1, 2]))

                    <div class="col-md-4">
                        <label class="form-label">Asset Name</label>
                        <input type="text" wire:model.defer="assetName" class="form-control">
                        @error('assetName') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    {{-- Show Location only for Conference Room (assetTypeId === 1) --}}
                    @if ((int) $assetTypeId === 1)
                    <div class="col-md-4">
                        <label class="form-label">Location</label>
                        <input type="text" wire:model.defer="location" class="form-control">
                    </div>
                    @endif

                    {{-- Vehicle Specific --}}
                    @if ((int) $assetTypeId === 2)
                    <div class="col-md-4">
                        <label class="form-label">Brand</label>
                        <input type="text" wire:model.defer="brand" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Model</label>
                        <input type="text" wire:model.defer="model" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Color</label>
                        <input type="text" wire:model.defer="color" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Plate Number</label>
                        <input type="text" wire:model.defer="plateNumber" class="form-control">
                    </div>
                    @endif

                    <div class="col-md-4">
                        <label class="form-label">Number of Seats</label>
                        <input type="number" wire:model.defer="numberOfSeats" class="form-control">
                    </div>

                    @endif

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        {{ $editMode ? 'Update' : 'Add' }}
                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Add Buttons --}}
    <div class="mb-2">
        @if (!$showForm)
        <button class="btn btn-sm btn-outline-success me-2" wire:click="startAdd(2)">
            Add Vehicle
        </button>
        <button class="btn btn-sm btn-outline-primary" wire:click="startAdd(1)">
            Add Conference Room
        </button>
        @endif
    </div>

    {{-- Table --}}
    @include('livewire.admin.partials.assets.asset-detail-table')
</div>