<div class="col-md-3 text-center border-end">
    <!-- Centered Profile Image and User Info -->
    <div class="d-flex flex-column align-items-center">
        <img src="{{ auth()->user()->avatar_url ?? asset('images/default-user.jpg') }}" class="rounded-circle mb-2" width="80">
        <h6>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h6>
        <small>{{ auth()->user()->email }}</small>
    </div>

    <!-- Step Display Info -->
    <div class="mt-4">
        <div class="d-flex flex-column align-items-center gap-3">
            <!-- Date -->
            <div class="text-center fw-bold text-secondary">
                <div class="step-circle mb-1 bg-secondary text-white">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="small">Date</div>
                <p class="mt-2 text-secondary fw-semibold" id="calendar-picked-date">—</p>
            </div>

            <!-- Time -->
            <div class="text-center fw-bold text-secondary">
                <div class="step-circle mb-1 bg-secondary text-white">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="small">Time</div>
                <p class="mt-1 text-secondary fw-semibold" id="calendar-picked-time">—</p>
            </div>

            <!-- Summary Info -->
            <div class="text-center fw-bold text-secondary">
                <div class="step-circle mb-1 bg-secondary text-white">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="small">Summary Info</div>
                <p class="mt-1 text-secondary fw-semibold">—</p>
            </div>
        </div>
    </div>
</div>

<style>
    .step-circle {
        width: 30px;
        height: 30px;
        line-height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
</style>
