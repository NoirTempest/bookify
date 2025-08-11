{{-- Uploaded Files Table --}}
<div class="card-body p-0">
    @if ($assets->count())
    <table class="table table-hover mb-0 align-middle" style="border-collapse: separate; border-spacing: 0 0.6rem;">
        <thead class="table-light">
            <tr>
                <th class="ps-4">No.</th>
                <th>Asset Name</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = ($assets->currentPage() - 1) * $assets->perPage() + 1; @endphp
            @foreach ($assets as $asset)
            @forelse ($asset->files as $file)
            <tr style="background: #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08); border-radius: 0.375rem;">
                <td class="ps-4">{{ $counter++ }}</td>
                <td>{{ $asset->asset_name }}</td>
                <td>
                    @if ($file->file_attachments && file_exists(public_path('storage/' . $file->file_attachments)))
                    <img src="{{ asset('storage/' . $file->file_attachments) }}" alt="Asset Image" class="img-thumbnail"
                        style="max-height: 100px;">
                    @else
                    <span class="text-muted">No image</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        @if ($file->file_attachments)
                        <a href="{{ asset('storage/' . $file->file_attachments) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        @endif

                        <button wire:click="deleteFile({{ $file->id }})" class="btn btn-sm btn-outline-danger"
                            title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr style="background: #fff;">
                <td class="ps-4">{{ $counter++ }}</td>
                <td>{{ $asset->asset_name }}</td>
                <td class="text-muted fst-italic">No image uploaded</td>
                <td>-</td>
            </tr>
            @endforelse
            @endforeach
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="p-3">
        {{ $assets->links() }}
    </div>
    @else
    <div class="p-3 text-center text-muted">No uploaded files found.</div>
    @endif
</div>