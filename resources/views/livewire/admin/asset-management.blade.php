<div>
    {{-- Page Heading --}}
    <div class="mb-4">
        <h2 class="h4 font-weight-bold text-dark">Asset Management</h2>
        {{-- <p class="text-muted">Manage asset types, asset details, and files here.</p> --}}
    </div>

    {{-- Asset Types --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-header bg-white border-bottom fw-semibold">Asset Types</div>
        <div class="card-body p-3">
            <livewire:admin.asset-type-management />
        </div>
    </div>

    {{-- Asset Details --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-header bg-white border-bottom fw-semibold">Asset Details</div>
        <div class="card-body p-3">
            <livewire:admin.asset-detail-management />
        </div>
    </div>

    {{-- Asset Files --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-header bg-white border-bottom fw-semibold">Asset Files</div>
        <div class="card-body p-3">
            <livewire:admin.asset-file-management />
        </div>
    </div>
</div>