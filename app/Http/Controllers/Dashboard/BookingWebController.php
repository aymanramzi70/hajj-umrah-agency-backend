<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Customer;
use App\Models\Agent; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
class BookingWebController extends Controller
{
    /**
     * Display a listing of the bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['package', 'customer', 'agent', 'bookedByUser']);
        $user = Auth::user();
        if ($user->role === 'reservation_agent' || $user->role === 'agent') {
            $query->where('booked_by_user_id', $user->id);
        } elseif ($user->role === 'branch_manager' && $user->branch_id) {
            $query->whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            });
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', '%' . $search . '%')
                    ->orWhereHas('customer', function ($subQuery) use ($search) {
                        $subQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('agent', function ($subQuery) use ($search) {
                        $subQuery->where('company_name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('package', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }
        if ($request->filled('booking_status')) {
            $query->where('booking_status', $request->input('booking_status'));
        }
        
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->input('package_id'));
        }
        $bookings = $query->latest()->paginate(10);
        $packages = Package::all(); 
        return view('bookings.index', compact('bookings', 'packages', 'user')); 
    }

    public function create()
    {
        $user = Auth::user();
        $packages = Package::where('status', 'active')->get();
        $customers = collect();
        if ($user->role === 'admin' || $user->role === 'branch_manager' || $user->role === 'accountant') {
            $customers = Customer::all();
        } elseif ($user->role === 'reservation_agent' || $user->role === 'agent') {
            $customers = Customer::where('added_by_user_id', $user->id)->get();
        }
        $agents = collect(); 
        if ($user->role === 'admin' || $user->role === 'branch_manager' || $user->role === 'accountant' || $user->role === 'reservation_agent') {
            $agents = Agent::all();
        }

        return view('bookings.create', compact('packages', 'customers', 'agents', 'user'));
    }
    /**
     * Store a newly created booking in storage.
     */

    public function store(Request $request)
    {
        $user = Auth::user();
        $validationRules = [
            'package_id' => ['required', 'exists:packages,id'],
            'number_of_people' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_price'],
            'booking_status' => ['required', 'string', Rule::in(['pending', 'confirmed', 'canceled', 'completed'])],
            'notes' => ['nullable', 'string'],
        ];
        $customerId = null;
        $agentId = null;
        if ($user->role === 'agent') {
            $validationRules['customer_id'] = ['required', 'exists:customers,id'];
            $request->validate($validationRules);
            $customerId = $request->customer_id;
            $agentId = $user->id;
        } else {
            $validationRules['customer_id'] = ['nullable', 'exists:customers,id', 'required_without:agent_id'];
            $validationRules['agent_id'] = ['nullable', 'exists:agents,id', 'required_without:customer_id'];
            $request->validate($validationRules);
            if ($request->filled('customer_id')) {
                $customerId = $request->customer_id;
                $agentId = null;
            } elseif ($request->filled('agent_id')) {
                $customerId = null;
                $agentId = $request->agent_id;
            }
            if ($customerId && $agentId) {
                return redirect()->back()->withErrors(['customer_id' => 'لا يمكن أن يكون الحجز لعميل ووكيل في نفس الوقت.'])->withInput();
            }
        }
        $package = Package::find($request->package_id);
        if (!$package) {
            return redirect()->back()->withErrors(['package_id' => 'الباقة المختارة غير موجودة.'])->withInput();
        }
        if ($package->available_seats < $request->number_of_people) {
            return redirect()->back()->withErrors(['number_of_people' => 'لا توجد مقاعد كافية متاحة في هذه الباقة. المقاعد المتاحة: ' . $package->available_seats])->withInput();
        }
        $paidAmount = $request->input('paid_amount', 0);
        $remainingAmount = $request->total_price - $paidAmount;
        $paymentStatus = ($remainingAmount <= 0) ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');
        Booking::create([
            'booking_code' => 'BK-' . strtoupper(Str::random(8)),
            'package_id' => $request->package_id,
            'customer_id' => $customerId,
            'agent_id' => $agentId,
            'booked_by_user_id' => Auth::id(),
            'number_of_people' => $request->number_of_people,
            'total_price' => $request->total_price,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'booking_status' => $request->booking_status,
            'notes' => $request->notes,
        ]);
        $package->available_seats -= $request->number_of_people;
        if ($package->available_seats <= 0) {
            $package->status = 'full';
        }
        $package->save();
        return redirect()->route('bookings.index')->with('success', 'تم إضافة الحجز بنجاح.');
    }
    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $user = Auth::user();
        if (($user->role === 'reservation_agent' || $user->role === 'agent') && $booking->booked_by_user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا الحجز.');
        } elseif ($user->role === 'branch_manager' && $user->branch_id) {
            if ($booking->bookedByUser->branch_id !== $user->branch_id) {
                abort(403, 'غير مصرح لك بتعديل حجز لا يتبع لفرعك.');
            }
        }
        $packages = Package::where('status', 'active')->get();
        $customers = collect();
        $agents = collect();
        if ($user->role === 'admin' || $user->role === 'branch_manager' || $user->role === 'accountant') {
            $customers = Customer::all();
            $agents = Agent::all();
        } elseif ($user->role === 'reservation_agent' || $user->role === 'agent') {
            $customers = Customer::where('added_by_user_id', $user->id)->get();
            if ($user->role === 'reservation_agent') {
                $agents = Agent::all();
            }
        }
        $users = User::all(); 
        return view('bookings.edit', compact('booking', 'packages', 'customers', 'agents', 'users', 'user'));
    }
    /**
     * Update the specified booking in storage.
     */

    public function update(Request $request, Booking $booking)
    {
        $user = Auth::user();
        if (($user->role === 'reservation_agent' || $user->role === 'agent') && $booking->booked_by_user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتحديث هذا الحجز.');
        } elseif ($user->role === 'branch_manager' && $user->branch_id) {
            if ($booking->bookedByUser->branch_id !== $user->branch_id) {
                abort(403, 'غير مصرح لك بتحديث حجز لا يتبع لفرعك.');
            }
        }
        $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'customer_id' => ['nullable', 'exists:customers,id', 'required_without:agent_id'],
            'agent_id' => ['nullable', 'exists:agents,id', 'required_without:customer_id'],
            'number_of_people' => ['required', 'integer', 'min:1'],
            'total_price' => ['required', 'numeric', 'min:0'],
            'paid_amount' => ['nullable', 'numeric', 'min:0', 'lte:total_price'],
            'booking_status' => ['required', 'string', Rule::in(['pending', 'confirmed', 'canceled', 'completed'])],
            'notes' => ['nullable', 'string'],
        ]);
        $customerId = null;
        $agentId = null;
        if ($user->role === 'agent') {
            $customerId = $request->customer_id;
            $agentId = $user->id;
        } else {
            if ($request->filled('customer_id')) {
                $customerId = $request->customer_id;
                $agentId = null;
            } elseif ($request->filled('agent_id')) {
                $customerId = null;
                $agentId = $request->agent_id;
            }
        }
        if ($customerId && $agentId) {
            return redirect()->back()->withErrors([
                'customer_id' => 'لا يمكن أن يكون الحجز لعميل ووكيل في نفس الوقت.'
            ])->withInput();
        }
        $oldPackage = $booking->package;
        $newPackage = Package::find($request->package_id);
        $oldNumber = $booking->number_of_people;
        $newNumber = $request->number_of_people;
        if ($oldPackage->id === $newPackage->id) {
            $diff = $newNumber - $oldNumber;
            if ($diff > 0) {
                if ($newPackage->available_seats < $diff) {
                    return redirect()->back()->withErrors([
                        'number_of_people' => 'لا توجد مقاعد كافية متاحة. المقاعد المتاحة: ' . $newPackage->available_seats
                    ])->withInput();
                }
                $newPackage->available_seats -= $diff;
            } elseif ($diff < 0) {
                $newPackage->available_seats += abs($diff);
            }
            $newPackage->status = $newPackage->available_seats <= 0 ? 'full' : 'active';
            $newPackage->save();
        } else {
            $oldPackage->available_seats += $oldNumber;
            if ($oldPackage->status === 'full' && $oldPackage->available_seats > 0) {
                $oldPackage->status = 'active';
            }
            $oldPackage->save();
            if ($newPackage->available_seats < $newNumber) {
                $oldPackage->available_seats -= $oldNumber;
                if ($oldPackage->available_seats <= 0) {
                    $oldPackage->status = 'full';
                }
                $oldPackage->save();
                return redirect()->back()->withErrors([
                    'number_of_people' => 'لا توجد مقاعد كافية في الباقة الجديدة. المقاعد المتاحة: ' . $newPackage->available_seats
                ])->withInput();
            }
            $newPackage->available_seats -= $newNumber;
            $newPackage->status = $newPackage->available_seats <= 0 ? 'full' : 'active';
            $newPackage->save();
        }
        $paidAmount = $request->input('paid_amount', $booking->paid_amount);
        $remainingAmount = $request->total_price - $paidAmount;
        $paymentStatus = ($remainingAmount <= 0) ? 'paid' : ($paidAmount > 0 ? 'partial' : 'pending');
        $booking->update([
            'package_id' => $request->package_id,
            'customer_id' => $customerId,
            'agent_id' => $agentId,
            'number_of_people' => $newNumber,
            'total_price' => $request->total_price,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $paymentStatus,
            'booking_status' => $request->booking_status,
            'notes' => $request->notes,
        ]);
        return redirect()->route('bookings.index')->with('success', 'تم تحديث الحجز بنجاح.');
    }
    /**
     * Remove the specified booking from storage.
     */

    public function destroy(Booking $booking)
    {
        $package = $booking->package; 
        if ($package) {
            $package->available_seats += $booking->number_of_people;
            if ($package->status === 'full' && $package->available_seats > 0) {
                $package->status = 'active';
            }
            $package->save();
        }
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'تم حذف الحجز بنجاح.');
    }
}
