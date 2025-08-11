<div class="p-4 bg-white rounded shadow">
    <h2 class="mb-4 h4 fw-bold text-dark">Account Management</h2>

    <!-- Bootstrap Nav Pills -->
    <ul class="nav nav-pills mb-4" id="accountTabs" role="tablist">
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link active" id="account-tab" data-bs-toggle="pill" data-bs-target="#accountTab"
                type="button" role="tab">
                All Accounts
            </button>
        </li>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link" id="adminStaff-tab" data-bs-toggle="pill" data-bs-target="#adminStaffTab"
                type="button" role="tab">
                Admin Staff
            </button>
        </li>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link" id="driver-tab" data-bs-toggle="pill" data-bs-target="#driverTab" type="button"
                role="tab">
                Drivers
            </button>
        </li>
        <li class="nav-item me-2" role="presentation">
            <button class="nav-link" id="approver-tab" data-bs-toggle="pill" data-bs-target="#approverTab" type="button"
                role="tab">
                Approvers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="user-tab" data-bs-toggle="pill" data-bs-target="#userTab" type="button"
                role="tab">
                Users
            </button>
        </li>
    </ul>

    <!-- Bootstrap Tab Content -->
    <div class="tab-content" id="accountTabsContent">
        <div class="tab-pane fade show active" id="accountTab" role="tabpanel" aria-labelledby="account-tab">
            @include('livewire.admin.partials.account-table', ['users' => $allUsers])
        </div>
        <div class="tab-pane fade" id="adminStaffTab" role="tabpanel" aria-labelledby="adminStaff-tab">
            <livewire:admin.admin-staff-management />
        </div>
        <div class="tab-pane fade" id="driverTab" role="tabpanel" aria-labelledby="driver-tab">
            <livewire:admin.driver-management />
        </div>
        <div class="tab-pane fade" id="approverTab" role="tabpanel" aria-labelledby="approver-tab">
            <livewire:admin.approver-management />
        </div>
        <div class="tab-pane fade" id="userTab" role="tabpanel" aria-labelledby="user-tab">
            @include('livewire.admin.partials.user-table', ['users' => $users])
        </div>
    </div>

    <!-- Custom CSS -->
    <style>
        .nav-pills .nav-link {
            color: #172736;
            border: 1px solid #172736;
            transition: background-color 0.3s, color 0.3s;
        }

        .nav-pills .nav-link:hover {
            background-color: #172736;
            color: #fff;
        }

        .nav-pills .nav-link.active {
            background-color: #172736 !important;
            color: #fff !important;
        }
    </style>
</div>