<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // استيراد Model المستخدم
use App\Models\Branch; // استيراد Model الفرع للحصول على branch_id
use Illuminate\Support\Facades\Hash; // لاستخدام التشفير لكلمات المرور

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على معرفات الفروع الموجودة
        $branchIds = Branch::pluck('id')->toArray();

        // إنشاء مستخدم "Admin" رئيسي
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@hajjumrah.com',
            'phone_number' => '+967770000000',
            'password' => Hash::make('password'), // كلمة المرور الافتراضية
            'branch_id' => $branchIds[array_rand($branchIds)], // اختيار فرع عشوائي
            'role' => 'admin',
        ]);

        // إنشاء مستخدم "Branch Manager"
        User::create([
            'name' => 'Branch Manager',
            'email' => 'manager@hajjumrah.com',
            'phone_number' => '+967771111111',
            'password' => Hash::make('password'),
            'branch_id' => $branchIds[array_rand($branchIds)],
            'role' => 'branch_manager',
        ]);

        // إنشاء مستخدم "Reservation Agent"
        User::create([
            'name' => 'Reservation Agent',
            'email' => 'agent@hajjumrah.com',
            'phone_number' => '+967772222222',
            'password' => Hash::make('password'),
            'branch_id' => $branchIds[array_rand($branchIds)],
            'role' => 'reservation_agent',
        ]);

        // يمكنك إنشاء المزيد من المستخدمين باستخدام Factory
        // User::factory()->count(10)->create();
    }
}
