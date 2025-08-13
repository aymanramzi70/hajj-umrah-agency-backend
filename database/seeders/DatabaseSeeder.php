<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BranchSeeder::class, // يجب أن يأتي أولاً
            UserSeeder::class,   // يعتمد على Branches
            CustomerSeeder::class, // يعتمد على Branches
            AgentSeeder::class,  // لا يعتمد على جداولنا الأخرى
            PackageSeeder::class, // لا يعتمد على جداولنا الأخرى
            BookingSeeder::class, // يعتمد على Customers, Agents, Packages, Users
            PaymentSeeder::class, // يعتمد على Bookings, Users
        ]);
    }
}
