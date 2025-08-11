<div id="orgTableWrapper_{{ \Str::slug($title) }}" class="card shadow-sm border-0">

    <!-- Table Header -->
    <div class="card-header bg-white d-flex justify-content-between align-items-center mt-3">
        <h5 class="mb-0 fw-bold">{{ $title }}</h5>
        <input type="search" class="form-control form-control-sm w-auto" placeholder="Search..."
            id="searchInput_{{ \Str::slug($title) }}">
    </div>

    <!-- Table Body -->
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">No.</th>
                    <th>{{ $title }} Name</th>
                    <th>Created At</th>
                    <th class="pe-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody_{{ \Str::slug($title) }}">
                @forelse ($items as $index => $item)
                <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $items->firstItem() + $index }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                    <td class="pe-4 text-center">
                        @if (method_exists($this, 'edit') && method_exists($this, 'delete'))
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary" wire:click="edit({{ $item->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger"
                                onclick="confirm('Are you sure?') || event.stopImmediatePropagation()"
                                wire:click="delete({{ $item->id }})">
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
                    <td colspan="4" class="text-center text-muted py-4">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($items->hasPages())
    <div class="card-footer bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} results
            </div>
            <div>
                {{ $items->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const wrapperId = "orgTableWrapper_{{ \Str::slug($title) }}";
        const input = document.querySelector(`#searchInput_{{ \Str::slug($title) }}`);
        const tableBody = document.querySelector(`#tableBody_{{ \Str::slug($title) }}`);

        const noResultsRow = document.createElement("tr");
        noResultsRow.innerHTML = `<td colspan="4" class="text-center text-muted py-4">No matching records found.</td>`;
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