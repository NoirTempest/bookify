<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Booking Approval</title>
</head>

<body style="font-family: 'Poppins', sans-serif; line-height: 1.6; color: #333;">

    @if ($recipientType === 'requester')
    <h2>Hello {{ $booking->user->first_name }},</h2>
    <p>Your booking for <strong>{{ $booking->assetType->name }}</strong> has been approved.</p>
    @elseif ($recipientType === 'guest' && $guestEmail)
    @php $guestName = ucfirst(strtok($guestEmail, '@')) ?? 'Guest'; @endphp
    <h2>Hello {{ $guestName }},</h2>
    <p>You are a guest on a booking made by <strong>{{ $booking->user->first_name }}</strong>.</p>
    @endif

    <ul>
        <li><strong>Asset Type:</strong> {{ $booking->assetType->name }}</li>
        <li><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->scheduled_date)->format('F j, Y') }}</li>
        <li><strong>Time:</strong> {{ \Carbon\Carbon::parse($booking->time_from)->format('g:i A') }} – {{
            \Carbon\Carbon::parse($booking->time_to)->format('g:i A') }}</li>

        {{-- Smart Venue Label --}}
        @php
        $venueLabel = match((int) $booking->asset_type_id) {
        1 => 'Venue', // Conference Room
        2 => 'Destination', // Vehicle
        default => 'Venue'
        };
        $venueValue = $booking->assetDetail->location ?? $booking->destination ?? '—';
        @endphp
        <li><strong>{{ $venueLabel }}:</strong> {{ $venueValue }}</li>
    </ul>

    <p>Thank you.</p>
</body>

</html>