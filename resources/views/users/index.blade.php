<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة المستخدمين') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">قائمة المستخدمين</h3>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">إضافة مستخدم جديد</a>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($users->isEmpty())
                    <div class="alert alert-info">لا يوجد مستخدمون لعرضهم حالياً.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>رقم الهاتف</th>
                                    <th>الدور</th>
                                    <th>الفرع</th>
                                    <th>الحالة</th> 
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone_number ?? 'N/A' }}</td>
                                        <td>{{ __($user->role) }}</td>
                                        <td>{{ $user->branch->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <span
                                                class="badge {{ $user->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $user->status == 'active' ? 'نشط' : 'معطل' }}
                                            </span>
                                        </td> 
                                        <td>
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="btn btn-sm btn-warning">تعديل</a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
