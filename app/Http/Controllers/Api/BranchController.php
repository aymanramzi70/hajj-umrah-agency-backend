<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     * عرض قائمة بجميع الفروع.
     */
    public function index()
    {
        
        $branches = Branch::all();
        return response()->json([
            'message' => 'تم استرجاع الفروع بنجاح.',
            'branches' => $branches
        ]);
    }

    /**
     * Store a newly created resource in storage.
     * تخزين فرع جديد في قاعدة البيانات.
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

        
        $branch = Branch::create($request->all());

        return response()->json([
            'message' => 'تم إضافة الفرع بنجاح.',
            'branch' => $branch
        ], 201); 
    }

    /**
     * Display the specified resource.
     * عرض تفاصيل فرع معين.
     */
    public function show(Branch $branch) 
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل الفرع بنجاح.',
            'branch' => $branch
        ]);
    }

    /**
     * Update the specified resource in storage.
     * تحديث معلومات فرع معين.
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

        return response()->json([
            'message' => 'تم تحديث الفرع بنجاح.',
            'branch' => $branch
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * حذف فرع معين.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();

        return response()->json([
            'message' => 'تم حذف الفرع بنجاح.'
        ], 204); 
    }
}
