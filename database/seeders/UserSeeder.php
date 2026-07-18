<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء مستخدم Admin ثابت للتجربة والاختبار
        $adminRole = Role::where('role_name', 'Admin')->first();
        if ($adminRole) {
            User::create([
                'full_name' => 'Saif Admin',
                'email' => 'admin@task.com',
                'phone_number' => '123456789',
                'password' => bcrypt('password'), // الباسورد موحد للتجربة
                'status' => 'active',
                'role_id' => $adminRole->id,
            ]);
        }

        // 2. إنشاء 10 مستخدمين عشوائيين وتوزيعهم على الأدوار المتبقية
        User::factory()->count(10)->create([
            'role_id' => fn () => Role::where('role_name', '!=', 'Admin')->inRandomOrder()->first()->id
        ]);
    }
}