<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->unique(); // اسم شركة الوكيل يجب أن يكون فريدًا
            $table->string('contact_person'); // اسم مسؤول التواصل في الشركة
            $table->string('email')->unique(); // بريد الوكيل الإلكتروني (فريد)
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->string('license_number')->unique()->nullable(); // رقم الترخيص التجاري (فريد ويمكن أن يكون فارغًا)
            $table->decimal('commission_rate', 5, 2)->default(0.00); // نسبة العمولة الافتراضية (مثال: 5.00 لـ 5%)
            $table->string('status')->default('active'); // active, inactive, pending
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
