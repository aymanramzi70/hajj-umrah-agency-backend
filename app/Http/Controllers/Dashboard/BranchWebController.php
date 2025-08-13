<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class BranchWebController extends Controller
{
    /**
     * Display a listing of the branches.
     */
    public function index(Request $request)
    {
        $query = Branch::query();
        $user = Auth::user();
        if ($user->role === 'branch_manager') {
            $query->where('id', $user->branch_id);
        }
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $branches = $query->latest()->paginate(10); 
        $branchStatuses = ['active', 'inactive'];
        return view('branches.index', compact('branches', 'branchStatuses'));
    }
    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('branches.create');
    }
    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:branches'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ]);
        Branch::create($request->all());
        return redirect()->route('branches.index')->with('success', 'تم إضافة الفرع بنجاح.');
    }
    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch) 
    {
        return view('branches.edit', compact('branch'));
    }
    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('branches')->ignore($branch->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive'])],
        ]);
        $branch->update($request->all());
        return redirect()->route('branches.index')->with('success', 'تم تحديث الفرع بنجاح.');
    }
    /**
     * Remove the specified branch from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'تم حذف الفرع بنجاح.');
    }
}
