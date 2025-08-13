<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة الحجوزات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة الحجوزات</h3>

                <form action="{{ route('bookings.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث بكود الحجز، اسم العميل/الوكيل..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="payment_status" class="form-select">
                                <option value="">تصفية حسب حالة الدفع</option>
                                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>
                                    معلق</option>
                                <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>
                                    مدفوع جزئياً</option>
                                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوع
                                </option>
                                <option value="refunded"
                                    {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>مسترد</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="booking_status" class="form-select">
                                <option value="">تصفية حسب حالة الحجز</option>
                                <option value="pending" {{ request('booking_status') == 'pending' ? 'selected' : '' }}>
                                    قيد الانتظار</option>
                                <option value="confirmed"
                                    {{ request('booking_status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                                <option value="canceled"
                                    {{ request('booking_status') == 'canceled' ? 'selected' : '' }}>ملغي</option>
                                <option value="completed"
                                    {{ request('booking_status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="package_id" class="form-select">
                                <option value="">تصفية حسب الباقة</option>
                                @foreach ($packages as $package)
                                    <option value="{{ $package->id }}"
                                        {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                        {{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('bookings.create') }}" class="btn btn-success mb-3">إضافة حجز جديد</a>

                @if ($bookings->isEmpty() && !request()->hasAny(['search', 'payment_status', 'booking_status', 'package_id']))
                    <div class="alert alert-info" role="alert">
                        لا توجد حجوزات مسجلة حالياً.
                    </div>
                @else
                    @if ($bookings->isEmpty() && request()->hasAny(['search', 'payment_status', 'booking_status', 'package_id']))
                        <div class="alert alert-warning" role="alert">
                            لا توجد نتائج مطابقة لمعايير البحث والتصفية.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>كود الحجز</th>
                                        <th>الباقة</th>
                                        <th>العميل/الوكيل</th>
                                        <th>عدد الأشخاص</th>
                                        <th>المبلغ الكلي</th>
                                        <th>المدفوع</th>
                                        <th>المتبقي</th>
                                        <th>حالة الدفع</th>
                                        <th>حالة الحجز</th>
                                        <th>بواسطة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td>{{ $booking->id }}</td>
                                            <td>{{ $booking->booking_code }}</td>
                                            <td>{{ $booking->package->name ?? 'باقة محذوفة' }}</td>
                                            <td>
                                                @if ($booking->customer)
                                                    {{ $booking->customer->first_name }}
                                                    {{ $booking->customer->last_name }} (عميل)
                                                @elseif($booking->agent)
                                                    {{ $booking->agent->company_name }} (وكيل)
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                            <td>{{ $booking->number_of_people }}</td>
                                            <td>{{ number_format($booking->total_price, 2) }}</td>
                                            <td>{{ number_format($booking->paid_amount, 2) }}</td>
                                            <td>{{ number_format($booking->remaining_amount, 2) }}</td>
                                            <td>
                                                <span
                                                    class="badge {{ $booking->payment_status == 'paid'
                                                        ? 'bg-success'
                                                        : ($booking->payment_status == 'partial'
                                                            ? 'bg-warning'
                                                            : 'bg-danger') }}">
                                                    {{ $booking->payment_status == 'paid'
                                                        ? 'مدفوع'
                                                        : ($booking->payment_status == 'partial'
                                                            ? 'مدفوع جزئياً'
                                                            : 'معلق') }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ $booking->booking_status == 'confirmed'
                                                        ? 'bg-success'
                                                        : ($booking->booking_status == 'pending'
                                                            ? 'bg-info'
                                                            : ($booking->booking_status == 'completed'
                                                                ? 'bg-primary'
                                                                : 'bg-danger')) }}">
                                                    {{ $booking->booking_status == 'confirmed'
                                                        ? 'مؤكد'
                                                        : ($booking->booking_status == 'pending'
                                                            ? 'قيد الانتظار'
                                                            : ($booking->booking_status == 'completed'
                                                                ? 'مكتمل'
                                                                : 'ملغي')) }}
                                                </span>
                                            </td>
                                            <td>{{ $booking->bookedByUser->name ?? 'غير معروف' }}</td>
                                            <td>
                                                <a href="{{ route('bookings.edit', $booking) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('bookings.destroy', $booking) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الحجز؟ هذا سيؤثر على المدفوعات المرتبطة!')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $bookings->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
