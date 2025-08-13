<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // باقات عمرة
        Package::create([
            'name' => 'عمرة اقتصادية - 10 أيام',
            'description' => 'باقة عمرة مميزة بأسعار تنافسية.',
            'type' => 'Umrah',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(40),
            'price_per_person' => 1500.00,
            'agent_price_per_person' => 1350.00,
            'number_of_days' => 10,
            'available_seats' => 50,
            'status' => 'active',
            'includes' => ['تأشيرة', 'تذاكر طيران', 'إقامة 3 نجوم', 'وجبات فطور'],
            'excludes' => ['نقل داخلي', 'زيارات إضافية'],
        ]);

        Package::create([
            'name' => 'عمرة VIP - 7 أيام',
            'description' => 'تجربة عمرة فاخرة مع خدمات مميزة.',
            'type' => 'Umrah',
            'start_date' => now()->addDays(60),
            'end_date' => now()->addDays(67),
            'price_per_person' => 3500.00,
            'agent_price_per_person' => 3150.00,
            'number_of_days' => 7,
            'available_seats' => 20,
            'status' => 'active',
            'includes' => ['تأشيرة', 'تذاكر طيران درجة أولى', 'إقامة 5 نجوم', 'نقل خاص', 'مرشد'],
            'excludes' => [],
        ]);

        // باقة حج (مثال بسيط)
        Package::create([
            'name' => 'حج الميسر - 2025',
            'description' => 'باقة حج شاملة مع كل الخدمات الأساسية.',
            'type' => 'Hajj',
            'start_date' => '2025-06-01', // مثال لتاريخ ثابت (يجب تعديله حسب التقويم الهجري)
            'end_date' => '2025-06-20',
            'price_per_person' => 7000.00,
            'agent_price_per_person' => 6500.00,
            'number_of_days' => 20,
            'available_seats' => 10,
            'status' => 'active',
            'includes' => ['تأشيرة حج', 'تذاكر طيران', 'إقامة في مخيمات مجهزة', 'وجبات'],
            'excludes' => [],
        ]);

        // يمكنك إنشاء المزيد من الباقات باستخدام Factory
        // Package::factory()->count(5)->create();
    }
}
