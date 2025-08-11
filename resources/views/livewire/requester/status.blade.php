<div>
    <h2>Conference Room Status</h2>
    <p>This is the Livewire Status component.</p>
</div>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Conference Room Status</h2>
            <button wire:click="refresh" class="btn btn-primary btn-sm">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Current Bookings -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> Currently In Use
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($currentBookings) > 0)
                        <div class="row">
                            @foreach($currentBookings as $booking)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <h6 class="card-title text-success">{{ $booking->assetDetail->name ?? 'Conference Room' }}</h6>
                                            <p class="card-text">
                                                <strong>Time:</strong> {{ $booking->time_from }} - {{ $booking->time_to }}<br>
                                                <strong>User:</strong> {{ $booking->user->full_name ?? 'N/A' }}
                                            </p>
                                            <span class="badge bg-success">In Use</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                            <h4 class="text-success mt-3">All Rooms Available</h4>
                            <p class="text-muted">No conference rooms are currently in use.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> Upcoming Today
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($upcomingBookings) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Room</th>
                                        <th>Time</th>
                                        <th>User</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingBookings as $booking)
                                        <tr>
                                            <td>
                                                <strong>{{ $booking->assetDetail->name ?? 'Conference Room' }}</strong>
                                            </td>
                                            <td>
                                                {{ $booking->time_from }} - {{ $booking->time_to }}
                                            </td>
                                            <td>
                                                {{ $booking->user->full_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-warning">Reserved</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-check text-info" style="font-size: 3rem;"></i>
                            <h4 class="text-info mt-3">No More Bookings Today</h4>
                            <p class="text-muted">All rooms are free for the rest of the day.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Auto refresh every 30 seconds -->
    <script>
        setInterval(function() {
            @this.call('refresh');
        }, 30000);
    </script>
</div>
