{{-- SEARCH + VEHICLES TABLE --}}
<div class="card shadow-sm border bg-white mb-4" id="vehiclesTableWrapper">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <h5 class="mb-0 fw-semibold">Vehicles</h5>
        <input type="search" class="form-control form-control-sm w-auto" placeholder="Search..."
            id="vehicleSearchInput">
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Asset Name</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Color</th>
                    <th>Plate Number</th>
                    <th>Seats</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="vehicleTableBody">
                @forelse($assetDetails->where('asset_type_id', 2) as $index => $asset)
                <tr class="searchable-row"
                    style="background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $assetDetails->firstItem() + $index }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->brand }}</td>
                    <td>{{ $asset->model }}</td>
                    <td>{{ $asset->color }}</td>
                    <td>{{ $asset->plate_number }}</td>
                    <td>{{ $asset->number_of_seats }}</td>
                    <td class="pe-4 text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $asset->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $asset->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No vehicle assets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($assetDetails->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $assetDetails->firstItem() }} to {{ $assetDetails->lastItem() }} of {{ $assetDetails->total()
                }} results
            </div>
            <div>
                {{ $assetDetails->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

{{-- SEARCH + CONFERENCE ROOMS TABLE --}}
<div class="card shadow-sm border bg-white mb-4" id="roomsTableWrapper">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <h5 class="mb-0 fw-semibold">Conference Rooms</h5>
        <input type="search" class="form-control form-control-sm w-auto" placeholder="Search..." id="roomSearchInput">
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Asset Name</th>
                    <th>Location</th>
                    <th>Seats</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="roomTableBody">
                @forelse($assetDetails->where('asset_type_id', 1) as $index => $asset)
                <tr class="searchable-row"
                    style="background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $assetDetails->firstItem() + $index }}</td>
                    <td>{{ $asset->asset_name }}</td>
                    <td>{{ $asset->location }}</td>
                    <td>{{ $asset->number_of_seats }}</td>
                    <td class="pe-4 text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $asset->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $asset->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No conference room assets found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- SEARCH SCRIPT --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchConfigs = [
            { inputId: "vehicleSearchInput", tableBodyId: "vehicleTableBody", colCount: 8 },
            { inputId: "roomSearchInput", tableBodyId: "roomTableBody", colCount: 5 }
        ];

        searchConfigs.forEach(({ inputId, tableBodyId, colCount }) => {
            const input = document.getElementById(inputId);
            const tableBody = document.getElementById(tableBodyId);

            const noResultsRow = document.createElement("tr");
            noResultsRow.innerHTML = `<td colspan="${colCount}" class="text-center text-muted py-4">No matching records found.</td>`;
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
    });
</script>
@endpush