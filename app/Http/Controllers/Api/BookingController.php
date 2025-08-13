<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Package;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $bookings = Booking::with(['package', 'customer', 'agent', 'bookedByUser', 'payments'])->get();
        return response()->json([
            'message' => 'تم استرجاع الحجوزات بنجاح.',
            'bookings' => $bookings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'customer_id' => ['nullable', 'exists:customers,id', 'required_without:agent_id'],
            'agent_id' => ['nullable', 'exists:agents,id', 'required_without:customer_id'],
            'number_of_people' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_price'],
            'payment_status' => ['nullable', 'string', Rule::in(['pending', 'partial', 'paid', 'refunded'])],
            'booking_status' => ['nullable', 'string', Rule::in(['pending', 'confirmed', 'canceled', 'completed'])],
            'notes' => ['nullable', 'string'],
        ]);


        if ($request->filled('customer_id') && $request->filled('agent_id')) {
            return response()->json(['message' => 'لا يمكن أن يكون الحجز لعميل ووكيل في نفس الوقت.'], 422);
        }

        $booking = Booking::create([
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'package_id' => $request->package_id,
            'customer_id' => $request->customer_id,
            'agent_id' => $request->agent_id,
            'booked_by_user_id' => $request->user()->id,
            'number_of_people' => $request->number_of_people,
            'total_price' => $request->total_price,
            'paid_amount' => $request->input('paid_amount', 0),
            'remaining_amount' => $request->total_price - $request->input('paid_amount', 0),
            'payment_status' => $request->input('payment_status', 'pending'),
            'booking_status' => $request->input('booking_status', 'pending'),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'تم إضافة الحجز بنجاح.',
            'booking' => $booking->load(['package', 'customer', 'agent', 'bookedByUser'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل الحجز بنجاح.',
            'booking' => $booking->load(['package', 'customer', 'agent', 'bookedByUser', 'payments'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'customer_id' => ['nullable', 'exists:customers,id', 'required_without:agent_id'],
            'agent_id' => ['nullable', 'exists:agents,id', 'required_without:customer_id'],
            'number_of_people' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_price'],
            'payment_status' => ['nullable', 'string', Rule::in(['pending', 'partial', 'paid', 'refunded'])],
            'booking_status' => ['nullable', 'string', Rule::in(['pending', 'confirmed', 'canceled', 'completed'])],
            'notes' => ['nullable', 'string'],
        ]);

        if ($request->filled('customer_id') && $request->filled('agent_id')) {
            return response()->json(['message' => 'لا يمكن أن يكون الحجز لعميل ووكيل في نفس الوقت.'], 422);
        }

        $booking->update([
            'package_id' => $request->package_id,
            'customer_id' => $request->customer_id,
            'agent_id' => $request->agent_id,
            'number_of_people' => $request->number_of_people,
            'total_price' => $request->total_price,
            'paid_amount' => $request->input('paid_amount', $booking->paid_amount),
            'remaining_amount' => $request->total_price - $request->input('paid_amount', $booking->paid_amount),
            'payment_status' => $request->input('payment_status', $booking->payment_status),
            'booking_status' => $request->input('booking_status', $booking->booking_status),
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'تم تحديث الحجز بنجاح.',
            'booking' => $booking->load(['package', 'customer', 'agent', 'bookedByUser'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json([
            'message' => 'تم حذف الحجز بنجاح.'
        ], 204);
    }



    /**
     * Display a listing of bookings for the authenticated user (customer).
     * عرض قائمة بالحجوزات للمستخدم المصادق عليه (العميل).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myBookings(Request $request)
    {
        $user = $request->user();









        $customer = $user->customer;

        if (!$customer) {
            return response()->json(['message' => 'لم يتم العثور على حساب عميل مرتبط بهذا المستخدم.'], 404);
        }

        $bookings = Booking::with(['package', 'agent', 'bookedByUser'])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'تم استرجاع حجوزات العميل بنجاح.',
            'bookings' => $bookings
        ]);
    }













































    public function storeCustomerBooking(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return response()->json(['message' => 'لم يتم العثور على ملف شخصي للعميل مرتبط بحسابك.'], 403);
        }

        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'number_of_people' => ['required', 'integer', 'min:1'],
        ]);

        $package = Package::find($request->package_id);
        if (!$package) {
            return response()->json(['message' => 'الباقة المختارة غير موجودة.'], 404);
        }


        if ($package->available_seats < $request->number_of_people) {
            return response()->json(['message' => 'لا توجد مقاعد كافية متاحة في هذه الباقة.'], 422);
        }

        $totalPrice = $package->price_per_person * $request->number_of_people;

        $booking = Booking::create([
            'booking_code' => 'APP-BK-' . strtoupper(Str::random(6)),
            'package_id' => $request->package_id,
            'customer_id' => $customer->id,
            'agent_id' => null,
            'booked_by_user_id' => $user->id,
            'number_of_people' => $request->number_of_people,
            'total_price' => $totalPrice,
            'paid_amount' => 0,
            'remaining_amount' => $totalPrice,
            'payment_status' => 'pending',
            'booking_status' => 'pending',
            'notes' => 'حجز تم عبر تطبيق الموبايل.',
        ]);


        $package->available_seats -= $request->number_of_people;

        if ($package->available_seats <= 0) {
            $package->status = 'full';
        }
        $package->save();

        return response()->json([
            'message' => 'تم إنشاء الحجز بنجاح. يرجى إتمام عملية الدفع.',
            'booking' => $booking,
            'remaining_amount' => $booking->remaining_amount
        ], 201);
    }
}
