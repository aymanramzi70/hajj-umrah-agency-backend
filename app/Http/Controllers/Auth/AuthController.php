<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    /**
     * Register a new user and create a corresponding customer record.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'unique:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:customers'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],






        ]);

        try {

            return DB::transaction(function () use ($request) {

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'role' => 'customer_app_user',
                    'branch_id' => null,
                ]);


                $customer = \App\Models\Customer::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,





                    'source_branch_id' => null,
                    'user_id' => $user->id,
                ]);






                $token = $user->createToken('mobile_app_flutter')->plainTextToken;

                return response()->json([
                    'message' => 'تم إنشاء الحساب بنجاح.',
                    'user' => $user,
                    'customer' => $customer,
                    'token' => $token,
                ], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل إنشاء الحساب: ' . $e . getMessage()], 500);
        }
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الاعتماد المقدمة غير صحيحة.'],
            ]);
        }
        if ($user->status == 'inactive') {
            throw ValidationException::withMessages([
                'email' => ['حسابك غير نشط. يرجى التواصل مع الإدارة.'],
            ]);
        }


        $user->tokens()->where('name', $request->device_name)->delete();


        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'تم تسجيل الدخول بنجاح.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج بنجاح.']);
    }

    /**
     * Get the authenticated user's details.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    /**
     * Get the authenticated user's customer profile.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerProfile(Request $request)
    {
        $user = $request->user();


        $customer = $user->customer;

        if (!$customer) {
            return response()->json(['message' => 'لم يتم العثور على ملف شخصي للعميل مرتبط بهذا المستخدم.'], 404);
        }

        return response()->json([
            'message' => 'تم استرجاع ملف العميل بنجاح.',
            'customer' => $customer
        ]);
    }

    /**
     * Update the authenticated user's customer profile.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCustomerProfile(Request $request)
    {
        $user = $request->user();


        $customer = $user->customer;

        if (!$customer) {
            return response()->json(['message' => 'لم يتم العثور على ملف شخصي للعميل مرتبط بهذا المستخدم.'], 404);
        }


        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],

            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('customers')->ignore($customer->id)],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('customers')->ignore($customer->id)],
            'national_id' => ['nullable', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'passport_number' => ['nullable', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female'])],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        try {

            $customer->update($request->only([
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'national_id',
                'passport_number',
                'date_of_birth',
                'gender',
                'address',
            ]));



            $user->update([
                'name' => $request->input('first_name') . ' ' . $request->input('last_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
            ]);

            return response()->json([
                'message' => 'تم تحديث ملف العميل بنجاح.',
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل تحديث ملف العميل: ' . $e->getMessage()], 500);
        }
    }
    public function saveDeviceToken(Request $request)
    {
        $request->validate([
            'device_token' => ['required', 'string', 'max:255'],
        ]);

        $user = $request->user();

        try {




            $user->fcm_token = $request->input('device_token');
            $user->save();

            return response()->json([
                'message' => 'تم حفظ رمز الجهاز بنجاح.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'فشل حفظ رمز الجهاز: ' . $e . getMessage()], 500);
        }
    }
}
