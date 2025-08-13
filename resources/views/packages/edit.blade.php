<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل الباقة') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">تعديل الباقة: {{ $package->name }}</h3>

                <form action="{{ route('packages.update', $package) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">اسم الباقة <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $package->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">الوصف</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                            rows="3">{{ old('description', $package->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">النوع <span class="text-danger">*</span></label>
                        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                            required>
                            <option value="">اختر النوع</option>
                            <option value="Hajj" {{ old('type', $package->type) == 'Hajj' ? 'selected' : '' }}>حج
                            </option>
                            <option value="Umrah" {{ old('type', $package->type) == 'Umrah' ? 'selected' : '' }}>عمرة
                            </option>
                            <option value="Tour" {{ old('type', $package->type) == 'Tour' ? 'selected' : '' }}>جولات
                                سياحية</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">تاريخ البدء <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" name="start_date"
                                value="{{ old('start_date', $package->start_date?->format('Y-m-d')) }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">تاريخ الانتهاء <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                id="end_date" name="end_date"
                                value="{{ old('end_date', $package->end_date?->format('Y-m-d')) }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price_per_person" class="form-label">السعر للشخص (للعميل) <span
                                    class="text-danger">*</span></label>
                            <input type="number" step="0.01"
                                class="form-control @error('price_per_person') is-invalid @enderror"
                                id="price_per_person" name="price_per_person"
                                value="{{ old('price_per_person', $package->price_per_person) }}" required
                                min="0">
                            @error('price_per_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="agent_price_per_person" class="form-label">السعر للشخص (للوكيل)</label>
                            <input type="number" step="0.01"
                                class="form-control @error('agent_price_per_person') is-invalid @enderror"
                                id="agent_price_per_person" name="agent_price_per_person"
                                value="{{ old('agent_price_per_person', $package->agent_price_per_person) }}"
                                min="0">
                            @error('agent_price_per_person')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="number_of_days" class="form-label">عدد الأيام <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('number_of_days') is-invalid @enderror"
                                id="number_of_days" name="number_of_days"
                                value="{{ old('number_of_days', $package->number_of_days) }}" required min="1">
                            @error('number_of_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="available_seats" class="form-label">المقاعد المتاحة <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('available_seats') is-invalid @enderror"
                                id="available_seats" name="available_seats"
                                value="{{ old('available_seats', $package->available_seats) }}" required
                                min="0">
                            @error('available_seats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status"
                            name="status" required>
                            <option value="active"
                                {{ old('status', $package->status) == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="full" {{ old('status', $package->status) == 'full' ? 'selected' : '' }}>
                                ممتلئة</option>
                            <option value="archived"
                                {{ old('status', $package->status) == 'archived' ? 'selected' : '' }}>أرشيفية</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="includes_text" class="form-label">الخدمات المضمنة (افصل بينها بفاصلة ,)</label>
                        <textarea class="form-control @error('includes_text') is-invalid @enderror" id="includes_text" name="includes_text"
                            rows="3">{{ old('includes_text', $package->includes_text) }}</textarea>
                        @error('includes_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="excludes_text" class="form-label">الخدمات غير المضمنة (افصل بينها بفاصلة
                            ,)</label>
                        <textarea class="form-control @error('excludes_text') is-invalid @enderror" id="excludes_text" name="excludes_text"
                            rows="3">{{ old('excludes_text', $package->excludes_text) }}</textarea>
                        @error('excludes_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">تحديث الباقة</button>
                    <a href="{{ route('packages.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
