<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\User; 
use App\Models\Branch; 
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; 
class UserWebController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('branch')->paginate(10); 
        return view('users.index', compact('users'));
    }
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $branches = Branch::all(); 
        return view('users.create', compact('branches'));
    }
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'branch_manager', 'reservation_agent', 'accountant', 'agent', 'customer_app_user'])],
            'branch_id' => 'nullable|exists:branches,id',
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
        ]);
        return redirect()->route('users.index')->with('success', 'تم إضافة المستخدم بنجاح.');
    }
    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $branches = Branch::all();
        return view('users.edit', compact('user', 'branches'));
    }
    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['admin', 'branch_manager', 'reservation_agent', 'accountant', 'agent', 'customer_app_user'])],
            'branch_id' => 'nullable|exists:branches,id',
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ]);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'branch_id' => $request->branch_id,
            'status' => $request->status,
        ]);
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        return redirect()->route('users.index')->with('success', 'تم تحديث المستخدم بنجاح.');
    }
    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
