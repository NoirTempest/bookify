<div class="px-4 pt-3 pb-2 border-bottom">
    <div class="d-flex justify-content-around text-center">
        <!-- Step 1 -->
        <div :class="{
                'fw-bold text-success': showCalendar || showConfirm,
                'fw-bold text-primary': !showCalendar && !showConfirm
            }">
            <div class="mb-1">Step 1</div>
            <div class="step-circle mb-1"
                 :class="{
                     'bg-success text-white': showCalendar || showConfirm,
                     'bg-primary text-white': !showCalendar && !showConfirm,
                     'bg-light border': !(showCalendar || showConfirm)
                 }">1</div>
            <div>Details</div>
        </div>

        <!-- Step 2 -->
        <div :class="{
                'fw-bold text-success': showConfirm,
                'fw-bold text-primary': showCalendar && !showConfirm
            }">
            <div class="mb-1">Step 2</div>
            <div class="step-circle mb-1"
                 :class="{
                     'bg-success text-white': showConfirm,
                     'bg-primary text-white': showCalendar && !showConfirm,
                     'bg-light border': !showCalendar
                 }">2</div>
            <div>Schedule</div>
        </div>

        <!-- Step 3 -->
        <div :class="{
                'fw-bold text-primary': showConfirm
            }">
            <div class="mb-1">Step 3</div>
            <div class="step-circle mb-1"
                 :class="{
                     'bg-primary text-white': showConfirm,
                     'bg-light border': !showConfirm
                 }">3</div>
            <div>Confirm</div>
        </div>
    </div>
</div>
