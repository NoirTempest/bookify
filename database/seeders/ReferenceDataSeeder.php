<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessUnit;
use App\Models\CompanyCode;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Role;

class ReferenceDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Business Units
        $businessUnits = [
            'Information Technology',
            'Human Resources',
            'Finance & Accounting',
            'Operations',
            'Marketing & Sales',
            'Legal & Compliance',
            'Administration',
        ];

        foreach ($businessUnits as $unit) {
            BusinessUnit::firstOrCreate(['name' => $unit]);
        }

        // Create Company Codes
        $companyCodes = [
            'GMALL-HQ',
            'GMALL-NORTH',
            'GMALL-SOUTH',
            'GMALL-CENTRAL',
            'GMALL-EAST',
            'GMALL-WEST',
        ];

        foreach ($companyCodes as $code) {
            CompanyCode::firstOrCreate(['name' => $code]);
        }

        // Create Branches
        $branches = [
            'Head Office',
            'Manila Branch',
            'Quezon City Branch',
            'Makati Branch',
            'Cebu Branch',
            'Davao Branch',
            'Iloilo Branch',
            'Baguio Branch',
        ];

        foreach ($branches as $branch) {
            Branch::firstOrCreate(['name' => $branch]);
        }

        // Create Departments
        $departments = [
            'Executive Management',
            'IT Development',
            'IT Support',
            'System Administration',
            'HR Recruitment',
            'HR Benefits',
            'Accounting',
            'Finance',
            'Treasury',
            'Operations Management',
            'Logistics',
            'Procurement',
            'Marketing',
            'Sales',
            'Customer Service',
            'Legal Affairs',
            'Compliance',
            'General Administration',
            'Facilities Management',
            'Security',
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(['name' => $department]);
        }

        // Create Roles
        $roles = [
            'Admin',
            'Manager', 
            'User',
            'Driver',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $this->command->info('Reference data seeded successfully!');
    }
}