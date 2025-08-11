<div>
    {{-- Branch Form --}}
    @if ($showForm)
    <div class="card shadow-sm border mb-3 bg-white">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label for="branchName" class="form-label">Branch Name</label>
                    <input type="text" wire:model.defer="branchName" id="branchName" class="form-control">
                    @error('branchName') <div class="text-danger small">{{ $message }}</div> @enderror
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
    <div class="mb-2 d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-outline d-flex align-items-center gap-2"
            style="color: #172736; border-color: #172736;" wire:click="$toggle('showForm')">
            @if ($showForm)


            {{-- Eye Open SVG --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="#172736"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z" />
                <circle cx="12" cy="12" r="3" />
            </svg>
            @else
            {{-- Eye Closed SVG --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="#172736"
                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                <path d="M17.94 17.94A10.93 10.93 0 0112 20c-5.05 0-9.29-3.16-11-8 1.06-2.91 3.26-5.27 6-6.71" />
                <path d="M3 3l18 18" />
            </svg>
            @endif
            {{ $showForm ? 'Hide Form' : 'Add Branch' }}
        </button>
    </div>

    <style>
        .eye-icon {
            transition: transform 0.3s ease;
        }

        button:hover .eye-icon {
            transform: scale(1.15);
        }
    </style>

    {{-- Branch Table --}}
    @include('livewire.admin.partials.organization.branch-table')

</div>