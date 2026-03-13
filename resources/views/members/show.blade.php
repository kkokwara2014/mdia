@extends('layouts.app')

@section('title', 'Member Profile')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">{{ $member->name }}</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('members.edit', $member) }}" class="btn btn-primary">Edit Member</a>
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Back to Members</a>
        </div>
    </div>
</div>

<div class="row row-deck row-cards">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-auto">
                        @if($member->user_image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($member->user_image) }}" alt="{{ $member->name }}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;">
                        @else
                            <span class="avatar avatar-xl rounded bg-secondary">{{ strtoupper(substr($member->name, 0, 2)) }}</span>
                        @endif
                    </div>
                    <div class="col">
                        <h3 class="mb-1">{{ $member->name }}</h3>
                        <div class="text-secondary">{{ $member->email }}</div>
                        <div class="text-secondary">{{ $member->phone ?? '—' }}</div>
                    </div>
                    <div class="col-auto text-end">
                        @if($member->password)
                            <span class="badge bg-success-lt">Claimed</span>
                        @else
                            <span class="badge bg-yellow-lt">Unclaimed</span>
                        @endif
                        <div class="text-secondary mt-2 small">Member since {{ $member->created_at->format('M j, Y') }}</div>
                        <div class="mt-2">
                            @foreach($member->roles as $role)
                                <span class="badge bg-blue-lt">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Payment History</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Verified By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($member->payments as $payment)
                <tr>
                    <td>{{ $payment->paymentType->name ?? '—' }}</td>
                    <td>${{ number_format($payment->amount, 2) }}</td>
                    <td>{{ $payment->year }}</td>
                    <td>
                        @if($payment->status === 'verified')
                            <span class="badge bg-success-lt">Verified</span>
                        @else
                            <span class="badge bg-yellow-lt">Pending</span>
                        @endif
                    </td>
                    <td>{{ $payment->payment_date?->format('M j, Y') ?? '—' }}</td>
                    <td>{{ $payment->verifiedBy->name ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-secondary py-4">No payment records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
