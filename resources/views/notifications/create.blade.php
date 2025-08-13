<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إرسال إشعار فوري') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">إرسال إشعار فوري</h3>

                <form action="{{ route('notifications.send') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان الإشعار <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">محتوى الإشعار <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="4"
                            required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="recipient" class="form-label">المستلمون <span class="text-danger">*</span></label>
                        <select class="form-select @error('recipient') is-invalid @enderror" id="recipient"
                            name="recipient" required>
                            <option value="all_users" {{ old('recipient') == 'all_users' ? 'selected' : '' }}>جميع
                                المستخدمين</option>
                            <option value="specific_user" {{ old('recipient') == 'specific_user' ? 'selected' : '' }}>
                                مستخدم محدد</option>
                        </select>
                        @error('recipient')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="user_email_field"
                        style="display: {{ old('recipient') == 'specific_user' ? 'block' : 'none' }};">
                        <label for="user_email" class="form-label">البريد الإلكتروني للمستخدم</label>
                        <select class="form-select @error('user_email') is-invalid @enderror" id="user_email"
                            name="user_email">
                            <option value="">اختر بريد المستخدم</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->email }}"
                                    {{ old('user_email') == $user->email ? 'selected' : '' }}>{{ $user->name }}
                                    ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @error('user_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">إرسال الإشعار</button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const recipientSelect = document.getElementById('recipient');
                const userEmailField = document.getElementById('user_email_field');
                const userEmailSelect = document.getElementById('user_email');

                function toggleUserEmailField() {
                    if (recipientSelect.value === 'specific_user') {
                        userEmailField.style.display = 'block';
                        userEmailSelect.setAttribute('required', 'required');
                    } else {
                        userEmailField.style.display = 'none';
                        userEmailSelect.removeAttribute('required');
                    }
                }

                recipientSelect.addEventListener('change', toggleUserEmailField);

                toggleUserEmailField();
            });
        </script>
    @endpush
</x-admin-layout>
