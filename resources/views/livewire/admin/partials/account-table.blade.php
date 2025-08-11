<div> {{-- ✅ SINGLE ROOT WRAPPER --}}

    {{-- Card Wrapper --}}
    <div class="card shadow-sm border-0">
        {{-- Table Header --}}
        <!-- Table Header -->
        <div class="card-header bg-white d-flex justify-content-between align-items-center mt-3">
            <h5 class="mb-0 fw-bold">User Management</h5>
            <input id="userSearchInput" type="search" class="form-control form-control-sm w-auto"
                placeholder="Search users...">
        </div>

        {{-- Table --}}
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle"
                style="border-collapse: separate; border-spacing: 0 0.6rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    @forelse ($users as $index => $user)
                    <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                        <td class="ps-4">{{ $users->firstItem() + $index }}</td>
                        <td>{{ $user->full_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->name ?? 'N/A' }}</td>
                        <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($users->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                <div>
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
    @push('scripts')
    <script>
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
    
                rows.forEach(row => {
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
    </script>
    @endpush
</div>