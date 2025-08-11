<div class="p-3">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0 text-dark">Approver Management</h4>
        <button wire:click="$set('showForm', true)" class="btn btn-sm text-white" style="background-color:#172736;">
            <i class="bi bi-plus-circle me-1"></i> Add Approver
        </button>
    </div>

    {{-- Form --}}
    @if ($showForm)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-secondary">{{ $editMode ? 'Edit Approver' : 'Register New Approver' }}</h5>

            <form wire:submit.prevent="{{ $editMode ? 'update' : 'save' }}">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input wire:model="first_name" type="text" class="form-control" placeholder="e.g. Ana">
                        @error('first_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input wire:model="last_name" type="text" class="form-control" placeholder="e.g. Santos">
                        @error('last_name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input wire:model="email" type="email" class="form-control" placeholder="e.g. ana@email.com">
                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mobile Number</label>
                        <input wire:model="mobile_number" type="text" class="form-control"
                            placeholder="e.g. 09123456789">
                        @error('mobile_number') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    @if (!$editMode)
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input wire:model="password" type="password" class="form-control">
                        @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="col-md-6">
                        <label class="form-label">Branch</label>
                        <select wire:model="branch_id" class="form-select">
                            <option value="">-- Select Branch --</option>
                            @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('branch_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Department</label>
                        <select wire:model="department_id" class="form-select">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Business Unit</label>
                        <select wire:model="business_unit_id" class="form-select">
                            <option value="">-- Select Business Unit --</option>
                            @foreach($businessUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('business_unit_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Company Code</label>
                        <select wire:model="company_code_id" class="form-select">
                            <option value="">-- Select Company Code --</option>
                            @foreach($companyCodes as $code)
                            <option value="{{ $code->id }}">{{ $code->name }}</option>
                            @endforeach
                        </select>
                        @error('company_code_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success px-4">{{ $editMode ? 'Update' : 'Save' }}</button>
                    <button type="button" wire:click="resetForm" class="btn btn-outline-secondary px-4">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Approver Table --}}
    @include('livewire.admin.partials.approver-table', ['approvers' => $approvers])

    <style>
        .card-body input.form-control,
        .card-body select.form-select {
            border-radius: 3px;
        }
    </style>
</div>