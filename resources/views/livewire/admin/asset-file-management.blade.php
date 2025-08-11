<div>
    {{-- Toggle Upload Form --}}
    <div class="mb-2">
        <button class="btn btn-sm btn-outline-dark" wire:click="$toggle('showForm')">
            {{ $showForm ? 'Hide Upload Form' : 'Add Image to Asset' }}
        </button>
    </div>

    {{-- Upload Success Message --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show small" role="alert">
        {{ session('message') }}
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Upload Form --}}
    @if($showForm)
    <div class="card shadow-sm border mb-3">
        <div class="card-header">
            <h6 class="mb-0">Upload Asset Image</h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row">
                    {{-- Left: Inputs --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="assetDetailId">Select Asset</label>
                            <select class="form-select @error('assetDetailId') is-invalid @enderror"
                                wire:model="assetDetailId" id="assetDetailId">
                                <option value="">-- Choose an asset --</option>
                                @foreach ($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->asset_name }}</option>
                                @endforeach
                            </select>
                            @error('assetDetailId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="fileAttachment">Upload Image</label>
                            <input type="file"
                                class="form-control @error('fileAttachment') is-invalid @enderror border border-secondary"
                                style="padding: 0.375rem 0.75rem;" wire:model="fileAttachment" id="fileAttachment"
                                accept="image/*">
                            @error('fileAttachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                            <button type="button" class="btn btn-sm btn-secondary" wire:click="cancel">Cancel</button>
                            <div wire:loading wire:target="fileAttachment" class="text-muted small ms-3">
                                Uploading...
                            </div>
                        </div>
                    </div>

                    {{-- Right: Preview --}}
                    <div class="col-md-6">
                        @if ($fileAttachment)
                        <label class="form-label">Image Preview:</label>
                        <img src="{{ $fileAttachment->temporaryUrl() }}" class="img-thumbnail w-50"
                            style="max-height: 300px;">
                        @else
                        <div class="text-muted small border p-3 rounded text-center">
                            No image selected
                        </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Uploaded Images Table --}}
    @include('livewire.admin.partials.assets.asset-files-table')
</div>