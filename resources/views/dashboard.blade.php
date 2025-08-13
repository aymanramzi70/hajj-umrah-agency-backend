<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('لوحة القيادة') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="p-6 text-gray-900">
                    مرحباً، {{ Auth::user()->name }}! أنت مسجل الدخول. (دورك: {{ __($user->role) }})
                </div>

                {{-- إحصائيات عامة --}}
                <h4 class="mb-4 font-bold text-lg">إحصائيات عامة</h4>
                <div class="row mt-4 gy-4">
                    {{-- الفروع --}}
                    @if(in_array($user->role, ['admin', 'branch_manager']))
                        <x-dashboard.card route="branches.index" title="الفروع" count="{{ $branchesCount }}" bg="primary" />
                    @endif

                    {{-- العملاء --}}
                    @if(in_array($user->role, ['admin', 'reservation_agent', 'agent']))
                        <x-dashboard.card route="customers.index" title="العملاء" count="{{ $customersCount }}" bg="success" />
                    @endif

                    {{-- الوكلاء --}}
                    @if($user->role === 'admin')
                        <x-dashboard.card route="agents.index" title="الوكلاء الخارجيين" count="{{ $agentsCount }}" bg="warning" />
                    @endif

                    {{-- الباقات --}}
                    @if(in_array($user->role, ['admin', 'reservation_agent', 'agent']))
                        <x-dashboard.card route="packages.index" title="الباقات" count="{{ $packagesCount }}" bg="info" />
                    @endif

                    {{-- الحجوزات --}}
                    @if($user->role !== 'customer_app_user')
                        <x-dashboard.card route="bookings.index" title="الحجوزات" count="{{ $bookingsCount }}" bg="secondary" />
                    @endif

                    {{-- المدفوعات --}}
                    @if(in_array($user->role, ['admin', 'accountant', 'reservation_agent']))
                        <x-dashboard.card route="payments.index" title="المدفوعات" count="{{ $paymentsCount }}" bg="dark" />
                    @endif
                </div>

                {{-- الملخص المالي --}}
                @if($user->role !== 'customer_app_user')
                    <h4 class="mt-5 mb-4 font-bold text-lg">ملخص مالي</h4>
                    <div class="row gy-4">
                        <x-dashboard.summary-card title="إجمالي قيمة الحجوزات" amount="{{ $totalBookingsValue ?? 0 }}" bg="success" />
                        <x-dashboard.summary-card title="المبالغ المدفوعة" amount="{{ $totalPaidAmount ?? 0 }}" bg="primary" />
                        <x-dashboard.summary-card title="المبالغ المتبقية" amount="{{ $totalRemainingAmount ?? 0 }}" bg="danger" />
                    </div>
                @endif

                {{-- توزيع الحجوزات حسب الباقة --}}
                @if($user->role !== 'customer_app_user')
                    <h4 class="mt-5 mb-4 font-bold text-lg">توزيع الحجوزات حسب الباقة</h4>
                    @if($bookingsByType->isEmpty())
                        <div class="alert alert-info">لا توجد حجوزات لتوزيعها حالياً.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>الباقة</th>
                                        <th>عدد الحجوزات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookingsByType as $bookingType)
                                        <tr>
                                            <td>{{ $bookingType->package->name ?? 'باقة محذوفة' }}</td>
                                            <td>{{ $bookingType->total }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
</x-admin-layout>



