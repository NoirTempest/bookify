<div class="p-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0 text-dark">Admin Staff Management</h4>
        <button wire:click="$set('showForm', true)" class="btn btn-sm text-white" style="background-color:#172736;">
            <i class="bi bi-plus-circle me-1"></i> Add Admin Staff
        </button>
    </div>

    {{-- Form --}}
    @if ($showForm)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-secondary">{{ $editMode ? 'Edit Staff' : 'Register New Staff' }}</h5>

            <form wire:submit.prevent="{{ $editMode ? 'update' : 'save' }}">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label">First Name</label>
                        <input wire:model="first_name" type="text" class="form-control" id="first_name"
                            placeholder="e.g. Juan">
                        @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input wire:model="last_name" type="text" class="form-control" id="last_name"
                            placeholder="e.g. Dela Cruz">
                        @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input wire:model="email" type="email" class="form-control" id="email"
                            placeholder="e.g. juan@email.com">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="mobile_number" class="form-label">Mobile Number</label>
                        <input wire:model="mobile_number" type="text" class="form-control" id="mobile_number"
                            placeholder="e.g. 09123456789">
                        @error('mobile_number') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    @if (!$editMode)
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input wire:model="password" type="password" class="form-control" id="password">
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">Branch</label>
                        <select wire:model="branch_id" class="form-select">
                            <option value="">-- Select Branch --</option>
                            @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select wire:model="department_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="business_unit_id" class="form-label">Business Unit</label>
                        <select wire:model="business_unit_id" class="form-select">
                            <option value="">-- Select Business Unit --</option>
                            @foreach ($businessUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('business_unit_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="company_code_id" class="form-label">Company Code</label>
                        <select wire:model="company_code_id" class="form-select">
                            <option value="">-- Select Company Code --</option>
                            @foreach ($companyCodes as $code)
                            <option value="{{ $code->id }}">{{ $code->name }}</option>
                            @endforeach
                        </select>
                        @error('company_code_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success px-4">{{ $editMode ? 'Update' : 'Save' }}</button>
                    <button type="button" wire:click="resetForm" class="btn btn-outline-secondary px-4">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Admin Staff Table --}}
    @include('livewire.admin.partials.admin-staff-table', ['users' => $users])

    <style>
        .card-body input.form-control,
        .card-body select.form-select {
            border-radius: 3px;
        }
    </style>
    
</div>
