<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = Agent::all();
        return response()->json([
            'message' => 'تم استرجاع الوكلاء بنجاح.',
            'agents' => $agents
        ]);
    }

    /**
     * Store a newly created resource in storage.
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

        $agent = Agent::create($request->all());

        return response()->json([
            'message' => 'تم إضافة الوكيل بنجاح.',
            'agent' => $agent
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent)
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل الوكيل بنجاح.',
            'agent' => $agent
        ]);
    }

    /**
     * Update the specified resource in storage.
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

        return response()->json([
            'message' => 'تم تحديث الوكيل بنجاح.',
            'agent' => $agent
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        $agent->delete();

        return response()->json([
            'message' => 'تم حذف الوكيل بنجاح.'
        ], 204);
    }
}
