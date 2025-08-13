import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
    host: '192.168.8.27', // أو استخدم '0.0.0.0' للسماح لكل الأجهزة
    port: 5173,            // يمكنك تغييره إذا كان مستخدم مسبقًا
    strictPort: true,      // يمنع استخدام بورت بديل تلقائيًا
  },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
