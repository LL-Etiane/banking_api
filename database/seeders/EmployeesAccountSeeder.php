<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class EmployeesAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'employee@bank.com',
            'phone' => '237677777777',
            'password' => bcrypt('password'),
        ]);
        
        $employee_role = Role::firstOrCreate(['name' => 'employee']);
        $employee->assignRole($employee_role);
    }
}
