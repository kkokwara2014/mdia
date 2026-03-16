@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Dashboard</h2>
        </div>
    </div>
</div>

<div class="row-cards stats-grid mb-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                <span class="avatar avatar-md bg-green-lt text-green">
                    <i class="ti ti-circle-check"></i>
                </span>
                <div>
                    <div class="text-secondary text-uppercase fw-bold small">My Total Paid</div>
                    <div class="h2 mb-0">${{ number_format($member_total_paid ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                <span class="avatar avatar-md bg-yellow-lt text-yellow">
                    <i class="ti ti-clock"></i>
                </span>
                <div>
                    <div class="text-secondary text-uppercase fw-bold small">My Pending Payments</div>
                    <div class="h2 mb-0">${{ number_format($member_total_pending ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @if(auth()->user()->hasPermission('validate_payment'))
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                <span class="avatar avatar-md bg-blue-lt text-blue">
                    <i class="ti ti-users"></i>
                </span>
                <div>
                    <div class="text-secondary text-uppercase fw-bold small">Total Members</div>
                    <div class="h2 mb-0">{{ $total_members ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                <span class="avatar avatar-md bg-green-lt text-green">
                    <i class="ti ti-cash"></i>
                </span>
                <div>
                    <div class="text-secondary text-uppercase fw-bold small">Total Collections</div>
                    <div class="h2 mb-0">${{ number_format($total_verified_collections ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center" style="gap: var(--spacing-sm);">
                <span class="avatar avatar-md bg-yellow-lt text-yellow">
                    <i class="ti ti-hourglass"></i>
                </span>
                <div>
                    <div class="text-secondary text-uppercase fw-bold small">Total Pending</div>
                    <div class="h2 mb-0">${{ number_format($total_pending_collections ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row row-deck row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">My Payment Summary</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Verified Total</th>
                                <th>Pending Total</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member_breakdown ?? [] as $row)
                            <tr>
                                <td>{{ $row->paymentType?->name ?? '—' }}</td>
                                <td>${{ number_format($row->verified_total ?? 0, 2) }}</td>
                                <td>${{ number_format($row->pending_total ?? 0, 2) }}</td>
                                <td>{{ $row->count ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">No payment records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">My Recent Payments</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member_recent_payments ?? [] as $payment)
                            <tr>
                                <td>{{ $payment->paymentType?->name ?? '—' }}</td>
                                <td>${{ number_format($payment->amount ?? 0, 2) }}</td>
                                <td>{{ $payment->year ?? '—' }}</td>
                                <td>
                                    @if(($payment->status ?? '') === 'verified')
                                    <span class="badge bg-success">Verified</span>
                                    @else
                                    <span class="badge bg-yellow">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-4">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('validate_payment'))
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment Type Breakdown</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Verified Total</th>
                                <th>Pending Total</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($breakdown_by_payment_type ?? [] as $row)
                            <tr>
                                <td>{{ $row->name ?? '—' }}</td>
                                <td>${{ number_format($row->verified_total ?? 0, 2) }}</td>
                                <td>${{ number_format($row->pending_total ?? 0, 2) }}</td>
                                <td>{{ $row->count ?? 0 }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-4">No payment types found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Payments</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter card-table table-striped">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_payments ?? [] as $payment)
                            <tr>
                                <td>{{ $payment->user?->name ?? '—' }}</td>
                                <td>{{ $payment->paymentType?->name ?? '—' }}</td>
                                <td>${{ number_format($payment->amount ?? 0, 2) }}</td>
                                <td>{{ $payment->year ?? '—' }}</td>
                                <td>
                                    @if(($payment->status ?? '') === 'verified')
                                    <span class="badge bg-success">Verified</span>
                                    @else
                                    <span class="badge bg-yellow">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date?->format('M j, Y') ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-secondary py-4">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
