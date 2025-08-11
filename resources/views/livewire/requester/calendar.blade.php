<div>
    <div class="container py-4">
        <div id="calendar"></div>
    </div>

    @php
        $assetTypes = \App\Models\AssetType::all();
        $assetDetails = \App\Models\AssetDetail::all();
    @endphp
    @include('livewire.requester.modal.bookingModal', compact('assetTypes', 'assetDetails'))

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-custom.css') }}">
    @endpush

    @push('scripts')
    <script>
        window.calendarEvents = @json($events);
    </script>
    <script src="{{ asset('js/fullcalendar-init.js') }}"></script>
    <script src="{{ asset('js/custom.js') }}"></script>
    @endpush
    
</div>