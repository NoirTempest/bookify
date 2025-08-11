document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("bookingSearch");
    const tableBody = document.querySelector("#bookingTable tbody");

    // Live search
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

    // Modal open/close handlers
    document.addEventListener("open-details-modal", () => {
        new bootstrap.Modal(
            document.getElementById("bookingDetailsModal")
        ).show();
    });

    document.addEventListener("close-details-modal", () => {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("bookingDetailsModal")
        );
        if (modal) modal.hide();
    });

    document.addEventListener("open-disapprove-modal", () => {
        new bootstrap.Modal(document.getElementById("disapproveModal")).show();
    });

    document.addEventListener("close-disapprove-modal", () => {
        const modal = bootstrap.Modal.getInstance(
            document.getElementById("disapproveModal")
        );
        if (modal) modal.hide();
    });

    // ✅ Approve with confirmation
    document.body.addEventListener("click", function (e) {
        const approveBtn = e.target.closest(".approve-button");
        if (approveBtn && approveBtn.dataset.id) {
            const bookingId = approveBtn.dataset.id;
            const componentId = document
                .querySelector("[wire\\:id]")
                ?.getAttribute("wire:id");

            Swal.fire({
                title: "Confirmation Approval",
                text: "Are you sure you want to approve this request? This action cannot be undone.",
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

    // ✅ Disapprove button logic
    document.body.addEventListener("click", function (e) {
        const disBtn = e.target.closest(".disapprove-button");
        if (disBtn && disBtn.dataset.id) {
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

    // ✅ SweetAlert notifications
    document.addEventListener("swal:success", function (e) {
        const { title, text } = e.detail;
        Swal.fire({
            icon: "success",
            title: title,
            text: text,
            timer: 2500,
            showConfirmButton: false,
        });
    });

    document.addEventListener("swal:warning", function (e) {
        const { title, text } = e.detail;
        Swal.fire({
            icon: "warning",
            title: title,
            text: text,
        });
    });

    document.addEventListener("swal:info", function (e) {
        const { title, text } = e.detail;
        Swal.fire({
            icon: "info",
            title: title,
            text: text,
        });
    });

    // ✅ Disapproval success popup
    document.addEventListener("disapproval-success", (e) => {
        const reason = e.detail.reason || "No reason provided.";
        const message = e.detail.message || "Booking disapproved.";
        Swal.fire({
            icon: "error",
            title: message,
            text: `Reason: ${reason}`,
        });
    });

    // ✅ Reset Livewire state after closing modal
    document.addEventListener("reset-selected-booking", () => {
        setTimeout(() => {
            const componentId = document
                .querySelector("[wire\\:id]")
                ?.getAttribute("wire:id");
            if (componentId) {
                Livewire.find(componentId).call("clearSelectedBooking");
            }
        }, 300);
    });
});
