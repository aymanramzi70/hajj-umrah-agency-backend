<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking; 
use App\Models\Payment; 
use Stripe\Stripe; 
use Stripe\PaymentIntent; 
use Exception; 

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        
        Stripe::setApiKey(config('stripe.sk'));
    }

    /**
     * Create a PaymentIntent for a given booking.
     * إنشاء نية دفع لحجز معين.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.50', 
        ]);

        $booking = Booking::find($request->booking_id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود.'], 404);
        }

        
        if ($request->amount > $booking->remaining_amount) {
            return response()->json(['message' => 'المبلغ المدفوع يتجاوز المبلغ المتبقي على الحجز.'], 422);
        }

        try {
            
            
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($request->amount * 100), 
                'currency' => 'usd', 
                'description' => 'Payment for booking ' . $booking->booking_code,
                'metadata' => ['booking_id' => $booking->id, 'user_id' => $request->user()->id],
            ]);

            return response()->json([
                'message' => 'تم إنشاء نية الدفع بنجاح.',
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
            ]);
        } catch (Exception $e) {
            \Log::error("Stripe Payment Intent creation failed: " . $e->getMessage(), [
                'booking_id' => $request->booking_id,
                'user_id' => $request->user()->id,
            ]);
            return response()->json(['message' => 'فشل إنشاء نية الدفع: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Confirm a payment and update booking status.
     * تأكيد الدفع وتحديث حالة الحجز.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:0.50',
        ]);

        $booking = Booking::find($request->booking_id);

        if (!$booking) {
            return response()->json(['message' => 'الحجز غير موجود.'], 404);
        }

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            
            if ($paymentIntent->status === 'succeeded') {
                
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $request->amount,
                    'payment_date' => now(),
                    'payment_method' => 'Online Payment (Stripe)',
                    'transaction_id' => $request->payment_intent_id,
                    'received_by_user_id' => $request->user()->id, 
                    'notes' => 'Online payment via Stripe.',
                ]);

                
                $booking->paid_amount += $request->amount;
                $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
                $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : 'partial';
                $booking->save();

                return response()->json([
                    'message' => 'تم تأكيد الدفع بنجاح وتحديث الحجز.',
                    'booking' => $booking
                ]);
            } else {
                return response()->json(['message' => 'لم يتم تأكيد الدفع بواسطة Stripe. الحالة: ' . $paymentIntent->status], 400);
            }
        } catch (Exception $e) {
            \Log::error("Stripe Payment confirmation failed: " . $e->getMessage(), [
                'payment_intent_id' => $request->payment_intent_id,
                'booking_id' => $request->booking_id,
            ]);
            return response()->json(['message' => 'فشل تأكيد الدفع: ' . $e->getMessage()], 500);
        }
    }
}
