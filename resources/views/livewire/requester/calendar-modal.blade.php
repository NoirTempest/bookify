<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dateModalLabel">Selected Date & Time</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dateTimeForm">
                    <div class="mb-3">
                        <label for="selectedDateOnly" class="form-label">Date</label>
                        <input type="text" class="form-control" id="selectedDateOnly" name="date" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="selectedTimeOnly" class="form-label">Time</label>
                        <input type="text" class="form-control" id="selectedTimeOnly" name="time" readonly>
                    </div>
                    <!-- Add additional form fields here if needed -->
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>