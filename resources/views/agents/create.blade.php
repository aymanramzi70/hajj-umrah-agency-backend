<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إضافة وكيل جديد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">إضافة وكيل جديد</h3>

                <form action="{{ route('agents.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="company_name" class="form-label">اسم الشركة <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                            id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                        @error('company_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="contact_person" class="form-label">مسؤول التواصل <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                            id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                        @error('contact_person')
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
                        <label for="address" class="form-label">العنوان</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="license_number" class="form-label">رقم الترخيص التجاري</label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror"
                            id="license_number" name="license_number" value="{{ old('license_number') }}">
                        @error('license_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="commission_rate" class="form-label">نسبة العمولة (%) <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01"
                            class="form-control @error('commission_rate') is-invalid @enderror" id="commission_rate"
                            name="commission_rate" value="{{ old('commission_rate') }}" required min="0"
                            max="100">
                        @error('commission_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                            </option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ الوكيل</button>
                    <a href="{{ route('agents.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
