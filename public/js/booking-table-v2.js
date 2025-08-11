document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("bookingSearch");
    const tableBody = document.querySelector("#bookingTable tbody");

    // 🔍 Live search filter
    if (searchInput && tableBody) {
        searchInput.addEventListener("input", function () {
            const filter = searchInput.value.toLowerCase();
            const rows = tableBody.querySelectorAll("tr");

            rows.forEach((row) => {
                const cells = row.querySelectorAll("td");
                let match = false;

                cells.forEach((cell, index) => {
                    if (index < cells.length - 1) {
                        const text = cell.textContent.toLowerCase();
                        if (text.includes(filter)) match = true;
                    }
                });

                row.style.display = filter === "" || match ? "" : "none";
            });
        });
    }

    // 📅 Bootstrap modal handlers
    const modals = {
        details: document.getElementById("bookingDetailsModal"),
        disapprove: document.getElementById("disapproveModal"),
    };

    document.addEventListener("open-details-modal", () => {
        new bootstrap.Modal(modals.details).show();
    });

    document.addEventListener("close-details-modal", () => {
        const modal = bootstrap.Modal.getInstance(modals.details);
        if (modal) modal.hide();
    });

    document.addEventListener("open-disapprove-modal", () => {
        new bootstrap.Modal(modals.disapprove).show();
    });

    document.addEventListener("close-disapprove-modal", () => {
        const modal = bootstrap.Modal.getInstance(modals.disapprove);
        if (modal) modal.hide();
    });

    // ✅ Approve booking with confirmation
    document.body.addEventListener("click", function (e) {
        const approveBtn = e.target.closest(".approve-button");
        if (approveBtn?.dataset.id) {
            const bookingId = approveBtn.dataset.id;
            const componentId = document
                .querySelector("[wire\\:id]")
                ?.getAttribute("wire:id");

            Swal.fire({
                title: "Confirmation Approval",
                text: "Are you sure you want to approve this request?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Approve",
                cancelButtonText: "Cancel",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed && componentId) {
                    Livewire.find(componentId).call(
                        "approveBooking",
                        bookingId
                    );
                }
            });
        }
    });

    // ❌ Disapprove booking (open modal)
    document.body.addEventListener("click", function (e) {
        const disBtn = e.target.closest(".disapprove-button");
        if (disBtn?.dataset.id) {
            const bookingId = disBtn.dataset.id;
            const componentId = document
                .querySelector("[wire\\:id]")
                ?.getAttribute("wire:id");

            if (componentId) {
                Livewire.find(componentId).call(
                    "openDisapproveModal",
                    bookingId
                );
            }
        }
    });

    // ✅ SweetAlert from Livewire status event (JS controlled only)
    document.addEventListener("approved-status", (e) => {
        const status = e.detail[0]?.status;
        console.log("📦 Received approval status from Livewire:", status);

        const showConfirmationAndSuccess = (title, text) => {
            Swal.fire({
                title: "Confirmation Approval",
                text: "Are you sure you want to approve this request? This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Approve",
                cancelButtonText: "Cancel",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: title,
                        text: text,
                        timer: 2500,
                        showConfirmButton: false,
                    });
                }
            });
        };

        switch (status) {
            case "already":
                Swal.fire({
                    icon: "warning",
                    title: "Already Approved",
                    text: "You have already approved this booking.",
                });
                break;

            case "first":
                showConfirmationAndSuccess(
                    "First Approval Complete",
                    "You are the first approver for this booking."
                );
                break;

            case "second":
                showConfirmationAndSuccess(
                    "Booking Fully Approved",
                    "You are the second approver. Booking is now fully approved."
                );
                break;

            case "fully":
                Swal.fire({
                    icon: "info",
                    title: "Already Fully Approved",
                    text: "This booking has already been approved by both approvers.",
                });
                break;

            default:
                console.warn("⚠️ Unexpected status received:", status);
                Swal.fire({
                    icon: "error",
                    title: "Unknown Status",
                    text: "An unexpected approval status occurred.",
                });
        }
    });

    // ❌ Disapproval feedback
    document.addEventListener("disapproval-status", (e) => {
        const status = e.detail[0]?.status;
        console.log("📦 Received disapproval status from Livewire:", status);

        if (status === "done") {
            Swal.fire({
                icon: "error",
                title: "Request Disapproved",
                text: "You disapproved the request.",
            });
        } else {
            Swal.fire({
                icon: "warning",
                title: "Disapproval Incomplete",
                text: "Something went wrong disapproving the request.",
            });
        }
    });

    // 🔄 Clear selected booking after modal close
    document.addEventListener("clear-selected-booking", () => {
        const componentId = document
            .querySelector("[wire\\:id]")
            ?.getAttribute("wire:id");
        if (componentId) {
            Livewire.find(componentId).call("clearSelectedBooking");
        }
    });
});
