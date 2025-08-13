<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('إضافة حجز جديد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">إضافة حجز جديد</h3>

                <form action="{{ route('bookings.store') }}" method="POST">
                    @csrf

                    {{-- حقل اختيار الباقة --}}
                    <div class="mb-3">
                        <label for="package_id" class="form-label">الباقة <span class="text-danger">*</span></label>
                        <select class="form-select @error('package_id') is-invalid @enderror" id="package_id" name="package_id" required>
                            <option value="">اختر الباقة</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" data-price="{{ $package->price_per_person }}" data-agent-price="{{ $package->agent_price_per_person }}" {{ old('package_id') == $package->id ? 'selected' : '' }}>
                                    {{ $package->name }} ({{ number_format($package->price_per_person, 2) }} / {{ number_format($package->agent_price_per_person, 2) }} - مقاعد متاحة: {{ $package->available_seats }})
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
                        <input type="hidden" name="agent_id" value="{{ Auth::id() }}"> {{-- تعيين الوكيل الخارجي تلقائياً --}}
                        <input type="hidden" id="customer_agent_type_hidden" value="customer"> {{-- لإعلام JS أن النوع هو عميل --}}

                        <div class="mb-3" id="customer_select">
                            <label for="customer_id" class="form-label">العميل <span class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">اختر العميل</option>
                                @foreach($customers as $customer) {{-- هذه القائمة مفلترة مسبقاً في الكنترولر --}}
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->phone_number }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        {{-- للأدوار الأخرى (admin, reservation_agent, branch_manager, accountant) --}}
                        <div class="mb-3">
                            <label for="customer_agent_type" class="form-label">نوع العميل <span class="text-danger">*</span></label>
                            <select class="form-select" id="customer_agent_type" required>
                                <option value="">اختر نوع العميل</option>
                                <option value="customer" {{ old('customer_id') ? 'selected' : '' }}>عميل مباشر</option>
                                <option value="agent" {{ old('agent_id') ? 'selected' : '' }}>وكيل</option>
                            </select>
                        </div>

                        <div class="mb-3" id="customer_select" style="display: {{ old('customer_id') ? 'block' : 'none' }};">
                            <label for="customer_id" class="form-label">العميل</label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                <option value="">اختر العميل</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->first_name }} {{ $customer->last_name }} ({{ $customer->phone_number }})</option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3" id="agent_select" style="display: {{ old('agent_id') ? 'block' : 'none' }};">
                            <label for="agent_id" class="form-label">الوكيل</label>
                            <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id" name="agent_id">
                                <option value="">اختر الوكيل</option>
                                @foreach($agents as $agent_obj) {{-- غيّر $agent إلى $agent_obj لتجنب تضارب الأسماء --}}
                                    <option value="{{ $agent_obj->id }}" {{ old('agent_id') == $agent_obj->id ? 'selected' : '' }}>{{ $agent_obj->company_name }} ({{ $agent_obj->contact_person }})</option>
                                @endforeach
                            </select>
                            @error('agent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    {{-- ... بقية حقول النموذج (عدد الأشخاص، المبلغ الكلي، المدفوع، الحالة، الملاحظات) ... --}}
                    <div class="mb-3">
                        <label for="number_of_people" class="form-label">عدد الأشخاص <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('number_of_people') is-invalid @enderror" id="number_of_people" name="number_of_people" value="{{ old('number_of_people', 1) }}" required min="1">
                        @error('number_of_people')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="total_price" class="form-label">المبلغ الكلي <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('total_price') is-invalid @enderror" id="total_price" name="total_price" value="{{ old('total_price') }}" required min="0">
                        @error('total_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="paid_amount" class="form-label">المبلغ المدفوع</label>
                        <input type="number" step="0.01" class="form-control @error('paid_amount') is-invalid @enderror" id="paid_amount" name="paid_amount" value="{{ old('paid_amount', 0) }}" min="0">
                        @error('paid_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="booking_status" class="form-label">حالة الحجز <span class="text-danger">*</span></label>
                        <select class="form-select @error('booking_status') is-invalid @enderror" id="booking_status" name="booking_status" required>
                            <option value="pending" {{ old('booking_status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ old('booking_status') == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="canceled" {{ old('booking_status') == 'canceled' ? 'selected' : '' }}>ملغي</option>
                            <option value="completed" {{ old('booking_status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                        </select>
                        @error('booking_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ الحجز</button>
                    <a href="{{ route('bookings.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        // JavaScript for dynamic price calculation and customer/agent selection
        document.addEventListener('DOMContentLoaded', function() {
            const packageSelect = document.getElementById('package_id');
            const numberOfPeopleInput = document.getElementById('number_of_people');
            const totalPriceInput = document.getElementById('total_price');

            // Determine if current user is an 'agent' for simplified logic
            const isAgentUser = "{{ $user->role }}" === "agent";
            const customerAgentTypeSelect = document.getElementById('customer_agent_type'); // May not exist for agent role
            const customerSelectDiv = document.getElementById('customer_select');
            const agentSelectDiv = document.getElementById('agent_select'); // May not exist for agent role
            const customerSelect = document.getElementById('customer_id');

            function updatePrice() {
                const selectedPackageOption = packageSelect.options[packageSelect.selectedIndex];
                const people = parseInt(numberOfPeopleInput.value);

                let price = 0;
                if (selectedPackageOption && people > 0) {
                    if (isAgentUser) {
                        price = parseFloat(selectedPackageOption.dataset.agentPrice); // For agent role, use agent_price
                    } else {
                        const selectedType = customerAgentTypeSelect ? customerAgentTypeSelect.value : null; // Get type for non-agent
                        if (selectedType === 'customer') {
                            price = parseFloat(selectedPackageOption.dataset.price);
                        } else if (selectedType === 'agent') {
                            price = parseFloat(selectedPackageOption.dataset.agentPrice);
                        } else {
                            price = parseFloat(selectedPackageOption.dataset.price); // Default for direct customer
                        }
                    }
                    totalPriceInput.value = (price * people).toFixed(2);
                } else {
                    totalPriceInput.value = '';
                }
            }

            function toggleCustomerAgentFields() {
                if (isAgentUser) {
                    // Agent user always works with customers
                    customerSelectDiv.style.display = 'block';
                    customerSelect.setAttribute('required', 'required');
                    // agent_select_div is hidden by Blade, agent_id is via hidden input
                } else {
                    const selectedType = customerAgentTypeSelect.value;
                    if (selectedType === 'customer') {
                        customerSelectDiv.style.display = 'block';
                        agentSelectDiv.style.display = 'none';
                        customerSelect.setAttribute('required', 'required');
                        // agentSelect.removeAttribute('required'); // Will be handled by the other part
                    } else if (selectedType === 'agent') {
                        agentSelectDiv.style.display = 'block';
                        customerSelectDiv.style.display = 'none';
                        // agentSelect.setAttribute('required', 'required'); // Will be handled by the other part
                        customerSelect.removeAttribute('required');
                    } else {
                        customerSelectDiv.style.display = 'none';
                        agentSelectDiv.style.display = 'none';
                        customerSelect.removeAttribute('required');
                        // agentSelect.removeAttribute('required'); // Will be handled by the other part
                    }
                }
                updatePrice();
            }

            packageSelect.addEventListener('change', updatePrice);
            numberOfPeopleInput.addEventListener('input', updatePrice);

            if (!isAgentUser && customerAgentTypeSelect) {
                customerAgentTypeSelect.addEventListener('change', toggleCustomerAgentFields);
            }

            // Initial calls
            toggleCustomerAgentFields();
            updatePrice();
        });
    </script>
    @endpush
</x-admin-layout>
