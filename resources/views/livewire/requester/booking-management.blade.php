<div class="container py-5">
    <div class="row justify-content-center mt-4">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Booking Summary</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Booking ID</th>
                                <th>User</th>
                                <th>Asset Type</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bookings as $index => $booking)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $booking->id }}</td>
                                    <td>{{ $booking->user->first_name }} {{ $booking->user->last_name }}</td>
                                    <td>{{ $booking->assetType->name ?? 'N/A' }}</td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($booking->scheduled_date)->format('M d, Y') }}<br>
                                        {{ $booking->time_from }} - {{ $booking->time_to }}
                                    </td>
                                    <td>
                                        @php $status = $booking->status; @endphp
                                        @if ($status === 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No bookings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
