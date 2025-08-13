<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إضافة مستخدم جديد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">إضافة مستخدم جديد</h3>

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني <span
                                class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                            id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span
                                class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" required>
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">الدور <span class="text-danger">*</span></label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                            required>
                            <option value="">اختر الدور</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مسؤول عام (Admin)
                            </option>
                            <option value="branch_manager" {{ old('role') == 'branch_manager' ? 'selected' : '' }}>مدير
                                فرع (Branch Manager)</option>
                            <option value="reservation_agent"
                                {{ old('role') == 'reservation_agent' ? 'selected' : '' }}>موظف حجوزات (Reservation
                                Agent)</option>
                            <option value="accountant" {{ old('role') == 'accountant' ? 'selected' : '' }}>محاسب
                                (Accountant)</option>
                            <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>وكيل خارجي (Agent)
                            </option>
                            <option value="customer_app_user"
                                {{ old('role') == 'customer_app_user' ? 'selected' : '' }}>مستخدم تطبيق عميل (Customer
                                App User)</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="branch_id" class="form-label">الفرع (اختياري)</label>
                        <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id"
                            name="branch_id">
                            <option value="">لا يوجد فرع محدد</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3"> 
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>معطل</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
