document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");

    // Set initial events from global data
    window.calendarEvents = window.calendarEvents || [];
    window.originalEvents = [...window.calendarEvents];

    const events = window.calendarEvents.map((event) => ({
        ...event,
        allDay: false,
        start: event.start,
        end: event.end,
    }));
    // aa
    function toLocalHM(date) {
        const h = String(date.getHours()).padStart(2, "0");
        const m = String(date.getMinutes()).padStart(2, "0");
        return `${h}:${m}`;
    }
    function toLocalYMD(date) {
        const y = date.getFullYear();
        const mo = String(date.getMonth() + 1).padStart(2, "0");
        const d = String(date.getDate()).padStart(2, "0");
        return `${y}-${mo}-${d}`;
    }

    // Try to notify Livewire component to open and populate the modal.
    function emitToLivewire(payload) {
        try {
            if (window.Livewire) {
                if (typeof window.Livewire.emit === "function") {
                    window.Livewire.emit("calendarDateClicked", payload);
                    return true;
                }
                if (typeof window.Livewire.dispatch === "function") {
                    window.Livewire.dispatch("calendarDateClicked", payload);
                    return true;
                }
            }
        } catch (e) {
            console.warn("Livewire dispatch failed:", e);
        }
        return false;
    }

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        timeZone: "local",
        nowIndicator: true,
        now: new Date().toISOString(),
        height: 650,

        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
        },

        dayMaxEventRows: true,
        views: {
            dayGridMonth: {
                dayMaxEventRows: 3,
            },
        },

        events: events,

        selectable: true,
        selectMirror: true,

        select: function (info) {
            const start = info.start;
            const end = info.end || new Date(start.getTime() + 30 * 60 * 1000);

            // Prefer Livewire-driven workflow
            const payload = {
                start: start.toISOString(),
                end: end.toISOString(),
            };
            if (emitToLivewire(payload)) {
                calendar.unselect();
                return;
            }

            // Fallback: directly patch inputs and show modal
            const viewType = calendar.view.type;
            const dateInput = document.getElementById("selectedDateOnly");
            const timeFromInput = document.getElementById("selectedTimeOnly");
            const timeToInput = document.getElementById("timeTo");

            if (dateInput) {
                const ymd = toLocalYMD(start);
                dateInput.value = ymd;
                dateInput.dispatchEvent(new Event("input", { bubbles: true }));
            }

            if (viewType === "dayGridMonth") {
                // Month view: set only date, clear times
                if (timeFromInput) {
                    timeFromInput.value = "";
                    timeFromInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
                if (timeToInput) {
                    timeToInput.value = "";
                    timeToInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
            } else {
                // Timegrid views: set only start time (user will choose end)
                const hm = toLocalHM(start);
                if (timeFromInput) {
                    timeFromInput.value = hm;
                    timeFromInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
                if (timeToInput) {
                    timeToInput.value = "";
                    timeToInput.dispatchEvent(new Event("input", { bubbles: true }));
                }

                const dateModalEl = document.getElementById("dateModal");
                if (dateModalEl) {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(dateModalEl);
                    modalInstance.show();
                }
            }

            calendar.unselect();
        },

        // Style events based on timeline_status
        eventDidMount: function (info) {
            const props = info.event.extendedProps;
            let bgColor = "#ecf0f1";
            let borderColor = "#bdc3c7";

            switch (props.timeline_status) {
                case "Ongoing":
                    bgColor = "#d1ecf1";
                    borderColor = "#17a2b8";
                    break;
                case "Ended":
                    bgColor = "#f8d7da";
                    borderColor = "#dc3545";
                    break;
                case "Incoming":
                    bgColor = "#e2f0d9";
                    borderColor = "#28a745";
                    break;
            }

            const isVehicle = props.asset_type?.toLowerCase() === "vehicle";
            const title =
                isVehicle && props.driver_name
                    ? props.driver_name
                    : props.asset_name;

            info.el.innerHTML = `
                <div style="font-size: 14px;">${title}</div>
                <div style="height: 14px;"></div> 
                <div style="position: absolute; bottom: 4px; right: 6px; font-size: 0.7rem; color: ${borderColor};">
                    ${props.timeline_status ?? ""}
                </div>
            `;

            Object.assign(info.el.style, {
                backgroundColor: bgColor,
                border: `2px solid ${borderColor}`,
                borderRadius: "8px",
                padding: "4px 6px",
                color: "#2C3E50",
                display: "flex",
                flexDirection: "column",
                justifyContent: "flex-start",
                height: "100%",
                wordBreak: "break-word",
                whiteSpace: "normal",
                position: "relative",
            });
        },

        // Show popup on event click
        eventClick: function (info) {
            const props = info.event.extendedProps;
            const tooltip = `
                <strong>${props.requested_by}</strong><br>
                <b>Asset:</b> ${props.asset_name}<br>
                <b>Purpose:</b> ${props.purpose}<br>
                <b>Asset Type:</b> ${props.asset_type}<br>
                <b>Venue:</b> ${props.venue}<br>
                <b>Status:</b> ${props.status}<br>
                ${
                    props.driver_name
                        ? `<b>Driver:</b> ${props.driver_name}<br>`
                        : ""
                }
                <b>Clock:</b> ${props.timeline_status}
            `;

            Swal.fire({
                title: "Booking Details",
                html: tooltip,
                confirmButtonText: "Close",
            });
        },

        // Handle date clicks for modal input
        dateClick: function (info) {
            const d = info.date;
            const end = new Date(d.getTime() + 30 * 60 * 1000);

            // Prefer Livewire-driven workflow
            const payload = {
                start: d.toISOString(),
                end: end.toISOString(),
            };
            if (emitToLivewire(payload)) {
                return;
            }

            // Fallback: directly patch inputs and show modal
            const viewType = calendar.view.type;
            const dateInput = document.getElementById("selectedDateOnly");
            const timeFromInput = document.getElementById("selectedTimeOnly");
            const timeToInput = document.getElementById("timeTo");

            if (dateInput) {
                const ymd = toLocalYMD(d);
                dateInput.value = ymd;
                dateInput.dispatchEvent(new Event("input", { bubbles: true }));
            }

            if (viewType === "dayGridMonth") {
                // Month view: do not auto-fill times
                if (timeFromInput) {
                    timeFromInput.value = "";
                    timeFromInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
                if (timeToInput) {
                    timeToInput.value = "";
                    timeToInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
            } else {
                // Day/Week timegrid: auto-fill start time only (Asia/Manila)
                const hm = toLocalHM(d);
                if (timeFromInput) {
                    timeFromInput.value = hm;
                    timeFromInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
                if (timeToInput) {
                    timeToInput.value = "";
                    timeToInput.dispatchEvent(new Event("input", { bubbles: true }));
                }
            }

            // Always show the modal after click
            const dateModalEl = document.getElementById("dateModal");
            if (dateModalEl) {
                const modalInstance = bootstrap.Modal.getOrCreateInstance(dateModalEl);
                modalInstance.show();
            }
        },
    });

    window.calendar = calendar;
    calendar.render();

    // Listen for Livewire browser events to control the modal
    window.addEventListener("show-booking-modal", () => {
        const el = document.getElementById("dateModal");
        if (!el) return;
        bootstrap.Modal.getOrCreateInstance(el).show();
    });

    window.addEventListener("close-booking-modal", () => {
        const el = document.getElementById("dateModal");
        if (!el) return;
        const instance =
            bootstrap.Modal.getInstance(el) ||
            bootstrap.Modal.getOrCreateInstance(el);
        instance.hide();
    });

    // Filter calendar by asset/driver
    window.filterCalendarByAsset = function () {
        const selected = document
            .getElementById("assetFilter")
            .value.toLowerCase();
        const filtered = selected
            ? window.originalEvents.filter(
                  (e) =>
                      (e.asset_name &&
                          e.asset_name.toLowerCase() === selected) ||
                      (e.driver_name &&
                          e.driver_name.toLowerCase() === selected)
              )
            : window.originalEvents;

        window.calendar.removeAllEvents();
        window.calendar.addEventSource(filtered);
    };

    // Add new booking dynamically via Livewire
    window.addEventListener("bookingSubmitted", (event) => {
        const booking = event.detail.booking;

        calendar.addEvent({
            title: booking.title,
            start: booking.start,
            end: booking.end,
            allDay: false,
        });

        const modal = bootstrap.Modal.getInstance(
            document.getElementById("dateModal")
        );
        if (modal) modal.hide();
    });
});
