<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\User;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bookings = Booking::all();
        $userIds = User::pluck('id')->toArray();

        foreach ($bookings as $booking) {
            // لكل حجز، ننشئ دفعة واحدة على الأقل إذا كان هناك مبلغ مدفوع
            if ($booking->paid_amount > 0) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->paid_amount,
                    'payment_date' => fake()->dateTimeBetween($booking->created_at, 'now'),
                    'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'card']),
                    'transaction_id' => fake()->uuid(),
                    'received_by_user_id' => fake()->randomElement($userIds),
                    'notes' => fake()->sentence(3),
                ]);

                // إذا كان هناك مبلغ متبقي كبير، يمكن إضافة دفعة ثانية وهمية (اختياري)
                if ($booking->remaining_amount > 0 && fake()->boolean(30)) { // 30% احتمالية لدفعة ثانية
                    Payment::create([
                        'booking_id' => $booking->id,
                        'amount' => fake()->randomFloat(2, 0, $booking->remaining_amount),
                        'payment_date' => fake()->dateTimeBetween($booking->created_at, 'now'),
                        'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'card']),
                        'transaction_id' => fake()->uuid(),
                        'received_by_user_id' => fake()->randomElement($userIds),
                        'notes' => fake()->sentence(3),
                    ]);
                }
            }
        }
    }
}
