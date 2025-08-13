<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة الباقات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة الباقات</h3>

                <form action="{{ route('packages.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث باسم الباقة أو الوصف..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">تصفية حسب النوع</option>
                                @foreach ($packageTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ request('type') == $type ? 'selected' : '' }}>{{ __($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">تصفية حسب الحالة</option>
                                @foreach ($packageStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $status == 'active' ? 'نشط' : ($status == 'full' ? 'ممتلئة' : 'أرشيفية') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('packages.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                @if ($user->role === 'admin')
                    <a href="{{ route('packages.create') }}" class="btn btn-success mb-3">إضافة باقة جديدة</a>
                @endif

                @if ($packages->isEmpty() && !request()->hasAny(['search', 'type', 'status']))
                    <div class="alert alert-info" role="alert">
                        لا توجد باقات مسجلة حالياً.
                    </div>
                @else
                    @if ($packages->isEmpty() && request()->hasAny(['search', 'type', 'status']))
                        <div class="alert alert-warning" role="alert">
                            لا توجد نتائج مطابقة لمعايير البحث والتصفية.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم</th>
                                        <th>النوع</th>
                                        <th>تاريخ البدء</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>السعر (شخص)</th>
                                        <th>مقاعد متاحة</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $package)
                                        <tr>
                                            <td>{{ $package->id }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>{{ $package->type }}</td>
                                            <td>{{ $package->start_date->format('Y-m-d') }}</td>
                                            <td>{{ $package->end_date->format('Y-m-d') }}</td>
                                            <td>{{ number_format($package->price_per_person, 2) }}</td>
                                            <td>{{ $package->available_seats }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $package->status == 'active' ? 'bg-success' : ($package->status == 'full' ? 'bg-warning' : 'bg-secondary') }}">
                                                    {{ $package->status == 'active' ? 'نشط' : ($package->status == 'full' ? 'ممتلئة' : 'أرشيفية') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($user->role === 'admin')
                                                    <a href="{{ route('packages.edit', $package) }}"
                                                        class="btn btn-sm btn-primary">تعديل</a>
                                                    <form action="{{ route('packages.destroy', $package) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('هل أنت متأكد من حذف هذه الباقة؟ لن يمكنك التراجع عن ذلك!')">حذف</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">عرض فقط</span> 
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $packages->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

