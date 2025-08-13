<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage; // استيراد CloudMessage
use Kreait\Firebase\Messaging\Notification; // استيراد Notification
use Kreait\Firebase\Factory; // استيراد Factory

class SendPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:push-notification {user_email} {title} {body}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a push notification to a specific user by email using FCM v1.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userEmail = $this->argument('user_email');
        $title = $this->argument('title');
        $body = $this->argument('body');

        $user = User::where('email', $userEmail)->first();

        if (!$user) {
            $this->error('المستخدم غير موجود بهذا البريد الإلكتروني: ' . $userEmail);
            return Command::FAILURE;
        }

        if (empty($user->fcm_token)) {
            $this->warn('المستخدم ليس لديه رمز جهاز FCM مسجل: ' . $userEmail);
            return Command::FAILURE;
        }

        try {
            // تهيئة Firebase
            $factory = (new Factory)->withServiceAccount(config('services.firebase.credentials'));
            $messaging = $factory->createMessaging();

            // إنشاء الإشعار
            $notification = Notification::create($title, $body);

            // بناء الرسالة
            $message = CloudMessage::new()
                ->withChangedTarget('token', $user->fcm_token) // تحديد رمز الجهاز
                ->withNotification($notification)
                ->withData([ // البيانات الإضافية
                    'type' => 'general_message',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK', // مهم لفتح التطبيق
                    // يمكنك إضافة أي بيانات أخرى هنا
                ]);

            // إرسال الرسالة
            $messaging->send($message);

            $this->info('تم إرسال الإشعار بنجاح إلى: ' . $userEmail);
            return Command::SUCCESS;
        } catch (\Kreait\Firebase\Exception\FirebaseException $e) {
            $this->error('خطأ في Firebase: ' . $e->getMessage());
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('حدث خطأ غير متوقع: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
