document.addEventListener("alpine:init", () => {
    Alpine.store("step", {
        showCalendar: false,
        showConfirm: false,
        purpose: "",
        date: "",
        time: "",
        assetDetailId: "",
    });

    Alpine.data("timePicker", () => ({
        selectedAssetType: "",
        vehicleTypeId: '{{ $vehicleTypeId ?? "1" }}', // Optional: dynamically pass this

        updateTimeSlot(event) {
            const selectedDate = document.getElementById("selected_date").value;

            if (!selectedDate) {
                Swal.fire(
                    "No Date Selected",
                    "Please choose a date first.",
                    "warning"
                );
                event.target.checked = false;
                return;
            }

            // Check required form fields before allowing time slot selection
            const { purpose, destination, seats } = Alpine.store("step");

            if (!purpose || !destination || !seats) {
                Swal.fire(
                    "Missing Info",
                    "Please complete purpose, destination, and number of seats.",
                    "warning"
                );
                event.target.checked = false;
                return;
            }

            const previousDate = Alpine.store("step").date;
            const previousTime = Alpine.store("step").time;

            // Handle All Day
            if (event.target.dataset.allDay === "true") {
                const newTime = "All Day";

                if (selectedDate === previousDate && previousTime === newTime) {
                    Swal.fire(
                        "Duplicate",
                        'You already selected "All Day" for this date.',
                        "warning"
                    );
                    event.target.checked = false;
                    return;
                }

                this.setAllDay();
                Alpine.store("step").date = selectedDate;
                return;
            }

            // Handle Time Range
            const [from, to] = event.target.value.split("-");
            const newDisplayTime = `${formatTime(from)} - ${formatTime(to)}`;

            if (
                selectedDate === previousDate &&
                previousTime === newDisplayTime
            ) {
                Swal.fire(
                    "Duplicate",
                    "You already selected this time slot for this date.",
                    "warning"
                );
                event.target.checked = false;
                return;
            }

            // Save values
            Alpine.store("step").time = newDisplayTime;
            Alpine.store("step").date = selectedDate;

            document.getElementById("time_from").value = from + ":00";
            document.getElementById("time_to").value = to + ":00";
            document.getElementById("display_time_from").value =
                formatTime(from);
            document.getElementById("display_time_to").value = formatTime(to);

            const pickedTimeElement = document.getElementById(
                "calendar-picked-time"
            );
            if (pickedTimeElement) {
                pickedTimeElement.textContent = newDisplayTime;
            }
        },

        setAllDay() {
            const displayTime = "All Day";
            document.getElementById("time_from").value = "00:00:00";
            document.getElementById("time_to").value = "23:59:00";
            document.getElementById("display_time_from").value = displayTime;
            document.getElementById("display_time_to").value = "";

            const pickedTimeElement = document.getElementById(
                "calendar-picked-time"
            );
            if (pickedTimeElement) {
                pickedTimeElement.textContent = displayTime;
            }

            Alpine.store("step").time = displayTime;
        },
    }));
});

function toggleMobileSidebar() {
    const sidebar = document.querySelector(".sidebar");
    if (sidebar) sidebar.classList.toggle("show");
}

document.addEventListener("DOMContentLoaded", () => {
    bindTimeSlotEvents();

    const form = document.getElementById("bookingForm");
    if (form) form.addEventListener("submit", ajaxSubmitBooking);

    const modal = document.getElementById("bookingModal");
    modal?.addEventListener("shown.bs.modal", () => {
        bindTimeSlotEvents();
        initCalendar();
    });
    modal?.addEventListener("hidden.bs.modal", () => {
        if (window.calendarInst) window.calendarInst.destroy();
        document.getElementById("bookingForm").reset();
        Alpine.store("step").showCalendar = false;
        Alpine.store("step").showConfirm = false;
    });
});

function bindTimeSlotEvents() {
    document.querySelectorAll('input[name="time_slot"]').forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.disabled) {
                Swal.fire(
                    "Unavailable",
                    "This time slot is already booked.",
                    "warning"
                );
                radio.checked = false;
                return;
            }

            if (radio.dataset.allDay === "true") {
                document.getElementById("time_from").value = "00:00:00";
                document.getElementById("time_to").value = "23:59:00";
                document.getElementById("display_time_from").value = "All Day";
                document.getElementById("display_time_to").value = "";
                document.getElementById("calendar-picked-time").textContent =
                    "All Day";
                Alpine.store("step").time = "All Day";
                return;
            }

            const [from, to] = radio.value.split("-");
            const displayTime = `${formatTime(from)} - ${formatTime(to)}`;
            document.getElementById("time_from").value = from + ":00";
            document.getElementById("time_to").value = to + ":00";
            document.getElementById("display_time_from").value =
                formatTime(from);
            document.getElementById("display_time_to").value = formatTime(to);
            document.getElementById("calendar-picked-time").textContent =
                displayTime;
            Alpine.store("step").time = displayTime;
        });
    });
}

function formatTime(hm) {
    const [h, m] = hm.split(":").map(Number);
    const d = new Date();
    d.setHours(h, m);
    return d.toLocaleTimeString([], {
        hour: "numeric",
        minute: "2-digit",
    });
}

function initCalendar() {
    const el = document.getElementById("calendar");
    if (!el) return;
    if (window.calendarInst) window.calendarInst.destroy();

    fetch("/api/bookings/dates")
        .then((r) => r.json())
        .then((data) => {
            const booked = data.map((e) => e.start);
            const today = new Date().toISOString().split("T")[0];

            window.calendarInst = new FullCalendar.Calendar(el, {
                initialView: "dayGridMonth",
                validRange: { start: today },
                events: data,
                dateClick(info) {
                    if (booked.includes(info.dateStr)) {
                        Swal.fire(
                            "Unavailable",
                            "This date is already booked",
                            "error"
                        );
                        return;
                    }

                    document.getElementById("selected_date").value =
                        info.dateStr;
                    document.getElementById(
                        "calendar-picked-date"
                    ).textContent = info.dateStr;
                    Alpine.store("step").date = info.dateStr;

                    loadSlotAvailability(info.dateStr);
                },
                eventDidMount(info) {
                    if (info.event.extendedProps.allDay || info.event.allDay) {
                        const el = info.el.closest(".fc-daygrid-day");
                        if (el) {
                            el.style.backgroundColor = "#f8d7da"; // light red
                            el.style.color = "#721c24"; // dark red text
                        }
                    }
                },
            });

            window.calendarInst.render();
        })
        .catch((err) => console.error("Calendar Load Error", err));
}

function loadSlotAvailability(dateStr) {
    fetch(`/api/bookings/slots?date=${dateStr}`)
        .then((r) => r.json())
        .then((slotsBooked) => {
            const bookedKeys = slotsBooked.map(
                (b) => `${b.time_from}-${b.time_to}`
            );

            document.querySelectorAll(".time-slot-radio").forEach((radio) => {
                const [from, to] = radio.value.split("-");
                const label = document.querySelector(
                    `label[for="${radio.id}"]`
                );
                const key = `${from}:00-${to}:00`;
                const selectedDate = Alpine.store("step").date;
                const selectedTime = Alpine.store("step").time;

                if (bookedKeys.includes(key)) {
                    // Already booked in DB
                    radio.disabled = true;
                    label.style.backgroundColor = "#f8d7da";
                    label.style.color = "#721c24";
                } else if (
                    selectedDate === dateStr &&
                    selectedTime ===
                        (to
                            ? `${formatTime(from)} - ${formatTime(to)}`
                            : "All Day")
                ) {
                    // Already selected by the user for this day
                    label.style.backgroundColor = "#f5c6cb"; // lighter red/pink
                    label.style.color = "#721c24";
                    label.title = "You already selected this slot";
                } else {
                    // Available
                    radio.disabled = false;
                    label.style.backgroundColor = "#d4edda";
                    label.style.color = "#155724";
                    label.title = "Available";
                }
            });
        })
        .catch(console.error);
}

function nextToConfirm() {
    const purpose = document.querySelector('[name="purpose"]').value;
    const date = document.getElementById("selected_date").value;
    const from = document.getElementById("display_time_from").value;
    const to = document.getElementById("display_time_to").value;

    if (!purpose || !date || !from) {
        return Swal.fire(
            "Missing Information",
            "Please complete purpose, date, and time selection.",
            "warning"
        );
    }

    document.getElementById("confirm_purpose").textContent = purpose;
    document.getElementById("confirm_date").textContent = date;
    document.getElementById("confirm_time").textContent = to
        ? `${from} - ${to}`
        : from;

    Alpine.store("step").showConfirm = true;
}

function ajaxSubmitBooking(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);

    fetch(form.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
        body: formData,
    })
        .then((r) => {
            if (!r.ok) throw r;
            return r.json();
        })
        .then((json) => {
            if (json.success) {
                const selectedDate =
                    document.getElementById("selected_date").value;
                const displayFrom =
                    document.getElementById("display_time_from").value;
                const displayTo =
                    document.getElementById("display_time_to").value;

                Swal.fire({
                    icon: "success",
                    title: "Your request is submitted!",
                    html: `
                    <div style="display: flex; justify-content: center; gap: 10px; font-size: 12px; margin-bottom: 8px;">
                        <span><i class="fa fa-calendar"></i> <strong>${selectedDate}</strong></span>
                        <span><i class="fa fa-clock"></i> <strong>${displayFrom} - ${displayTo}</strong></span>
                    </div>
                    <p style="font-size: 12px;">Your booking request has been submitted successfully.</p>
                    <p style="font-size: 12px;">Please wait for an email confirmation once it has been approved.</p>
                `,
                    confirmButtonText: "OK",
                });

                bootstrap.Modal.getInstance(
                    document.getElementById("bookingModal")
                ).hide();
            } else if (json.errors) {
                Swal.fire(
                    "Validation Error",
                    Object.values(json.errors).flat().join("<br>"),
                    "error"
                );
            } else {
                Swal.fire("Error", "Unexpected server response", "error");
            }
        })
        .catch((err) => {
            err.text?.().then((txt) =>
                console.error("Server error body:", txt)
            );
            Swal.fire("Error", "Network or server error.", "error");
        });
}

function addGuestInput() {
    const guestInputs = document.getElementById("guestInputs");
    const newInput = document.createElement("div");
    newInput.classList.add("input-group", "mb-2");
    newInput.innerHTML = `
        <input type="email" name="guests[]" class="form-control" placeholder="guest@example.com">
        <button class="btn btn-outline-danger" type="button" onclick="this.parentNode.remove()">-</button>
    `;
    guestInputs.appendChild(newInput);
}

function updateSummary() {
    const purpose = document.querySelector('[name="purpose"]').value;
    const date = document.getElementById("selected_date").value;
    const from = document.getElementById("display_time_from").value;
    const to = document.getElementById("display_time_to").value;

    if (!purpose || !date || !from) {
        return Swal.fire(
            "Missing Info",
            "Complete purpose, date, and time.",
            "warning"
        );
    }

    document.getElementById("confirm_purpose").textContent = purpose;
    document.getElementById("confirm_date").textContent = date;
    document.getElementById("confirm_time").textContent = to
        ? `${from} - ${to}`
        : from;

    Alpine.store("step").purpose = purpose;
    Alpine.store("step").date = date;
    Alpine.store("step").time = to ? `${from} - ${to}` : from;
}
