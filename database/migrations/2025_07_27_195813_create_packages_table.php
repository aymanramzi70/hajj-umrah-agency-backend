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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type'); // Hajj, Umrah, Tour
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('price_per_person', 10, 2); // السعر الأساسي للشخص الواحد (للعميل المباشر)
            $table->decimal('agent_price_per_person', 10, 2)->nullable(); // السعر الخاص بالوكلاء (يمكن أن يكون فارغًا إذا كان يحسب من نسبة العمولة)
            $table->integer('number_of_days');
            $table->integer('available_seats')->default(0); // عدد المقاعد المتاحة
            $table->string('status')->default('active'); // active, full, archived
            $table->json('includes')->nullable(); // الخدمات المضمنة (يمكن تخزينها كـ JSON)
            $table->json('excludes')->nullable(); // الخدمات غير المضمنة (يمكن تخزينها كـ JSON)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
