<div>
    <div class="p-3">
        <div class="d-flex justify-content-between mb-3">
            <h5></h5>
            <button wire:click="$set('showForm', true)" class="btn btn-sm text-white" style="background-color:#172736;">
                <i class="bi bi-plus-circle me-1"></i> Add New Driver
            </button>
        </div>

        @if ($showForm)
        <div class="card mb-3">
            <div class="card-body">
                <form wire:submit.prevent="{{ $editMode ? 'update' : 'save' }}">
                    <div class="mb-2">
                        <label for="name" class="form-label">Full Name</label>
                        <input wire:model="name" type="text" class="form-control" id="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" role="switch" id="activeSwitch"
                            wire:model="is_active">
                        <label class="form-check-label" for="activeSwitch">Active</label>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-success">
                            {{ $editMode ? 'Update' : 'Save' }}
                        </button>
                        <button type="button" wire:click="resetForm" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @include('livewire.admin.partials.driver-table', ['drivers' => $drivers])
    </div>



    <script>
        document.addEventListener('livewire:load', () => {
                const notyf = new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } });
        
                Livewire.on('notify', message => {
                    notyf.success(message);
                });
        
                Livewire.on('confirmDelete', id => {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'This driver will be deleted permanently.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('delete', id);
                        }
                    });
                });
            });
    </script>
</div>