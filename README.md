# نظام إدارة وكالة الحج والعمرة (الواجهة الخلفية - Laravel)

![Laravel Logo](https://raw.githubusercontent.com/laravel/art/master/logo-lockup.svg)

نظام متكامل لإدارة وكالة خدمات الحج والعمرة، مصمم خصيصًا للشركات ذات الفروع المتعددة. هذه الواجهة الخلفية (Backend) مبنية على إطار عمل **Laravel**، وتوفر واجهات برمجة تطبيقات (APIs) قوية وآمنة لتطبيق الويب الإداري وتطبيق الموبايل الخاص بالعملاء.

---

## الميزات الرئيسية للواجهة الخلفية

- **إدارة المستخدمين والأدوار**: نظام كامل لإدارة مستخدمي لوحة التحكم (الموظفين والوكلاء) مع أدوار وصلاحيات محددة.
- **إدارة الموارد**: واجهات API كاملة لعمليات CRUD على (الفروع، العملاء، الوكلاء الخارجيين، الباقات، الحجوزات، والمدفوعات).
- **نظام المصادقة**: استخدام Laravel Sanctum لتوفير مصادقة آمنة عبر التوكن (Token-based authentication) لتطبيق الموبايل.
- **الإشعارات الفورية**: تكامل مع Firebase Cloud Messaging (FCM v1) لإرسال إشعارات فورية إلى أجهزة العملاء.
- **تكامل الدفع الإلكتروني**: دعم بوابة الدفع Stripe لمعالجة المدفوعات عبر الإنترنت.
- **المنطق التجاري**: تطبيق منطق معقد مثل تحديث المقاعد المتاحة في الباقات بشكل تلقائي عند كل عملية حجز أو تعديل.

---

## المتطلبات

- PHP 8.2 أو أحدث.
- Composer.
- Node.js و npm.
- MySQL أو PostgreSQL أو أي قاعدة بيانات مدعومة من Laravel.

---

## دليل التثبيت

اتبع الخطوات التالية لتثبيت وتشغيل المشروع على جهازك:

1.  **استنساخ المستودع (Clone the repository):**
    ```bash
    git clone (https://github.com/aymanramzi70/hajj-umrah-agency-backend.git)
    cd hajj-umrah-agency-backend
    ```

2.  **تثبيت الاعتمادات (Install dependencies):**
    ```bash
    composer install
    npm install
    ```

3.  **إعداد ملف البيئة (.env):**
    - انسخ ملف `env.example` وأعد تسميته إلى `.env`.
      ```bash
      cp .env.example .env
      ```
    - قم بتعديل متغيرات قاعدة البيانات (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    - قم بتوليد مفتاح التطبيق:
      ```bash
      php artisan key:generate
      ```
    - **إعدادات Firebase:**
      - أضف مسار ملف مفتاح حساب الخدمة (JSON) الذي تم تنزيله من Firebase:
      ```env
      FIREBASE_CREDENTIALS=/path/to/your-project-id-firebase-adminsdk-xxxxx-xxxxx.json
      ```
      - يفضل وضعه في مجلد `storage/app/` وتحديث المسار في `config/services.php` ليكون `storage_path('app/...')`.
    - **إعدادات Stripe:**
      - أضف مفاتيح Stripe API (من وضع الاختبار):
      ```env
      STRIPE_KEY=pk_test_...
      STRIPE_SECRET=sk_test_...
      ```

4.  **تشغيل عمليات الترحيل وتعبئة البيانات (Run migrations and seeders):**
    - ستقوم هذه الخطوة بإنشاء الجداول وملئها ببيانات تجريبية.
      ```bash
      php artisan migrate:fresh --seed
      ```

5.  **تجميع الأصول (Compile assets):**
    - لتجميع ملفات CSS و JavaScript للوحة التحكم.
      ```bash
      npm run dev
      # أو npm run watch للعملية التلقائية
      ```

6.  **تشغيل الخادم (Run the server):**
    ```bash
    php artisan serve
    ```

---

## دليل الاستخدام

-   **لوحة تحكم الويب**: يمكنك الوصول إليها من `http://127.0.0.1:8000/login`، واستخدام المستخدم التجريبي الذي تم إنشاؤه بواسطة الـ seeder (`admin@hajjumrah.com` وكلمة المرور `password`).
-   **الـ APIs**: يمكن الوصول إليها من `http://127.0.0.1:8000/api/...`
-   **الإشعارات الفورية**: يمكنك إرسال إشعار تجريبي من سطر الأوامر بعد تسجيل الدخول من التطبيق:
    ```bash
    php artisan send:push-notification user@email.com "عنوان الإشعار" "محتوى الإشعار"
    ```
-   **تكامل الدفع**: يمكنك اختبار APIs الدفع عبر Stripe باستخدام أرقام البطاقات التجريبية في وضع الاختبار.

---

## المساهمة

للتطوير أو المساهمة في المشروع، يرجى استنساخ المستودع، إنشاء فرع جديد للميزة أو الإصلاح، ثم إرسال طلب سحب (Pull Request).

---
