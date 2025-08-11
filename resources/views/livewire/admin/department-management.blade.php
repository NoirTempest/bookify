<div>
    {{-- Department Form --}}
    @if ($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label for="departmentName" class="form-label">Department Name</label>
                    <input type="text" wire:model.defer="departmentName" id="departmentName" class="form-control">
                    @error('departmentName') <div class="text-danger small">{{ $message }}</div> @enderror
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
            {{ $showForm ? 'Hide Form' : 'Add Department' }}
        </button>
    </div>

    {{-- Department Table --}}
    @include('livewire.admin.partials.organization.department-table')
</div>