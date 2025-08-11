<div id="businessUnitTableWrapper" class="card shadow-sm border mb-4 bg-white">
    <!-- Header -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Business Units</h5>
        <input type="search" class="form-control form-control-sm w-auto" placeholder="Search..."
            id="businessUnitSearchInput">
    </div>

    <!-- Table -->
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Business Unit Name</th>
                    <th>Created At</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="businessUnitTableBody">
                @forelse ($businessUnits as $index => $item)
                <tr class="searchable-row"
                    style="background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $businessUnits->firstItem() + $index }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->created_at?->format('M d, Y h:i A') }}</td>
                    <td class="pe-4 text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $item->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($businessUnits->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $businessUnits->firstItem() }} to {{ $businessUnits->lastItem() }} of {{
                $businessUnits->total() }} results
            </div>
            <div>
                {{ $businessUnits->links('pagination::bootstrap-4', ['paginator' => $businessUnits, 'pageName' =>
                'businessUnitPage']) }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("businessUnitSearchInput");
        const tableBody = document.getElementById("businessUnitTableBody");

        const noResultsRow = document.createElement("tr");
        noResultsRow.innerHTML = `<td colspan="4" class="text-center text-muted py-4">No matching records found.</td>`;
        noResultsRow.style.display = "none";
        tableBody.appendChild(noResultsRow);

        input.addEventListener("keyup", function () {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll(".searchable-row");
            let matchCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const match = text.includes(filter);
                row.style.display = match ? "" : "none";
                if (match) matchCount++;
            });

            noResultsRow.style.display = matchCount === 0 ? "" : "none";
        });
    });
</script>
@endpush