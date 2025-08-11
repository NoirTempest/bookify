<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trip Ticket</title>

    <!-- Poppins Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            padding: 30px;
        }

        .ticket {
            max-width: 500px;
            margin: auto;
            width: 400px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 20px 25px;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
        }

        .label {
            font-weight: 600;
            width: 160px;
            display: inline-block;
        }

        .bold {
            font-weight: 600;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
        }
         .label-title {
            font-size: 18px;
            font-weight: 600;
        }

        textarea.form-control[readonly] {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            font-weight: 600;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            .ticket,
            .ticket * {
                visibility: visible;
            }

            body {
                background: none;
                padding: 0;
            }

            .ticket {
                position: absolute;
                left: 0;
                top: 0;
                border: none;
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="ticket">
        <h5 class="mb-4 section-title">Trip Ticket</h5>

        <div class="mb-2"><span class="label">Requester name:</span> <span class="bold">{{ $booking->user->first_name }} {{ $booking->user->last_name }}</span></div>
        <div class="mb-2"><span class="label">Purpose:</span> <span class="bold">{{ $booking->purpose ?? '—' }}</span></div>
        <div class="mb-2"><span class="label">No. of Persons:</span> <span class="bold">{{ $booking->no_of_seats ?? '—' }}</span></div>
        <div class="mb-2"><span class="label">Booking Date:</span> <span class="bold">{{ \Carbon\Carbon::parse($booking->scheduled_date)->format('F d, Y') }}</span></div>
        <div class="mb-2">
            <span class="label">Time:</span>
            <span class="bold">
                {{ \Carbon\Carbon::parse($booking->time_from)->format('g:i A') }} –
                {{ \Carbon\Carbon::parse($booking->time_to)->format('g:i A') }}
            </span>
        </div>

        @php $vehicle = $booking->vehicleAssignment?->assetDetail; @endphp
        <div class="mb-2"><span class="label">Vehicle:</span> <span class="bold">{{ $vehicle->asset_name ?? '—' }}</span></div>
        <div class="mb-2"><span class="label">Plate No.:</span> <span class="bold">{{ $vehicle->plate_number ?? '—' }}</span></div>

        @php $driver = $booking->vehicleAssignment?->driver; @endphp
        <div class="mb-2"><span class="label">Driver:</span> <span class="bold">{{ $driver->name ?? '—' }}</span></div>

        <div class="mb-2">
            <span class="label-title">Odometer Reading</span><br>
            <span class="ms-4"><strong>Start:</strong></span>
            <span class="bold">
                {{ isset($booking->vehicleAssignment->odometer_start) ? number_format($booking->vehicleAssignment->odometer_start, 0) : '__________' }}
            </span>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="ms-4"><strong>End:</strong></span>
            <span class="bold">
                {{ isset($booking->vehicleAssignment->odometer_end) ? number_format($booking->vehicleAssignment->odometer_end, 0) : '__________' }}
            </span>
        </div>

        <div class="mb-3">
            <label class="label-title">Notes / Remarks:</label>
            <textarea class="form-control" rows="3" readonly>{{ $booking->notes ?? '—' }}</textarea>
        </div>

        <hr>
        <p class="text-muted small">Printed on {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>
