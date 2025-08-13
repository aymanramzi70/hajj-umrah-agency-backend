<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with(['booking', 'receivedByUser'])->get();
        return response()->json([
            'message' => 'تم استرجاع المدفوعات بنجاح.',
            'payments' => $payments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'received_by_user_id' => $request->user()->id,
            'notes' => $request->notes,
        ]);


        $booking = $payment->booking;
        $booking->paid_amount += $request->amount;
        $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
        $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'pending');
        $booking->save();


        return response()->json([
            'message' => 'تم إضافة الدفعة بنجاح.',
            'payment' => $payment->load(['booking', 'receivedByUser']),
            'updated_booking_status' => $booking->payment_status
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل الدفعة بنجاح.',
            'payment' => $payment->load(['booking', 'receivedByUser'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['required', 'string', 'max:255'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);


        $oldAmount = $payment->amount;
        $payment->update([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,

            'notes' => $request->notes,
        ]);


        $booking = $payment->booking;
        $booking->paid_amount = $booking->paid_amount - $oldAmount + $payment->amount;
        $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
        $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'pending');
        $booking->save();

        return response()->json([
            'message' => 'تم تحديث الدفعة بنجاح.',
            'payment' => $payment->load(['booking', 'receivedByUser']),
            'updated_booking_status' => $booking->payment_status
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {

        $booking = $payment->booking;
        $booking->paid_amount -= $payment->amount;
        $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
        $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'pending');
        $booking->save();

        $payment->delete();

        return response()->json([
            'message' => 'تم حذف الدفعة بنجاح.',
            'updated_booking_status' => $booking->payment_status
        ], 204);
    }
}
