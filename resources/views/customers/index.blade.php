<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة العملاء') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة العملاء</h3>

                <form action="{{ route('customers.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث بالاسم، البريد، الهاتف، الهوية، الجواز..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="branch_id" class="form-select">
                                <option value="">تصفية حسب الفرع</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"
                                        {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="gender" class="form-select">
                                <option value="">تصفية حسب الجنس</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>أنثى
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('customers.create') }}" class="btn btn-success mb-3">إضافة عميل جديد</a>

                @if ($customers->isEmpty() && !request()->hasAny(['search', 'branch_id', 'gender']))
                    <div class="alert alert-info" role="alert">
                        لا توجد عملاء مسجلين حالياً.
                    </div>
                @else
                    @if ($customers->isEmpty() && request()->hasAny(['search', 'branch_id', 'gender']))
                        <div class="alert alert-warning" role="alert">
                            لا توجد نتائج مطابقة لمعايير البحث والتصفية.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>الاسم الكامل</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>رقم الهاتف</th>
                                        <th>رقم الجواز</th>
                                        <th>الفرع</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ $customer->id }}</td>
                                            <td>{{ $customer->first_name }} {{ $customer->last_name }}</td>
                                            <td>{{ $customer->email ?? 'لا يوجد' }}</td>
                                            <td>{{ $customer->phone_number }}</td>
                                            <td>{{ $customer->passport_number ?? 'لا يوجد' }}</td>
                                            <td>{{ $customer->sourceBranch->name ?? 'غير محدد' }}</td>
                                            <td>
                                                <a href="{{ route('customers.edit', $customer) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('customers.destroy', $customer) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا العميل؟ لن يمكنك التراجع عن ذلك!')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $customers->appends(request()->except('page'))->links() }}
                        </div>
                    @endif 
                @endif 
        </div>
    </div>
</x-admin-layout>
