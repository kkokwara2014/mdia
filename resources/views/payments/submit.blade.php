@extends('layouts.app')

@section('title', 'Submit Payment')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Submit Payment</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('payments.my-payments') }}" class="btn btn-outline-secondary">Back to My Payments</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <div class="d-flex">
                    <div>
                        <h4 class="alert-title">Please fix the following errors</h4>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('payments.submit.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label required">Payment Type</label>
                <select name="payment_type_uuid" id="payment_type_select" class="form-select @error('payment_type_uuid') is-invalid @enderror" required>
                    <option value="">Select a payment type</option>
                    @foreach($paymentTypes as $pt)
                        <option value="{{ $pt->uuid }}" data-amount="{{ $pt->amount }}" {{ old('payment_type_uuid') === $pt->uuid ? 'selected' : '' }}>{{ $pt->name }} — ${{ number_format($pt->amount, 2) }}</option>
                    @endforeach
                </select>
                @error('payment_type_uuid')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Amount</label>
                <input type="text" id="amount_display" class="form-control" readonly placeholder="Select a payment type" value="{{ old('amount_display') }}">
            </div>
            <div class="mb-3">
                <label class="form-label required">Year</label>
                <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', date('Y')) }}" min="1900" max="{{ date('Y') }}" required>
                @error('year')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Payment Date</label>
                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label required">Payment Evidence</label>
                <input type="file"
                       class="form-control"
                       name="evidence_files[]"
                       multiple
                       accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf">
                <small class="text-muted">You can upload multiple files. Formats: JPG, PNG, WEBP, PDF. Max 2MB each.</small>
            </div>
            <button type="submit" class="btn btn-primary">Submit Payment</button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const paymentTypes = @json($paymentTypes->mapWithKeys(fn($pt) => [$pt->uuid => $pt->amount]));
    const paymentTypeSelect = document.getElementById('payment_type_select');
    const amountDisplay = document.getElementById('amount_display');

    paymentTypeSelect.addEventListener('change', function () {
        const uuid = this.value;
        if (uuid && paymentTypes[uuid] != null) {
            amountDisplay.value = '$' + Number(paymentTypes[uuid]).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            amountDisplay.value = '';
        }
    });

    if (paymentTypeSelect.value) {
        paymentTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection
