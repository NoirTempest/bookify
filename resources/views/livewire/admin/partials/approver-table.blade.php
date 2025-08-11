<div id="approverTableWrapper" class="card shadow-sm border-0">

    {{-- Table Header --}}
    <div class="card-header bg-white d-flex justify-content-between align-items-center mt-3">
        <h5 class="mb-0 fw-bold">Approvers</h5>
        <input id="approverSearchInput" type="search" class="form-control form-control-sm w-auto"
            placeholder="Search approvers...">
    </div>

    {{-- Table Body --}}
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="approverTableBody">
                @forelse ($approvers as $index => $user)
                <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $approvers->firstItem() + $index }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name ?? 'N/A' }}</td>
                    <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                    <td class="pe-4 text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $user->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $user->id }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No approvers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($approvers->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $approvers->firstItem() }} to {{ $approvers->lastItem() }} of {{ $approvers->total() }}
                results
            </div>
            <div>
                {{ $approvers->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const wrapper = document.getElementById("approverTableWrapper");
        const input = wrapper.querySelector("#approverSearchInput");
        const tableBody = wrapper.querySelector("#approverTableBody");

        const noResultsRow = document.createElement("tr");
        noResultsRow.innerHTML = `<td colspan="6" class="text-center text-muted py-4">No matching approvers found.</td>`;
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