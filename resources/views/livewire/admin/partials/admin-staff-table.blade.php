<div class="card shadow-sm border-0">
    {{-- Table --}}
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
            <tbody>
                @forelse ($users as $index => $user)
                <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                    <td class="ps-4">{{ $users->firstItem() + $index }}</td>
                    <td>{{ $user->full_name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name ?? 'N/A' }}</td>
                    <td>{{ $user->created_at?->format('M d, Y h:i A') ?? 'N/A' }}</td>
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
                    <td colspan="6" class="text-center text-muted py-4">No Admin Staff users found.</td>
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