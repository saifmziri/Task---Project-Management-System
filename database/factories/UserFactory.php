<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(), // الحقل المحدث
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(), // تم إضافته
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => $this->faker->randomElement(['active', 'inactive']), // تم إضافته
            'remember_token' => Str::random(10),
            // يسحب دور عشوائي من الجداول الموجودة (Admin, Manager, Employee)
            'role_id' => Role::inRandomOrder()->first()?->id ?? Role::factory(),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}