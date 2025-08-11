function showInlineVehicleForm() {
    const panel = document.getElementById("inlineVehicleForm");
    if (panel) {
        panel.style.display = "block";
        window.scrollTo({ top: 0, behavior: "smooth" });
        rebindVehicleAutofill();
    }
}

function hideInlineVehicleForm() {
    const panel = document.getElementById("inlineVehicleForm");
    if (panel) {
        panel.style.display = "none";
    }
}

function rebindVehicleAutofill() {
    const select = document.getElementById("vehicleSelect");
    if (!select) return;

    select.onchange = function () {
        const id = this.value;
        const vehicle = window.vehicles.find((v) => v.id == id);

        document.getElementById("brand").value = vehicle?.brand || "";
        document.getElementById("model").value = vehicle?.model || "";
        document.getElementById("seats").value = vehicle?.number_of_seats || "";
        document.getElementById("plate").value = vehicle?.plate_number || "";
        document.getElementById("color").value = vehicle?.color || "";
    };

    if (select.value) {
        select.dispatchEvent(new Event("change"));
    }
}

document.addEventListener("livewire:load", function () {
    Livewire.hook("message.processed", () => {
        rebindVehicleAutofill();
    });
});

window.addEventListener("close-allocate-modal", () => {
    hideInlineVehicleForm();
});

window.addEventListener("allocation-status", function (event) {
    document.activeElement.blur();

    const status = event.detail.status;
    const alerts = {
        success: {
            icon: "success",
            title: "Vehicle Allocated",
            text: "The vehicle was successfully allocated.",
        },
        updated: {
            icon: "info",
            title: "Allocation Updated",
            text: "The vehicle assignment was updated successfully.",
        },
        already: {
            icon: "info",
            title: "Already Assigned",
            text: "This booking already has this vehicle and driver.",
        },
        "invalid-odometer": {
            icon: "warning",
            title: "Invalid Odometer",
            text: "Odometer end must be greater than start.",
        },
        error: {
            icon: "error",
            title: "Allocation Failed",
            text: "An error occurred during allocation. Please try again.",
        },
    };

    const alertData = alerts[status];
    if (alertData) {
        Swal.fire({
            ...alertData,
            confirmButtonColor: "#172736",
            timer: 2500,
            showConfirmButton: false,
        });
    }

    if (["success", "already", "updated"].includes(status)) {
        hideInlineVehicleForm();
    }
});
