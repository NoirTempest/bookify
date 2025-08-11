document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("userSearchInput");
    const tableBody = document.getElementById("userTableBody");

    // Create a "no results" row element
    const noResultsRow = document.createElement("tr");
    noResultsRow.innerHTML = `<td colspan="5" class="text-center text-muted py-4">No matching users found.</td>`;
    noResultsRow.style.display = "none";
    tableBody.appendChild(noResultsRow);

    input.addEventListener("keyup", function () {
        const filter = this.value.toLowerCase();
        const rows = tableBody.querySelectorAll("tr");
        let matchCount = 0;

        rows.forEach((row) => {
            if (row !== noResultsRow) {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(filter);
                row.style.display = isMatch ? "" : "none";
                if (isMatch) matchCount++;
            }
        });

        noResultsRow.style.display = matchCount === 0 ? "" : "none";
    });
});
