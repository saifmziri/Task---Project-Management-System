<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الأدوار الثابتة للنظام
        Role::create(['role_name' => 'Admin']);
        Role::create(['role_name' => 'Manager']);
        Role::create(['role_name' => 'Employee']);
    }
}