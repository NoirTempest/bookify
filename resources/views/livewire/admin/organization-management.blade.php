<div> {{-- Root Element --}}

    {{-- Page Heading --}}
    <div class="mb-4">
        <h2 class="h4 font-weight-bold text-dark">Organization Management</h2>
    </div>

    {{-- Branches Table Card --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-body p-3">
            <livewire:admin.branch-management />
        </div>
    </div>

    {{-- Departments Table Card --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-body p-3">
            <livewire:admin.department-management />
        </div>
    </div>

    {{-- Business Units Table Card --}}
    <div class="card shadow-sm border mb-4 bg-white">
        <div class="card-body p-3">
            <livewire:admin.business-unit-management />
        </div>
    </div>

    {{-- Company Codes Table Card --}}
    <div class="card shadow-sm border mb-4 bg-white">

        <div class="card-body p-3">
            <livewire:admin.company-code-management />
        </div>
    </div>

</div>