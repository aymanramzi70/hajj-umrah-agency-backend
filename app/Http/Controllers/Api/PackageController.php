<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packages = Package::all();
        return response()->json([
            'message' => 'تم استرجاع الباقات بنجاح.',
            'packages' => $packages
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['Hajj', 'Umrah', 'Tour'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'agent_price_per_person' => ['nullable', 'numeric', 'min:0'],
            'number_of_days' => ['required', 'integer', 'min:1'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'full', 'archived'])],
            'includes' => ['nullable', 'array'], 
            'excludes' => ['nullable', 'array'], 
        ]);

        $package = Package::create($request->all());

        return response()->json([
            'message' => 'تم إضافة الباقة بنجاح.',
            'package' => $package
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        
        return response()->json([
            'message' => 'تم استرجاع تفاصيل الباقة بنجاح.',
            'package' => $package
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(['Hajj', 'Umrah', 'Tour'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_person' => ['required', 'numeric', 'min:0'],
            'agent_price_per_person' => ['nullable', 'numeric', 'min:0'],
            'number_of_days' => ['required', 'integer', 'min:1'],
            'available_seats' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', Rule::in(['active', 'full', 'archived'])],
            'includes' => ['nullable', 'array'],
            'excludes' => ['nullable', 'array'],
        ]);

        $package->update($request->all());

        return response()->json([
            'message' => 'تم تحديث الباقة بنجاح.',
            'package' => $package
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        $package->delete();

        return response()->json([
            'message' => 'تم حذف الباقة بنجاح.'
        ], 204);
    }
}
