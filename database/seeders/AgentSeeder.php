<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agent;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 5; $i++) { // إنشاء 5 وكلاء تجريبيين
            Agent::create([
                'company_name' => fake()->unique()->company(),
                'contact_person' => fake()->name(),
                'email' => fake()->unique()->companyEmail(),
                'phone_number' => fake()->phoneNumber(),
                'address' => fake()->address(),
                'license_number' => fake()->unique()->bothify('LIC-######'), // مثال: LIC-123456
                'commission_rate' => fake()->randomFloat(2, 5, 20), // نسبة عمولة بين 5% و 20%
                'status' => fake()->randomElement(['active', 'pending']),
            ]);
        }
    }
}
