<div>
    @if($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-body">
            <form wire:submit.prevent="{{ $editMode ? 'update' : 'store' }}">
                <div class="mb-3">
                    <label class="form-label">Asset Type</label>
                    <input type="text" wire:model.defer="assetTypeName" class="form-control" />
                    @error('assetTypeName') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary">{{ $editMode ? 'Update' : 'Add' }}</button>
                    <button type="button" wire:click="cancel" class="btn btn-sm btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="mb-2">
        <button class="btn btn-sm btn-outline-dark" wire:click="$toggle('showForm')">
            {{ $showForm ? 'Hide Form' : 'Add Asset Type' }}
        </button>
    </div>

    @include('livewire.admin.partials.assets.asset-type-table')
</div>