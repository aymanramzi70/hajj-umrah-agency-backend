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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method'); // cash, bank_transfer, card, etc.
            $table->string('transaction_id')->nullable();
            $table->unsignedBigInteger('received_by_user_id'); // الموظف الذي استلم الدفعة
            $table->text('notes')->nullable();
            $table->timestamps();

            // تعريف المفاتيح الخارجية
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade'); // إذا حذف الحجز، تحذف المدفوعات المرتبطة
            $table->foreign('received_by_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
