<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل الفرع') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">تعديل الفرع: {{ $branch->name }}</h3>

                <form action="{{ route('branches.update', $branch) }}" method="POST">
                    @csrf
                    @method('PUT') {{-- مهم جداً لتحديد نوع الطلب كـ PUT/PATCH --}}

                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الفرع <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $branch->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">العنوان</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address"
                            name="address" value="{{ old('address', $branch->address) }}">
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone_number" class="form-label">رقم الهاتف</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                            id="phone_number" name="phone_number"
                            value="{{ old('phone_number', $branch->phone_number) }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $branch->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="active" {{ old('status', $branch->status) == 'active' ? 'selected' : '' }}>
                                نشط</option>
                            <option value="inactive"
                                {{ old('status', $branch->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">تحديث الفرع</button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
