<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة الوكلاء') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة الوكلاء</h3>

                <form action="{{ route('agents.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث باسم الشركة، مسؤول التواصل، البريد، الهاتف، الترخيص..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">تصفية حسب الحالة</option>
                                @foreach ($agentStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ $status == 'active' ? 'نشط' : ($status == 'inactive' ? 'غير نشط' : 'معلق') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('agents.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('agents.create') }}" class="btn btn-success mb-3">إضافة وكيل جديد</a>

                @if ($agents->isEmpty() && !request()->hasAny(['search', 'status']))
                    <div class="alert alert-info" role="alert">
                        لا توجد وكلاء مسجلين حالياً.
                    </div>
                @else
                    @if ($agents->isEmpty() && request()->hasAny(['search', 'status']))
                        <div class="alert alert-warning" role="alert">
                            لا توجد نتائج مطابقة لمعايير البحث والتصفية.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الشركة</th>
                                        <th>مسؤول التواصل</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>رقم الهاتف</th>
                                        <th>العمولة (%)</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($agents as $agent)
                                        <tr>
                                            <td>{{ $agent->id }}</td>
                                            <td>{{ $agent->company_name }}</td>
                                            <td>{{ $agent->contact_person }}</td>
                                            <td>{{ $agent->email }}</td>
                                            <td>{{ $agent->phone_number ?? 'لا يوجد' }}</td>
                                            <td>{{ $agent->commission_rate }}%</td>
                                            <td>
                                                <span
                                                    class="badge {{ $agent->status == 'active' ? 'bg-success' : ($agent->status == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $agent->status == 'active' ? 'نشط' : ($agent->status == 'pending' ? 'معلق' : 'غير نشط') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('agents.edit', $agent) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('agents.destroy', $agent) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الوكيل؟ لن يمكنك التراجع عن ذلك!')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $agents->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
