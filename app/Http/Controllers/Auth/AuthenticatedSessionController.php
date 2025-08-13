<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException; // لا تنسَ استيراد هذا

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // هذه الدالة تقوم بمحاولة المصادقة

        // جلب المستخدم الذي تم مصادقته للتو
        $user = Auth::user();

        // **هنا نضيف منطق التحقق من حالة المستخدم**
        if ($user->status == 'inactive') {
            // إذا كان المستخدم معطلاً، قم بتسجيل الخروج منه فوراً
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // ثم ارمِ استثناءً لمنع تسجيل الدخول وعرض رسالة الخطأ
            throw ValidationException::withMessages([
                'email' => ['حسابك غير نشط. يرجى التواصل مع الإدارة.'],
            ]);
        }
        // **نهاية منطق التحقق**

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
// namespace App\Http\Controllers\Auth;

// use App\Http\Controllers\Controller;
// use App\Http\Requests\Auth\LoginRequest;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\View\View;

// class AuthenticatedSessionController extends Controller
// {
//     /**
//      * Display the login view.
//      */
//     public function create(): View
//     {
//         return view('auth.login');
//     }

//     /**
//      * Handle an incoming authentication request.
//      */
//     public function store(LoginRequest $request): RedirectResponse
//     {
//         $request->authenticate();

//         $request->session()->regenerate();

//         return redirect()->intended(route('dashboard', absolute: false));
//     }

//     /**
//      * Destroy an authenticated session.
//      */
//     public function destroy(Request $request): RedirectResponse
//     {
//         Auth::guard('web')->logout();

//         $request->session()->invalidate();

//         $request->session()->regenerateToken();

//         return redirect('/');
//     }
// }
