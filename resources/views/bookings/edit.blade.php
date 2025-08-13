<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل الحجز') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">تعديل الحجز: {{ $booking->booking_code }}</h3>

                <form action="{{ route('bookings.update', $booking) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- حقل اختيار الباقة --}}
                    <div class="mb-3">
                        <label for="package_id" class="form-label">الباقة <span class="text-danger">*</span></label>
                        <select class="form-select @error('package_id') is-invalid @enderror" id="package_id"
                            name="package_id" required>
                            <option value="">اختر الباقة</option>
                            @foreach ($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price_per_person }}"
                                    data-agent-price="{{ $package->agent_price_per_person }}"
                                    {{ old('package_id', $booking->package_id) == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} ({{ number_format($package->price_per_person, 2) }} /
                                    {{ number_format($package->agent_price_per_person, 2) }} - مقاعد متاحة:
                                    {{ $package->available_seats }})
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- منطق اختيار العميل/الوكيل --}}
                    @if ($user->role === 'agent')
                        {{-- إذا كان المستخدم وكيل خارجي، فالحجز دائماً يكون لعميل مباشر تابع له --}}
                        <input type="hidden" name="agent_id" value="{{ Auth::id() }}">
                        <input type="hidden" id="customer_agent_type_hidden" value="customer">

                        <div class="mb-3" id="customer_select">
                            <label for="customer_id" class="form-label">العميل <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                name="customer_id" required>
                                <option value="">اختر العميل</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $booking->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                        ({{ $customer->phone_number }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        {{-- للأدوار الأخرى (admin, reservation_agent, branch_manager, accountant) --}}
                        <div class="mb-3">
                            <label for="customer_agent_type" class="form-label">نوع العميل <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="customer_agent_type" required>
                                <option value="">اختر نوع العميل</option>
                                <option value="customer"
                                    {{ old('customer_id', $booking->customer_id) ? 'selected' : '' }}>عميل مباشر
                                </option>
                                <option value="agent" {{ old('agent_id', $booking->agent_id) ? 'selected' : '' }}>وكيل
                                </option>
                            </select>
                        </div>

                        <div class="mb-3" id="customer_select"
                            style="display: {{ old('customer_id', $booking->customer_id) ? 'block' : 'none' }};">
                            <label for="customer_id" class="form-label">العميل</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id"
                                name="customer_id">
                                <option value="">اختر العميل</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id', $booking->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                        ({{ $customer->phone_number }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="agent_select"
                            style="display: {{ old('agent_id', $booking->agent_id) ? 'block' : 'none' }};">
                            <label for="agent_id" class="form-label">الوكيل</label>
                            <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id"
                                name="agent_id">
                                <option value="">اختر الوكيل</option>
                                @foreach ($agents as $agent_obj)
                                    <option value="{{ $agent_obj->id }}"
                                        {{ old('agent_id', $booking->agent_id) == $agent_obj->id ? 'selected' : '' }}>
                                        {{ $agent_obj->company_name }} ({{ $agent_obj->contact_person }})</option>
                                @endforeach
                            </select>
                            @error('agent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    {{-- ... بقية حقول النموذج ... --}}
                    <div class="mb-3">
                        <label for="number_of_people" class="form-label">عدد الأشخاص <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('number_of_people') is-invalid @enderror"
                            id="number_of_people" name="number_of_people"
                            value="{{ old('number_of_people', $booking->number_of_people) }}" required min="1">
                        @error('number_of_people')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="total_price" class="form-label">المبلغ الكلي <span
                                class="text-danger">*</span></label>
                        <input type="number" step="0.01"
                            class="form-control @error('total_price') is-invalid @enderror" id="total_price"
                            name="total_price" value="{{ old('total_price', $booking->total_price) }}" required
                            min="0">
                        @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="paid_amount" class="form-label">المبلغ المدفوع</label>
                        <input type="number" step="0.01"
                            class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount"
                            name="paid_amount" value="{{ old('paid_amount', $booking->paid_amount) }}" min="0">
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="booking_status" class="form-label">حالة الحجز <span
                                class="text-danger">*</span></label>
                        <select class="form-select @error('booking_status') is-invalid @enderror" id="booking_status"
                            name="booking_status" required>
                            <option value="pending"
                                {{ old('booking_status', $booking->booking_status) == 'pending' ? 'selected' : '' }}>
                                قيد الانتظار</option>
                            <option value="confirmed"
                                {{ old('booking_status', $booking->booking_status) == 'confirmed' ? 'selected' : '' }}>
                                مؤكد</option>
                            <option value="canceled"
                                {{ old('booking_status', $booking->booking_status) == 'canceled' ? 'selected' : '' }}>
                                ملغي</option>
                            <option value="completed"
                                {{ old('booking_status', $booking->booking_status) == 'completed' ? 'selected' : '' }}>
                                مكتمل</option>
                        </select>
                        @error('booking_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">تحديث الحجز</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const packageSelect = document.getElementById('package_id');
                const numberOfPeopleInput = document.getElementById('number_of_people');
                const totalPriceInput = document.getElementById('total_price');

                const isAgentUser = "{{ $user->role }}" === "agent";
                const customerAgentTypeSelect = document.getElementById('customer_agent_type');
                const customerSelectDiv = document.getElementById('customer_select');
                const agentSelectDiv = document.getElementById('agent_select');
                const customerSelect = document.getElementById('customer_id');

                function updatePrice() {
                    const selectedPackageOption = packageSelect.options[packageSelect.selectedIndex];
                    const people = parseInt(numberOfPeopleInput.value);

                    let price = 0;
                    if (selectedPackageOption && people > 0) {
                        if (isAgentUser) {
                            price = parseFloat(selectedPackageOption.dataset.agentPrice);
                        } else {
                            const selectedType = customerAgentTypeSelect ? customerAgentTypeSelect.value : null;
                            if (selectedType === 'customer') {
                                price = parseFloat(selectedPackageOption.dataset.price);
                            } else if (selectedType === 'agent') {
                                price = parseFloat(selectedPackageOption.dataset.agentPrice);
                            } else {
                                price = parseFloat(selectedPackageOption.dataset.price);
                            }
                        }
                        totalPriceInput.value = (price * people).toFixed(2);
                    } else {
                        totalPriceInput.value = '';
                    }
                }

                function toggleCustomerAgentFields() {
                    if (isAgentUser) {
                        customerSelectDiv.style.display = 'block';
                        customerSelect.setAttribute('required', 'required');
                    } else {
                        const selectedType = customerAgentTypeSelect.value;
                        if (selectedType === 'customer') {
                            customerSelectDiv.style.display = 'block';
                            agentSelectDiv.style.display = 'none';
                            customerSelect.setAttribute('required', 'required');
                            const agentActualSelect = document.getElementById('agent_id');
                            if (agentActualSelect) agentActualSelect.removeAttribute('required');
                        } else if (selectedType === 'agent') {
                            agentSelectDiv.style.display = 'block';
                            customerSelectDiv.style.display = 'none';
                            const agentActualSelect = document.getElementById('agent_id');
                            if (agentActualSelect) agentActualSelect.setAttribute('required', 'required');
                            customerSelect.removeAttribute('required');
                        } else {
                            customerSelectDiv.style.display = 'none';
                            agentSelectDiv.style.display = 'none';
                            customerSelect.removeAttribute('required');
                            const agentActualSelect = document.getElementById('agent_id');
                            if (agentActualSelect) agentActualSelect.removeAttribute('required');
                        }
                    }
                    updatePrice();
                }

                packageSelect.addEventListener('change', updatePrice);
                numberOfPeopleInput.addEventListener('input', updatePrice);

                if (!isAgentUser && customerAgentTypeSelect) {
                    customerAgentTypeSelect.addEventListener('change', toggleCustomerAgentFields);
                }

                // Initial calls on page load
                if (isAgentUser) {
                    customerSelect.setAttribute('required', 'required');
                } else if (customerAgentTypeSelect) {
                    // For edit screen, set initial selection based on booking data
                    if ("{{ old('customer_id', $booking->customer_id) }}") {
                        customerAgentTypeSelect.value = 'customer';
                    } else if ("{{ old('agent_id', $booking->agent_id) }}") {
                        customerAgentTypeSelect.value = 'agent';
                    }
                    toggleCustomerAgentFields();
                }
                updatePrice();
            });
        </script>
    @endpush
</x-admin-layout>
