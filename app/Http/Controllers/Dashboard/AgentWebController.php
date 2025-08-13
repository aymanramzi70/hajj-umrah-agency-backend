<?php
namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
class AgentWebController extends Controller
{
    /**
     * Display a listing of the agents.
     */
    public function index(Request $request)
    {
        $query = Agent::query();
        $user = Auth::user();
        if ($user->role !== 'admin') {
            if (!in_array($user->role, ['branch_manager', 'accountant', 'reservation_agent'])) {
                $query->where('id', -1); 
            }
            
        }
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', '%' . $search . '%')
                    ->orWhere('contact_person', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->orWhere('license_number', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $agents = $query->latest()->paginate(10);
        $agentStatuses = ['active', 'inactive', 'pending'];
        return view('agents.index', compact('agents', 'agentStatuses'));
    }
    /**
     * Show the form for creating a new agent.
     */
    public function create()
    {
        return view('agents.create');
    }
    /**
     * Store a newly created agent in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255', 'unique:agents'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:agents'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'license_number' => ['nullable', 'string', 'max:50', 'unique:agents'],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'pending'])],
        ]);
        Agent::create($request->all());
        return redirect()->route('agents.index')->with('success', 'تم إضافة الوكيل بنجاح.');
    }
    /**
     * Show the form for editing the specified agent.
     */
    public function edit(Agent $agent) 
    {
        return view('agents.edit', compact('agent'));
    }
    /**
     * Update the specified agent in storage.
     */
    public function update(Request $request, Agent $agent)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255', Rule::unique('agents')->ignore($agent->id)],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('agents')->ignore($agent->id)],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'license_number' => ['nullable', 'string', 'max:50', Rule::unique('agents')->ignore($agent->id)],
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(['active', 'inactive', 'pending'])],
        ]);
        $agent->update($request->all());
        return redirect()->route('agents.index')->with('success', 'تم تحديث الوكيل بنجاح.');
    }
    /**
     * Remove the specified agent from storage.
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();
        return redirect()->route('agents.index')->with('success', 'تم حذف الوكيل بنجاح.');
    }
}
