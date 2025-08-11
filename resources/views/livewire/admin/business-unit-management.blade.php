<div>
    {{-- Business Unit Form --}}
    @if ($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label for="businessUnitName" class="form-label">Business Unit Name</label>
                    <input type="text" wire:model.defer="businessUnitName" id="businessUnitName" class="form-control">
                    @error('businessUnitName') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        {{ $editMode ? 'Update' : 'Add' }}
                    </button>
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Add Button --}}
    <div class="mb-2">
        <button class="btn btn-sm {{ $showForm ? '' : 'btn-outline' }}"
            style="{{ $showForm ? 'background-color: #172736; color: #fff; border-color: #172736;' : 'color: #172736; border-color: #172736;' }}"
            wire:click="$toggle('showForm')">
            {{ $showForm ? 'Hide Form' : 'Add Business Unit' }}
        </button>
    </div>



    {{-- Business Unit Table --}}
    @include('livewire.admin.partials.organization.business-unit-table')
</div>