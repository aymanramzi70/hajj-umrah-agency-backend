<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('sourceBranch')->get(); 
        return response()->json([
            'message' => 'تم استرجاع العملاء بنجاح.',
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:customers'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:customers'],
            'passport_number' => ['nullable', 'string', 'max:50', 'unique:customers'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'address' => ['nullable', 'string', 'max:500'],
            'source_branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $customer = Customer::create($request->all());

        return response()->json([
            'message' => 'تم إضافة العميل بنجاح.',
            'customer' => $customer->load('sourceBranch') 
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return response()->json([
            'message' => 'تم استرجاع تفاصيل العميل بنجاح.',
            'customer' => $customer->load('sourceBranch')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'national_id' => ['nullable', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'passport_number' => ['nullable', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'address' => ['nullable', 'string', 'max:500'],
            'source_branch_id' => ['nullable', 'exists:branches,id'],
        ]);

        $customer->update($request->all());

        return response()->json([
            'message' => 'تم تحديث العميل بنجاح.',
            'customer' => $customer->load('sourceBranch')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'تم حذف العميل بنجاح.'
        ], 204);
    }
}
