<div wire:ignore.self class="modal fade" id="editBookingModal" tabindex="-1" aria-labelledby="editBookingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @if($booking)
            <div class="modal-header">
                <h5 class="modal-title">Edit Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="update">
                    <div class="mb-3">
                        <label>Status</label>
                        <select wire:model="status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    Livewire.on('show-edit-modal', () => {
        new bootstrap.Modal(document.getElementById('editBookingModal')).show();
    });
    Livewire.on('hide-edit-modal', () => {
        bootstrap.Modal.getInstance(document.getElementById('editBookingModal')).hide();
    });
</script>
@endpush