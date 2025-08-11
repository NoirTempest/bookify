<div>
    {{-- Company Code Form --}}
    @if ($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label for="companyCodeName" class="form-label">Company Code</label>
                    <input type="text" wire:model.defer="companyCodeName" id="companyCodeName" class="form-control">
                    @error('companyCodeName') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn btn-sm btn-primary me-2">
                        {{ $editMode ? 'Update' : 'Add' }}
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" wire:click="cancel">Cancel</button>
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
            {{ $showForm ? 'Hide Form' : 'Add Company Code' }}
        </button>
    </div>

    {{-- Company Code Table --}}
    @include('livewire.admin.partials.organization.company-code-table')
</div>