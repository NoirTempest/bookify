<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\{Role, BusinessUnit, CompanyCode, Branch, Department, User};

class UserAndReferenceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $roles = ['Admin', 'Manager', 'User', 'Driver'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Reference Data
        $businessUnits = ['Information Technology', 'Human Resources', 'Finance'];
        foreach ($businessUnits as $unit) {
            BusinessUnit::firstOrCreate(['name' => $unit]);
        }

        $companyCodes = ['GMALL-HQ', 'GMALL-NORTH', 'GMALL-SOUTH'];
        foreach ($companyCodes as $code) {
            CompanyCode::firstOrCreate(['name' => $code]);
        }

        $branches = ['Head Office', 'Manila Branch', 'Cebu Branch'];
        foreach ($branches as $branch) {
            Branch::firstOrCreate(['name' => $branch]);
        }

        $departments = ['IT Development', 'HR Management', 'Finance', 'Operations'];
        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept]);
        }

        // 3. Get IDs
        $businessUnitId = BusinessUnit::first()->id;
        $companyCodeId = CompanyCode::first()->id;
        $branchId = Branch::first()->id;
        $departmentId = Department::first()->id;

        $adminRoleId = Role::where('name', 'Admin')->first()->id;
        $managerRoleId = Role::where('name', 'Manager')->first()->id;
        $userRoleId = Role::where('name', 'User')->first()->id;
        $driverRoleId = Role::where('name', 'Driver')->first()->id;

        // 4. Create Users
        $users = [
            ['John', 'Admin', 'admin@example.com', '+1234567890', $adminRoleId],
            ['Jane', 'Manager', 'manager@example.com', '+1234567891', $managerRoleId],
            ['Bob', 'Requester', 'user@example.com', '+1234567892', $userRoleId],
            ['Mike', 'Driver', 'driver@example.com', '+1234567893', $driverRoleId],
        ];

        foreach ($users as [$firstName, $lastName, $email, $mobile, $roleId]) {
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Hash::make('password123'),
                    'mobile_number' => $mobile,
                    'business_unit_id' => $businessUnitId,
                    'company_code_id' => $companyCodeId,
                    'branch_id' => $branchId,
                    'department_id' => $departmentId,
                    'role_id' => $roleId,
                    'is_active' => true,
                ]
            );

            $this->command->info("✅ User created: {$user->email} ({$user->role->name})");
        }

        $this->command->info("\n🎉 All users created successfully!");
        $this->command->warn("📧 Login credentials (password: password123):");
        $this->command->line("🔴 Admin: admin@example.com");
        $this->command->line("🟡 Manager: manager@example.com");
        $this->command->line("🟢 User: user@example.com");
        $this->command->line("🟢 Driver: driver@example.com");
    }
}
