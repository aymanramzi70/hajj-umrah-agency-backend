<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إدارة المدفوعات') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">قائمة المدفوعات</h3>

                <form action="{{ route('payments.index') }}" method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="بحث بكود الحجز، رقم المعاملة، أو طريقة الدفع..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="booking_id" class="form-select">
                                <option value="">تصفية حسب الحجز</option>
                                @foreach ($bookingsForFilter as $booking)
                                    <option value="{{ $booking->id }}"
                                        {{ request('booking_id') == $booking->id ? 'selected' : '' }}>
                                        {{ $booking->booking_code }}
                                        @if ($booking->customer)
                                            ({{ $booking->customer->first_name }} {{ $booking->customer->last_name }})
                                        @elseif($booking->agent)
                                            ({{ $booking->agent->company_name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="payment_method" class="form-select">
                                <option value="">تصفية حسب طريقة الدفع</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method }}"
                                        {{ request('payment_method') == $method ? 'selected' : '' }}>
                                        {{ $method }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">بحث وتصفية</button>
                            <a href="{{ route('payments.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                        </div>
                    </div>
                </form>

                <a href="{{ route('payments.create') }}" class="btn btn-success mb-3">إضافة دفعة جديدة</a>

                @if ($payments->isEmpty() && !request()->hasAny(['search', 'booking_id', 'payment_method']))
                    <div class="alert alert-info" role="alert">
                        لا توجد مدفوعات مسجلة حالياً.
                    </div>
                @else
                    @if ($payments->isEmpty() && request()->hasAny(['search', 'booking_id', 'payment_method']))
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
                                        <th>العميل/الوكيل</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الدفع</th>
                                        <th>طريقة الدفع</th>
                                        <th>الموظف المستلم</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->id }}</td>
                                            <td>{{ $payment->booking->booking_code ?? 'حجز محذوف' }}</td>
                                            <td>
                                                @if ($payment->booking)
                                                    @if ($payment->booking->customer)
                                                        {{ $payment->booking->customer->first_name }}
                                                        {{ $payment->booking->customer->last_name }} (عميل)
                                                    @elseif($payment->booking->agent)
                                                        {{ $payment->booking->agent->company_name }} (وكيل)
                                                    @else
                                                        غير محدد
                                                    @endif
                                                @else
                                                    غير محدد
                                                @endif
                                            </td>
                                            <td>{{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->receivedByUser->name ?? 'غير معروف' }}</td>
                                            <td>
                                                <a href="{{ route('payments.edit', $payment) }}"
                                                    class="btn btn-sm btn-primary">تعديل</a>
                                                <form action="{{ route('payments.destroy', $payment) }}" method="POST"
                                                    style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذه الدفعة؟ هذا سيؤثر على رصيد الحجز المرتبط!')">حذف</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
