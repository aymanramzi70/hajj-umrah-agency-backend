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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // كود حجز فريد
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('customer_id')->nullable(); // يمكن أن يكون فارغًا إذا كان الحجز عن طريق وكيل
            $table->unsignedBigInteger('agent_id')->nullable(); // يمكن أن يكون فارغًا إذا كان الحجز مباشرًا
            $table->unsignedBigInteger('booked_by_user_id'); // الموظف الذي قام بالحجز
            $table->integer('number_of_people');
            $table->decimal('total_price', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('remaining_amount', 10, 2);
            $table->string('payment_status')->default('pending'); // pending, partial, paid, refunded
            $table->string('booking_status')->default('pending'); // pending, confirmed, canceled, completed
            $table->text('notes')->nullable();
            $table->timestamps();

            // تعريف المفاتيح الخارجية
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade'); // إذا حذفت الباقة، تحذف الحجوزات المرتبطة
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
            $table->foreign('booked_by_user_id')->references('id')->on('users')->onDelete('cascade'); // إذا حذف المستخدم، تحذف الحجوزات التي قام بها
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
