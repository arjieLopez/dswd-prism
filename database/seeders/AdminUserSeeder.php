<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the admin role ID
        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $directorDesignationId = DB::table('designations')->where('name', 'Director')->value('id');
        $regionalDirectorOfficeId = DB::table('offices')->where('name', 'Office of the Regional Director')->value('id');

        // Create default admin user
        DB::table('users')->insert([
            'first_name' => 'System',
            'middle_name' => null,
            'last_name' => 'Administrator',
            'employee_id' => 'ADMIN001',
            'email' => 'ademen111725@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'designation_id' => $directorDesignationId,
            'office_id' => $regionalDirectorOfficeId,
            'role_id' => $adminRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
