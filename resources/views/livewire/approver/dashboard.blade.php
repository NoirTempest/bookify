<div class="container py-4">

    <h4 class="fw-bold mb-4">Booking Summary</h4>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
        @foreach ($stats as $label => $value)
        @php
        $style = match (true) {
        Str::contains($label, 'Approved') => [
        'bg' => 'bg-light-success',
        'border' => 'border-start-success',
        'text' => 'text-success',
        'icon' => 'bi-check-circle-fill',
        ],
        Str::contains($label, 'Pending') => [
        'bg' => 'bg-light-warning',
        'border' => 'border-start-warning',
        'text' => 'text-warning',
        'icon' => 'bi-clock-fill',
        ],
        Str::contains($label, 'Rejected') => [
        'bg' => 'bg-light-danger',
        'border' => 'border-start-danger',
        'text' => 'text-danger',
        'icon' => 'bi-x-circle-fill',
        ],
        Str::contains($label, 'Ended') => [
        'bg' => 'bg-light-secondary',
        'border' => 'border-start-secondary',
        'text' => 'text-secondary',
        'icon' => 'bi-calendar-check-fill',
        ],
        Str::contains($label, 'Upcoming') => [
        'bg' => 'bg-light-info',
        'border' => 'border-start-info',
        'text' => 'text-info',
        'icon' => 'bi-arrow-up-circle-fill',
        ],
        default => [
        'bg' => 'bg-light-primary',
        'border' => 'border-start-primary',
        'text' => 'text-primary',
        'icon' => 'bi-info-circle-fill',
        ],
        };
        @endphp

        <div class="col">
            <div class="card banner-card shadow-sm {{ $style['bg'] }} {{ $style['border'] }} border-5 hover-effect">
                <div class="card-body d-flex align-items-center p-3 gap-3">
                    <div class="icon-circle {{ $style['text'] }}">
                        <i class="bi {{ $style['icon'] }} fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 {{ $style['text'] }}">{{ $value }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>