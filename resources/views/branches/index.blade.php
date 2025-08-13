<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة الفروع') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة الفروع</h3>

                <form action="{{ route('branches.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث بالاسم، العنوان، الهاتف، البريد..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">تصفية حسب الحالة</option>
                                @foreach ($branchStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $status == 'active' ? 'نشط' : 'غير نشط' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('branches.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('branches.create') }}" class="btn btn-success mb-3">إضافة فرع جديد</a>

                @if ($branches->isEmpty() && !request()->hasAny(['search', 'status']))
                    <div class="alert alert-info" role="alert">
                        لا توجد فروع مسجلة حالياً.
                    </div>
                @else
                    @if ($branches->isEmpty() && request()->hasAny(['search', 'status']))
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
                                        <th>العنوان</th>
                                        <th>رقم الهاتف</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($branches as $branch)
                                        <tr>
                                            <td>{{ $branch->id }}</td>
                                            <td>{{ $branch->name }}</td>
                                            <td>{{ $branch->address }}</td>
                                            <td>{{ $branch->phone_number }}</td>
                                            <td>{{ $branch->email }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $branch->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $branch->status == 'active' ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('branches.edit', $branch) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('branches.destroy', $branch) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الفرع؟ لن يمكنك التراجع عن ذلك!')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $branches->appends(request()->except('page'))->links() }}
                        </div>
                    @endif 
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>

