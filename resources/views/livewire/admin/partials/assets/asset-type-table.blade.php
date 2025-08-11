<div id="assetTypeTableWrapper" class="card shadow-sm border mb-4 bg-white">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">Asset Types</h5>
        <input type="search" class="form-control form-control-sm w-auto" placeholder="Search..."
            id="assetTypeSearchInput">
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Asset Type</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="assetTypeTableBody">
                @forelse ($assetTypes as $index => $type)
                <tr class="searchable-row"
                    style="background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $assetTypes->firstItem() + $index }}</td>
                    <td>{{ $type->name }}</td>
                    <td class="pe-4 text-center">
                        <div class="btn-group">
                            <button wire:click="edit({{ $type->id }})" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button wire:click="delete({{ $type->id }})" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">No asset types found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($assetTypes->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $assetTypes->firstItem() }} to {{ $assetTypes->lastItem() }} of {{ $assetTypes->total() }}
                results
            </div>
            <div>
                {{ $assetTypes->links('pagination::bootstrap-4', ['paginator' => $assetTypes, 'pageName' =>
                'assetTypePage']) }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const input = document.getElementById("assetTypeSearchInput");
        const tableBody = document.getElementById("assetTypeTableBody");
        const rows = tableBody.querySelectorAll(".searchable-row");

        const noResultsRow = document.createElement("tr");
        noResultsRow.innerHTML = `<td colspan="3" class="text-center text-muted py-4">No matching records found.</td>`;
        noResultsRow.style.display = "none";
        tableBody.appendChild(noResultsRow);

        input.addEventListener("keyup", function () {
            const filter = this.value.toLowerCase();
            let matchCount = 0;

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const show = text.includes(filter);
                row.style.display = show ? "" : "none";
                if (show) matchCount++;
            });

            noResultsRow.style.display = matchCount === 0 ? "" : "none";
        });
    });
</script>
@endpush