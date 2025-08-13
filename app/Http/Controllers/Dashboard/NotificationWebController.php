<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage; 
use Kreait\Firebase\Messaging\Notification; 
use Kreait\Firebase\Factory; 
use Exception;

class NotificationWebController extends Controller
{
    /**
     * Show the form for sending a new push notification.
     */
    public function create()
    {
        
        
        $users = User::whereNotNull('fcm_token')->select('id', 'name', 'email')->get();
        return view('notifications.create', compact('users'));
    }

    /**
     * Send a push notification based on the form data.
     */
    public function send(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'recipient' => ['required', 'string', 'in:all_users,specific_user'],
            'user_email' => ['nullable', 'email', 'exists:users,email', 'required_if:recipient,specific_user'],
        ]);

        
        
        if (empty(config('services.firebase.credentials'))) {
            return redirect()->back()->with('error', 'ملف اعتماد Firebase غير معرف. يرجى تهيئة إعدادات Firebase Admin SDK.');
        }

        $recipients = [];
        if ($request->recipient == 'all_users') {
            $recipients = User::whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
            if (empty($recipients)) {
                return redirect()->back()->with('error', 'لا توجد أجهزة مسجلة لإرسال الإشعار إليها.');
            }
        } else { 
            $user = User::where('email', $request->user_email)->first();
            if (!$user || empty($user->fcm_token)) {
                return redirect()->back()->with('error', 'المستخدم غير موجود أو ليس لديه رمز جهاز مسجل.');
            }
            $recipients[] = $user->fcm_token;
        }

        if (empty($recipients)) {
            return redirect()->back()->with('error', 'لم يتم العثور على مستلمين للإشعار.');
        }

        $successCount = 0;
        $failureCount = 0;

        try {
            
            $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
            $messaging = $factory->createMessaging();

            
            $notification = Notification::create($request->title, $request->body);

            
            foreach ($recipients as $fcmToken) {
                try {
                    $message = CloudMessage::new()
                        ->withChangedTarget('token', $fcmToken)
                        ->withNotification($notification)
                        ->withData([
                            'type' => 'general_message',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ]);

                    $messaging->send($message);
                    $successCount++;
                } catch (\Kreait\Firebase\Exception\MessagingException $e) {
                    $failureCount++;
                    \Log::error('فشل إرسال إشعار إلى رمز الجهاز ' . $fcmToken . ': ' . $e->getMessage());
                }
            }

        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            return redirect()->back()->with('error', 'خطأ في تهيئة Firebase أو الاتصال: ' . $e->getMessage());
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ غير متوقع أثناء إرسال الإشعارات: ' . $e->getMessage());
        }


        if ($successCount > 0 && $failureCount == 0) {
            return redirect()->back()->with('success', 'تم إرسال الإشعار بنجاح إلى ' . $successCount . ' جهاز.');
        } elseif ($successCount > 0 && $failureCount > 0) {
            return redirect()->back()->with('warning', 'تم إرسال الإشعار إلى ' . $successCount . ' جهاز، وفشل إرساله إلى ' . $failureCount . ' جهاز.');
        } else {
            return redirect()->back()->with('error', 'فشل إرسال الإشعار إلى جميع الأجهزة.');
        }
    }
}
