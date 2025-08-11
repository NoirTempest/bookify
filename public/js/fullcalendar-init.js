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
            const clickedDateUTC = new Date(info.date);

            // Convert UTC to Philippine Time (UTC+8)
            const clickedDate = new Date(
                clickedDateUTC.getTime() + 8 * 60 * 60 * 1000
            );

            // Format date as YYYY-MM-DD
            const dateInput = document.getElementById("selectedDateOnly");
            if (dateInput) {
                dateInput.value = clickedDate.toISOString().slice(0, 10);
                dateInput.dispatchEvent(new Event("input", { bubbles: true }));
            }

            // Format time as HH:MM
            const timeInput = document.getElementById("selectedTimeOnly");
            if (timeInput) {
                const hours = clickedDate
                    .getHours()
                    .toString()
                    .padStart(2, "0");
                const minutes = clickedDate
                    .getMinutes()
                    .toString()
                    .padStart(2, "0");
                timeInput.value = `${hours}:${minutes}`;
                timeInput.dispatchEvent(new Event("input", { bubbles: true }));
            }

            // Show the modal
            const dateModalEl = document.getElementById("dateModal");
            if (dateModalEl) {
                const modalInstance =
                    bootstrap.Modal.getOrCreateInstance(dateModalEl);
                modalInstance.show();
            }
        },
    });

    window.calendar = calendar;
    calendar.render();

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
