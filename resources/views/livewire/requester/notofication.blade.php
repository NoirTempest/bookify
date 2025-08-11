<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Notifications</h1>
            <p class="text-muted">Stay updated with your booking activities</p>
        </div>
        <div class="btn-group">
            <button wire:click="markAllAsRead" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-check-double"></i> Mark All Read
            </button>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group" role="group">
                    <button wire:click="setFilter('all')" 
                            class="btn {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        All
                        @if($unreadCount > 0)
                            <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <button wire:click="setFilter('recent')" 
                            class="btn {{ $filter === 'recent' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Recent Activity
                    </button>
                    <button wire:click="setFilter('approved')" 
                            class="btn {{ $filter === 'approved' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-check-circle text-success"></i> Approved
                    </button>
                    <button wire:click="setFilter('rejected')" 
                            class="btn {{ $filter === 'rejected' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="fas fa-times-circle text-danger"></i> Rejected
                    </button>
                </div>
                <small class="text-muted">{{ $notifications->total() }} notifications</small>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($notifications as $booking)
                        @php
                            $isUpcoming = $booking->status === 'approved' && \Carbon\Carbon::parse($booking->scheduled_date)->isFuture();
                            $isRecent = $booking->updated_at->isAfter(now()->subDays(3));
                            $statusColors = [
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'secondary'
                            ];
                            $color = $statusColors[$booking->status] ?? 'secondary';
                        @endphp
                        
                        <div class="list-group-item list-group-item-action {{ $isRecent ? 'bg-light' : '' }}" 
                             wire:click="viewBooking({{ $booking->id }})"
                             style="cursor: pointer;">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="d-flex align-items-start">
                                    <!-- Notification Icon -->
                                    <div class="me-3 mt-1">
                                        @if($booking->status === 'approved')
                                            @if($isUpcoming)
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                    <i class="fas fa-calendar-check text-primary"></i>
                                                </div>
                                            @else
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                </div>
                                            @endif
                                        @elseif($booking->status === 'rejected')
                                            <div class="bg-danger bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-times-circle text-danger"></i>
                                            </div>
                                        @elseif($booking->status === 'cancelled')
                                            <div class="bg-secondary bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-ban text-secondary"></i>
                                            </div>
                                        @else
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                <i class="fas fa-clock text-warning"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Notification Content -->
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1 fw-semibold">
                                                @if($booking->status === 'approved' && $isUpcoming)
                                                    Upcoming Booking Reminder
                                                @elseif($booking->status === 'approved')
                                                    Booking Approved
                                                @elseif($booking->status === 'rejected')
                                                    Booking Rejected
                                                @elseif($booking->status === 'cancelled')
                                                    Booking Cancelled
                                                @else
                                                    Booking Status Update
                                                @endif
                                            </h6>
                                            <span class="badge bg-{{ $color }}">{{ ucfirst($booking->status) }}</span>
                                        </div>
                                        
                                        <p class="mb-2 text-muted">
                                            <strong>{{ $booking->assetType->name ?? 'Asset' }}</strong>
                                            @if($booking->assetDetail)
                                                - {{ $booking->assetDetail->name }}
                                            @endif
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->scheduled_date)->format('M d, Y') }}
                                                <i class="fas fa-clock ms-3 me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking->time_from)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($booking->time_to)->format('H:i') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $booking->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        
                                        @if($booking->status === 'approved' && $isUpcoming)
                                            <div class="mt-2">
                                                <div class="alert alert-info py-2 mb-0">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <small>Your booking is scheduled for {{ \Carbon\Carbon::parse($booking->scheduled_date)->format('l, M d') }}</small>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if($booking->status === 'rejected' && $booking->rejection_reason)
                                            <div class="mt-2">
                                                <div class="alert alert-warning py-2 mb-0">
                                                    <small><strong>Reason:</strong> {{ $booking->rejection_reason }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="ms-3">
                                    <button class="btn btn-outline-primary btn-sm" 
                                            wire:click.stop="viewBooking({{ $booking->id }})"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                    <div class="mt-3">
                        <h5 class="text-muted">No notifications found</h5>
                        <p class="text-muted">
                            @if($filter === 'all')
                                You don't have any notifications at the moment.
                            @elseif($filter === 'recent')
                                No recent activity in the last 7 days.
                            @elseif($filter === 'approved')
                                No recently approved bookings.
                            @elseif($filter === 'rejected')
                                No recently rejected bookings.
                            @endif
                        </p>
                        <a href="{{ route('requester.bookings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Booking
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $notifications->links() }}
        </div>
    @endif
</div>