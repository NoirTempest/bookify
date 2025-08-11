<div>
    @if ($bookings->isNotEmpty())
    @foreach ($bookings as $booking)
    <div style="background-color: #D6F4DE; border-top: 4px solid #34C759; border-bottom: 4px solid #34C759;"
        class="text-dark py-2 px-3 mb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div class="fw-bold d-flex align-items-start gap-2">
                <div style="width: 28px; height: 28px; background-color: #030303; color: white;
                            border-radius: 50%; display: flex; align-items: center; justify-content: center;
                            font-weight: bold; font-size: 16px; box-shadow: 0 0 2px rgba(0,0,0,0.2);">
                    i
                </div>
                <div>
                    <div>{{ $booking->assetDetail->asset_name ?? '—' }} : {{ $booking->purpose }}</div>
                </div>
            </div>
            <div class="fw-bold">
                <small class="text-muted">
                    {{ \Carbon\Carbon::parse($booking->scheduled_date)->isoFormat('dddd, MMMM D, YYYY') }}

                    {{ \Carbon\Carbon::parse($booking->time_from)->format('h:i A') }} -
                    {{ \Carbon\Carbon::parse($booking->time_to)->format('h:i A') }}
                </small><span class="bg-success text-white px-2 py-1 rounded">Meeting in Progress</span>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="py-2 px-3"
        style="background-color: #f8d7da; color: #842029; border-top: 2px solid #f5c2c7; border-bottom: 2px solid #f5c2c7;">
        No approved conference/meeting room bookings in progress.
    </div>



    @endif
</div>