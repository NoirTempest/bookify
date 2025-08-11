<div wire:ignore.self class="modal fade" id="viewBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            @if($booking)
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Asset:</strong> {{ $booking->assetType->name }}</p>
                <p><strong>Status:</strong> {{ ucfirst($booking->status) }}</p>
                <p><strong>Updated:</strong> {{ $booking->updated_at }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    Livewire.on('show-view-modal', () => {
        new bootstrap.Modal(document.getElementById('viewBookingModal')).show();
    });
</script>
@endpush