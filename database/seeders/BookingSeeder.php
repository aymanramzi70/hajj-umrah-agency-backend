<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Agent;
use App\Models\Package;
use App\Models\User;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerIds = Customer::pluck('id')->toArray();
        $agentIds = Agent::pluck('id')->toArray();
        $packageIds = Package::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 30; $i++) { // إنشاء 30 حجزًا تجريبيًا
            $isAgentBooking = fake()->boolean(60); // 60% احتمالية أن يكون الحجز عن طريق وكيل
            $numberOfPeople = fake()->numberBetween(1, 5);
            $selectedPackage = Package::find(fake()->randomElement($packageIds));

            if (!$selectedPackage) {
                continue; // تخطي إذا لم يتم العثور على الباقة (فقط للاحتياط)
            }

            $totalPrice = $isAgentBooking
                ? $selectedPackage->agent_price_per_person * $numberOfPeople
                : $selectedPackage->price_per_person * $numberOfPeople;

            $paidAmount = fake()->randomFloat(2, 0, $totalPrice);
            $remainingAmount = $totalPrice - $paidAmount;

            Booking::create([
                'booking_code' => 'BOOK-' . fake()->unique()->bothify('######'),
                'package_id' => $selectedPackage->id,
                'customer_id' => $isAgentBooking ? null : fake()->randomElement($customerIds),
                'agent_id' => $isAgentBooking ? fake()->randomElement($agentIds) : null,
                'booked_by_user_id' => fake()->randomElement($userIds),
                'number_of_people' => $numberOfPeople,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
                'payment_status' => $remainingAmount == 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending'),
                'booking_status' => fake()->randomElement(['pending', 'confirmed', 'canceled', 'completed']),
                'notes' => fake()->sentence(),
            ]);
        }
    }
}
