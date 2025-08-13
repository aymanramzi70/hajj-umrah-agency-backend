<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking; 
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth; 

class PaymentWebController extends Controller
{
    /**
     * Display a listing of the payments.
     */

    
    
    

    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    
    
    
    

    
    
    
    

    
    

    
    
    


    
    
    public function index(Request $request)
    {
        $query = Payment::with(['booking.customer', 'booking.agent', 'receivedByUser']);
        $user = Auth::user();

        
        if ($user->role === 'reservation_agent' || $user->role === 'agent') {
            
            
            $query->where('received_by_user_id', $user->id);
        } elseif ($user->role === 'branch_manager' && $user->branch_id) {
            
            $query->whereHas('receivedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }
        

        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', '%' . $search . '%')
                    ->orWhere('payment_method', 'like', '%' . $search . '%')
                    ->orWhereHas('booking', function ($subQuery) use ($search) {
                        $subQuery->where('booking_code', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('booking.customer', function ($subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('booking.agent', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    });
            });
        }

        
        if ($request->filled('booking_id')) {
            $query->where('booking_id', $request->input('booking_id'));
        }

        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $payments = $query->latest()->paginate(10);

        $bookingsForFilter = Booking::all();
        $paymentMethods = Payment::select('payment_method')->distinct()->get()->pluck('payment_method');

        return view('payments.index', compact('payments', 'bookingsForFilter', 'paymentMethods'));
    }


    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        $bookings = Booking::whereIn('payment_status', ['pending', 'partial'])->get(); 
        $users = User::all(); 

        return view('payments.create', compact('bookings', 'users'));
    }

    /**
     * Store a newly created payment in storage.
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

        $booking = Booking::find($request->booking_id);
        if (!$booking) {
            return redirect()->back()->with('error', 'الحجز غير موجود.');
        }

        
        if (($booking->remaining_amount < $request->amount) && $booking->remaining_amount > 0) {
            return redirect()->back()->withErrors(['amount' => 'المبلغ المدفوع يتجاوز المبلغ المتبقي على الحجز (المتبقي: ' . number_format($booking->remaining_amount, 2) . ').'])->withInput();
        }

        $payment = Payment::create([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'received_by_user_id' => Auth::id(), 
            'notes' => $request->notes,
        ]);

        
        $booking->paid_amount += $request->amount;
        $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
        $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'pending');
        $booking->save();

        return redirect()->route('payments.index')->with('success', 'تم إضافة الدفعة بنجاح وتحديث حالة الحجز.');
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment) 
    {
        $bookings = Booking::all(); 
        $users = User::all();

        return view('payments.edit', compact('payment', 'bookings', 'users'));
    }

    /**
     * Update the specified payment in storage.
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
        $oldBooking = $payment->booking; 

        $payment->update([
            'booking_id' => $request->booking_id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
        ]);

        
        if ($oldBooking) {
            $oldBooking->paid_amount -= $oldAmount;
            $oldBooking->remaining_amount = $oldBooking->total_price - $oldBooking->paid_amount;
            $oldBooking->payment_status = ($oldBooking->remaining_amount <= 0) ? 'paid' : ($oldBooking->paid_amount > 0 ? 'partial' : 'pending');
            $oldBooking->save();
        }

        
        $newBooking = Booking::find($request->booking_id);
        if ($newBooking) {
            $newBooking->paid_amount += $payment->amount;
            $newBooking->remaining_amount = $newBooking->total_price - $newBooking->paid_amount;
            $newBooking->payment_status = ($newBooking->remaining_amount <= 0) ? 'paid' : ($newBooking->paid_amount > 0 ? 'partial' : 'pending');
            $newBooking->save();
        }

        return redirect()->route('payments.index')->with('success', 'تم تحديث الدفعة بنجاح وتحديث حالة الحجز.');
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        $booking = $payment->booking; 
        $payment->delete();

        
        if ($booking) {
            $booking->paid_amount -= $payment->amount;
            $booking->remaining_amount = $booking->total_price - $booking->paid_amount;
            $booking->payment_status = ($booking->remaining_amount <= 0) ? 'paid' : ($booking->paid_amount > 0 ? 'partial' : 'pending');
            $booking->save();
        }

        return redirect()->route('payments.index')->with('success', 'تم حذف الدفعة بنجاح وتحديث حالة الحجز.');
    }
}
