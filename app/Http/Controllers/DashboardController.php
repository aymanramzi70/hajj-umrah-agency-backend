<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Agent as ExternalAgent;
use App\Models\Package;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with key statistics based on user role.
     */
    public function index()
    {
        $user = Auth::user();
        $branchesCount = 0;
        $customersCount = 0;
        $agentsCount = 0;
        $packagesCount = 0;
        $bookingsCount = 0;
        $paymentsCount = 0;
        $totalBookingsValue = 0;
        $totalPaidAmount = 0;
        $totalRemainingAmount = 0;
        $bookingsByType = collect();


        if ($user->role === 'admin') {
            $branchesCount = Branch::count();
            $customersCount = Customer::count();
            $agentsCount = ExternalAgent::count();
            $packagesCount = Package::count();
            $bookingsCount = Booking::count();
            $paymentsCount = Payment::count();
            $totalBookingsValue = Booking::sum('total_price');
            $totalPaidAmount = Booking::sum('paid_amount');
            $totalRemainingAmount = Booking::sum('remaining_amount');
            $bookingsByType = Booking::select('package_id', DB::raw('count(*) as total'))
                ->groupBy('package_id')
                ->with('package')
                ->get();
        } elseif ($user->role === 'branch_manager') {

            $branchesCount = $user->branch ? 1 : 0;
            $customersCount = Customer::where('source_branch_id', $user->branch_id)->count();
            $bookingsCount = Booking::whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->count();
            $paymentsCount = Payment::whereHas('receivedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->count();
            $totalBookingsValue = Booking::whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->sum('total_price');
            $totalPaidAmount = Booking::whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->sum('paid_amount');
            $totalRemainingAmount = Booking::whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })->sum('remaining_amount');
            $bookingsByType = Booking::whereHas('bookedByUser', function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
                ->select('package_id', DB::raw('count(*) as total'))
                ->groupBy('package_id')
                ->with('package')
                ->get();
        } elseif ($user->role === 'reservation_agent') {

            $customersCount = Customer::where('added_by_user_id', $user->id)->count();
            $bookingsCount = Booking::where('booked_by_user_id', $user->id)->count();
            $paymentsCount = Payment::where('received_by_user_id', $user->id)->count();
            $totalBookingsValue = Booking::where('booked_by_user_id', $user->id)->sum('total_price');
            $totalPaidAmount = Booking::where('booked_by_user_id', $user->id)->sum('paid_amount');
            $totalRemainingAmount = Booking::where('booked_by_user_id', $user->id)->sum('remaining_amount');
            $bookingsByType = Booking::where('booked_by_user_id', $user->id)
                ->select('package_id', DB::raw('count(*) as total'))
                ->groupBy('package_id')
                ->with('package')
                ->get();
        } elseif ($user->role === 'accountant') {

            $bookingsCount = Booking::count();
            $paymentsCount = Payment::count();
            $totalBookingsValue = Booking::sum('total_price');
            $totalPaidAmount = Booking::sum('paid_amount');
            $totalRemainingAmount = Booking::sum('remaining_amount');
        } elseif ($user->role === 'agent') {

            $customersCount = Customer::where('added_by_user_id', $user->id)->count();
            $bookingsCount = Booking::where('booked_by_user_id', $user->id)->count();
            $totalBookingsValue = Booking::where('booked_by_user_id', $user->id)->sum('total_price');
            $totalPaidAmount = Booking::where('booked_by_user_id', $user->id)->sum('paid_amount');
            $totalRemainingAmount = Booking::where('booked_by_user_id', $user->id)->sum('remaining_amount');
            $bookingsByType = Booking::where('booked_by_user_id', $user->id)
                ->select('package_id', DB::raw('count(*) as total'))
                ->groupBy('package_id')
                ->with('package')
                ->get();
        }

        return view('dashboard', compact(
            'branchesCount',
            'customersCount',
            'agentsCount',
            'packagesCount',
            'bookingsCount',
            'paymentsCount',
            'totalBookingsValue',
            'totalPaidAmount',
            'totalRemainingAmount',
            'bookingsByType',
            'user'
        ));
    }
}
