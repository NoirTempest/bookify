<div id="userDriverTableWrapper" class="card shadow-sm border-0">

    {{-- Table Header --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center mt-3">
        <h5 class="mb-0 fw-bold">Driver</h5>
        <input id="userDriverSearchInput" type="search" class="form-control form-control-sm w-auto"
            placeholder="Search...">
    </div>

    {{-- Table --}}
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Full Name</th>
                    <th>Role / Status</th>
                    <th>Created At</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="userDriverTableBody">
                @forelse ($users ?? $drivers as $index => $item)
                <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ ($users ?? $drivers)->firstItem() + $index }}</td>
                    <td>{{ $item->full_name ?? $item->name ?? 'N/A' }}</td>
                    <td>
                        @if (isset($item->role))
                        {{ $item->role->name ?? 'N/A' }}
                        @elseif (isset($item->is_active))
                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @else
                        N/A
                        @endif
                    </td>
                    <td>{{ $item->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                    <td class="pe-4 text-center">
                        @if (method_exists($this, 'edit') && method_exists($this, 'delete'))
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $item->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        @else
                        <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if (($users ?? $drivers)->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ ($users ?? $drivers)->firstItem() }} to {{ ($users ?? $drivers)->lastItem() }}
                of {{ ($users ?? $drivers)->total() }} results
            </div>
            <div>
                {{ ($users ?? $drivers)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const wrapper = document.getElementById("userDriverTableWrapper");
        const input = wrapper.querySelector("#userDriverSearchInput");
        const tableBody = wrapper.querySelector("#userDriverTableBody");

        const noResultsRow = document.createElement("tr");
        noResultsRow.innerHTML = `<td colspan="5" class="text-center text-muted py-4">No matching records found.</td>`;
        noResultsRow.style.display = "none";
        tableBody.appendChild(noResultsRow);

        input.addEventListener("keyup", function () {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll("tr");
            let matchCount = 0;

            rows.forEach(row => {
                if (row !== noResultsRow) {
                    const text = row.textContent.toLowerCase();
                    const match = text.includes(filter);
                    row.style.display = match ? "" : "none";
                    if (match) matchCount++;
                }
            });

            noResultsRow.style.display = matchCount === 0 ? "" : "none";
        });
    });
</script>
@endpush