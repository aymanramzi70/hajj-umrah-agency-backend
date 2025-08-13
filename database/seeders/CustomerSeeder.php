<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Branch;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchIds = Branch::pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) { // إنشاء 20 عميلًا تجريبيًا
            Customer::create([
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'email' => fake()->unique()->safeEmail(),
                'phone_number' => fake()->unique()->phoneNumber(),
                'national_id' => fake()->unique()->numerify('###########'), // 11 رقمًا عشوائيًا
                'passport_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{7}'), // مثال: AB1234567
                'date_of_birth' => fake()->date('Y-m-d', '2000-01-01'),
                'gender' => fake()->randomElement(['male', 'female']),
                'address' => fake()->address(),
                'source_branch_id' => $branchIds[array_rand($branchIds)],
            ]);
        }
    }
}
