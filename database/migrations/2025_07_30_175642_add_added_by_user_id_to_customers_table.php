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
        Schema::table('customers', function (Blueprint $table) {
            // إضافة foreign key لربط العميل بالمستخدم الذي أضافه
            $table->foreignId('added_by_user_id')
                ->nullable() // يمكن أن يكون null إذا كان عميلًا قديمًا أو أضيف من Admin
                ->constrained('users')
                ->onDelete('set null') // إذا حُذف المستخدم، يُعيّن هذا الحقل إلى null
                ->after('id'); // بعد عمود الـ ID
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['added_by_user_id']);
            $table->dropColumn('added_by_user_id');
        });
    }
};
