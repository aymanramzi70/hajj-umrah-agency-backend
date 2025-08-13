<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch; // تأكد من استيراد Model الفرع

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء فرعين تجريبيين على الأقل
        Branch::create([
            'name' => 'الفرع الرئيسي - صنعاء',
            'address' => 'شارع حدة، صنعاء، اليمن',
            'phone_number' => '+967-1-123456',
            'email' => 'sanaabranch@hajjumrah.com',
            'status' => 'active',
        ]);

        Branch::create([
            'name' => 'فرع تعز',
            'address' => 'شارع جمال، تعز، اليمن',
            'phone_number' => '+967-4-654321',
            'email' => 'taizzbranch@hajjumrah.com',
            'status' => 'active',
        ]);

        // يمكنك إضافة المزيد من الفروع حسب الحاجة
        // Factory for more branches (اختياري، سنتحدث عن Factories لاحقاً)
        // Branch::factory()->count(3)->create();
    }
}
