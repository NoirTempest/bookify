<div>
    <div class="container py-4">
        <div id="calendar"></div>
    </div>

    {{-- Livewire Component for Modal --}}
    <livewire:requester.conference-room-booking />

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar-custom.css') }}">
    @endpush

    @push('scripts')
    <script>
        window.calendarEvents = @json($events);
    </script>
    <script src="{{ asset('js/fullcalendar-init.js') }}"></script>
    @endpush
    
</div>