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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable(); // يمكن أن يكون فارغًا، أو فريدًا حسب سياستك
            $table->string('phone_number')->unique(); // رقم الهاتف يجب أن يكون فريدًا للعميل
            $table->string('national_id')->unique()->nullable(); // رقم الهوية الوطنية (فريد ويمكن أن يكون فارغًا)
            $table->string('passport_number')->unique()->nullable(); // رقم الجواز (فريد ويمكن أن يكون فارغًا)
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable(); // male, female
            $table->text('address')->nullable();
            $table->unsignedBigInteger('source_branch_id')->nullable(); // الفرع الذي تم تسجيل العميل من خلاله
            $table->timestamps();

            // تعريف المفتاح الخارجي: يربط العميل بالفرع
            $table->foreign('source_branch_id')->references('id')->on('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
