<?php

namespace App\Livewire\Pages\Auth;

use App\Models\User;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $mobile_number = '';
    public $business_unit_id = '';
    public $company_code_id = '';
    public $branch_id = '';
    public $department_id = '';
    public $role_id = '';

    public function mount()
    {
        $defaultRole = Role::where('name', 'User')->first();
        if ($defaultRole) {
            $this->role_id = $defaultRole->id;
        }
    }

    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'mobile_number' => ['required', 'string', 'max:15'],
            'business_unit_id' => ['required', 'exists:business_units,id'],
            'company_code_id' => ['required', 'exists:company_codes,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'mobile_number' => $validated['mobile_number'],
            'business_unit_id' => $validated['business_unit_id'],
            'company_code_id' => $validated['company_code_id'],
            'branch_id' => $validated['branch_id'],
            'department_id' => $validated['department_id'],
            'role_id' => $validated['role_id'],
            'is_active' => true,
        ]);

        event(new Registered($user));

        $this->dispatch('registrationSuccess');

        $this->reset([
            'first_name',
            'last_name',
            'email',
            'password',
            'password_confirmation',
            'mobile_number',
            'business_unit_id',
            'company_code_id',
            'branch_id',
            'department_id',
            'role_id'
        ]);
    }

    public function render()
    {
        try {
            $businessUnits = BusinessUnit::all();
            $companyCodes = CompanyCode::all();
            $branches = Branch::all();
            $departments = Department::all();
            $roles = Role::all();

            if ($businessUnits->isEmpty()) {
                BusinessUnit::create(['name' => 'Default Business Unit']);
                $businessUnits = BusinessUnit::all();
            }

            if ($companyCodes->isEmpty()) {
                CompanyCode::create(['name' => 'Default Company']);
                $companyCodes = CompanyCode::all();
            }

            if ($branches->isEmpty()) {
                Branch::create(['name' => 'Main Branch']);
                $branches = Branch::all();
            }

            if ($departments->isEmpty()) {
                Department::create(['name' => 'General']);
                $departments = Department::all();
            }

            if ($roles->isEmpty()) {
                Role::insert([
                    ['name' => 'User'],
                    ['name' => 'Admin'],
                    ['name' => 'Manager'],
                    ['name' => 'Driver'],
                ]);
                $roles = Role::all();
            }

        } catch (\Exception $e) {
            $businessUnits = collect([]);
            $companyCodes = collect([]);
            $branches = collect([]);
            $departments = collect([]);
            $roles = collect([]);
        }

        return view('livewire.pages.auth.register', [
            'businessUnits' => $businessUnits,
            'companyCodes' => $companyCodes,
            'branches' => $branches,
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }
}
