<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('تعديل الدفعة') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="mb-4">تعديل الدفعة: #{{ $payment->id }} للحجز
                    {{ $payment->booking->booking_code ?? 'غير معروف' }}</h3>

                <form action="{{ route('payments.update', $payment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="booking_id" class="form-label">الحجز <span class="text-danger">*</span></label>
                        <select class="form-select @error('booking_id') is-invalid @enderror" id="booking_id"
                            name="booking_id" required>
                            <option value="">اختر الحجز</option>
                            @foreach ($bookings as $booking)
                                <option value="{{ $booking->id }}"
                                    data-remaining-amount="{{ $booking->remaining_amount + ($booking->id == $payment->booking_id ? $payment->amount : 0) }}"
                                    {{ old('booking_id', $payment->booking_id) == $booking->id ? 'selected' : '' }}>
                                    {{ $booking->booking_code }} -
                                    @if ($booking->customer)
                                        {{ $booking->customer->first_name }} {{ $booking->customer->last_name }}
                                    @elseif($booking->agent)
                                        {{ $booking->agent->company_name }}
                                    @endif
                                    (متبقي:
                                    {{ number_format($booking->remaining_amount + ($booking->id == $payment->booking_id ? $payment->amount : 0), 2) }})
                                    {{-- Adjusted remaining amount for current payment --}}
                                </option>
                            @endforeach
                        </select>
                        @error('booking_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror"
                            id="amount" name="amount" value="{{ old('amount', $payment->amount) }}" required
                            min="0.01">
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted" id="remainingAmountHint"></small>
                    </div>

                    <div class="mb-3">
                        <label for="payment_date" class="form-label">تاريخ الدفع <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('payment_date') is-invalid @enderror"
                            id="payment_date" name="payment_date"
                            value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                        @error('payment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">طريقة الدفع <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('payment_method') is-invalid @enderror"
                            id="payment_method" name="payment_method"
                            value="{{ old('payment_method', $payment->payment_method) }}" required>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">معرف المعاملة</label>
                        <input type="text" class="form-control @error('transaction_id') is-invalid @enderror"
                            id="transaction_id" name="transaction_id"
                            value="{{ old('transaction_id', $payment->transaction_id) }}">
                        @error('transaction_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">تحديث الدفعة</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">إلغاء</a>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bookingSelect = document.getElementById('booking_id');
                const amountInput = document.getElementById('amount');
                const remainingAmountHint = document.getElementById('remainingAmountHint');

                function updateRemainingAmountHint() {
                    const selectedBookingOption = bookingSelect.options[bookingSelect.selectedIndex];
                    if (selectedBookingOption && selectedBookingOption.value) {
                        const remainingAmount = parseFloat(selectedBookingOption.dataset.remainingAmount);
                        remainingAmountHint.textContent = `المبلغ المتبقي للحجز: ${remainingAmount.toFixed(2)}`;
                        // Adjust max value based on the current payment amount plus remaining
                        const currentPaymentAmount = parseFloat(amountInput.value);
                        amountInput.max = remainingAmount + currentPaymentAmount;
                    } else {
                        remainingAmountHint.textContent = '';
                        amountInput.removeAttribute('max');
                    }
                }

                bookingSelect.addEventListener('change', updateRemainingAmountHint);

                // Initial call
                updateRemainingAmountHint();
            });
        </script>
    @endpush
</x-admin-layout>
