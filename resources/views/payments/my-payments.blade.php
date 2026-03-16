@extends('layouts.app')

@section('title', 'My Payments')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">My Payments</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('payments.submit') }}" class="btn btn-primary">Submit Payment</a>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('payments.my-payments') }}" class="row g-2 mb-3">
    <div class="col-auto">
        <input type="number"
               name="year"
               class="form-control"
               placeholder="Year e.g. {{ date('Y') }}"
               min="1900"
               value="{{ request('year') }}">
    </div>
    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">All Statuses</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-primary">Filter</button>
    </div>
    <div class="col-auto">
        <a href="{{ route('payments.my-payments') }}" class="btn btn-outline-secondary">Clear</a>
    </div>
</form>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $index => $payment)
                    <tr>
                        <td>{{ $payments->firstItem() + $index }}</td>
                        <td>{{ $payment->paymentType->name ?? '—' }}</td>
                        <td>${{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $payment->year }}</td>
                        <td>
                            @if($payment->status === 'verified')
                                <span class="badge bg-success-lt">Verified</span>
                            @else
                                <span class="badge bg-warning-lt">Pending</span>
                            @endif
                        </td>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No payments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($payments->hasPages())
    <div class="mt-3">
        {{ $payments->withQueryString()->links() }}
    </div>
@endif
@endsection
