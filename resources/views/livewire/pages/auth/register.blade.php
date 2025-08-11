<div class="d-flex flex-column align-items-center justify-content-center text-center">
    <!-- Logo -->
    <div class="mb-3">
        <img src="{{ asset('images/gmall.png') }}" alt="Logo" style="height: 25px;">
    </div>

    <h3 class="mb-4">Sign Up</h3>

    <form wire:submit.prevent="register" class="w-100" style="max-width: 500px;">

        <!-- First Name -->
        <div class="row">
            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input wire:model="first_name" id="first_name" class="form-control" type="text" required
                    autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input wire:model="last_name" id="last_name" class="form-control" type="text" required
                    autocomplete="family-name" />
                <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input wire:model="email" id="email" class="form-control" type="email" required
                    autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="mobile_number" :value="__('Mobile Number')" />
                <x-text-input wire:model="mobile_number" id="mobile_number" class="form-control" type="tel" required
                    autocomplete="tel" />
                <x-input-error :messages="$errors->get('mobile_number')" class="mt-2" />
            </div>

            <!-- Business Unit -->
            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="business_unit_id" :value="__('Business Unit')" />
                <select wire:model="business_unit_id" id="business_unit_id" class="form-select" required>
                    <option value="">Select Business Unit</option>
                    @foreach ($businessUnits as $businessUnit)
                    <option value="{{ $businessUnit->id }}">{{ $businessUnit->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('business_unit_id')" class="mt-2" />
            </div>

            <!-- Company Code -->
            <div class="col-md-6 mb-3 text-start">
                <x-input-label for="company_code_id" :value="__('Company Code')" />
                <select wire:model="company_code_id" id="company_code_id" class="form-select" required>
                    <option value="">Select Company Code</option>
                    @foreach ($companyCodes as $companyCode)
                    <option value="{{ $companyCode->id }}">{{ $companyCode->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('company_code_id')" class="mt-2" />
            </div>

            <!-- Branch -->
            <div class=" col-md-6 mb-3 text-start">
                <x-input-label for="branch_id" :value="__('Branch')" />
                <select wire:model="branch_id" id="branch_id" class="form-select" required>
                    <option value="">Select Branch</option>
                    @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('branch_id')" class="mt-2" />
            </div>

            <!-- Department -->
            <div class=" col-md-6 mb-3 text-start">
                <x-input-label for="department_id" :value="__('Department')" />
                <select wire:model="department_id" id="department_id" class="form-select" required>
                    <option value="">Select Department</option>
                    @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>

           
        </div>

        <div class=" mb-3 text-start">
            <x-input-label for="role_id" :value="__('Role')" />
            <select wire:model="role_id" id="role_id" class="form-select" required>
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                @if ($role->name === 'User')
                <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endif
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>





        <!-- Role -->
        {{-- <div class="mb-3 text-start">
            <x-input-label for="role_id" :value="__('Role')" />
            <select wire:model="role_id" id="role_id" class="form-select" required>
                <option value="">Select Role</option>
                @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div> --}}



        <!-- Password -->
        <div class="mb-3 text-start">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="form-control" type="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mb-3 text-start">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="form-control"
                type="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <div class="text-center mt-3">
            <x-primary-button class="w-100 text-center d-block mx-auto">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Already Registered -->
    <div class="text-center mt-3">
        <p class="mb-0 small">
            {{ __('Already registered?') }}
            <a href="{{ route('login') }}" class="text-primary fw-semibold text-decoration-none" wire:navigate>
                {{ __('Sign in') }}
            </a>
        </p>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Livewire.on('registrationSuccess', () => {
            console.log('Registration event received'); // Optional debug
            Swal.fire({
                title: 'Registration Successful!',
                text: 'Your account has been created.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        });
</script>
@endpush