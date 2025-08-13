<?php






















































































































































































































































































































































namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerWebController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query()->with('sourceBranch', 'addedBy'); 

        $user = Auth::user();

        
        if ($user->role === 'reservation_agent' || $user->role === 'agent') {
            
            $query->where('added_by_user_id', $user->id);
        }
        
        
        
        
        

        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%')
                  ->orWhere('national_id', 'like', '%' . $search . '%')
                  ->orWhere('passport_number', 'like', '%' . $search . '%');
            });
        }

        
        if ($request->filled('gender') && in_array($request->gender, ['male', 'female'])) {
            $query->where('gender', $request->gender);
        }

        
        if ($request->filled('visa_status')) {
            $query->where('visa_status', $request->input('visa_status'));
        }

        $customers = $query->paginate(10);

        $branches = Branch::all();

        return view('customers.index', compact('customers', 'branches'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('customers.create', compact('branches'));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone_number' => 'required|string|max:20|unique:customers,phone_number',
            'passport_number' => 'nullable|string|max:255|unique:customers,passport_number', 
            'date_of_birth' => 'nullable|date', 
            'gender' => ['nullable', Rule::in(['male', 'female'])], 
            'address' => 'nullable|string|max:500', 
            'nationality' => 'nullable|string|max:255', 
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'visa_status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])], 
            'notes' => 'nullable|string',
        ]);

        try {
            
            $validatedData['added_by_user_id'] = $user->id;

            Customer::create($validatedData);

            return redirect()->route('customers.index')->with('success', 'تم إضافة العميل بنجاح.');
        } catch (\Exception $e) {
            
            Log::error('Customer creation failed by user ' . $user->id . ': ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception_trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withInput()->with('error', 'فشل إضافة العميل: ' . $e->getMessage());
        }
    }
    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $user = Auth::user();

        
        if (($user->role === 'reservation_agent' || $user->role === 'agent') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا العميل.');
        }
        $branches = Branch::all();
        return view('customers.edit', compact('customer', 'branches'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();

        
        if (($user->role === 'reservation_agent' || $user->role === 'agent') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'غير مصرح لك بتحديث هذا العميل.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'passport_number' => ['required', 'string', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'date_of_birth' => 'required|date',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'address' => 'nullable|string|max:255',
            'nationality' => 'required|string|max:255',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'visa_status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'notes' => 'nullable|string',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'تم تحديث العميل بنجاح.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        $user = Auth::user();

        
        if (($user->role === 'reservation_agent' || $user->role === 'agent') && $customer->added_by_user_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذا العميل.');
        }

        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'تم حذف العميل بنجاح.');
    }
}
