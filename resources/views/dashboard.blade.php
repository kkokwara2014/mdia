@extends('layouts.app')

@section('title', 'Dashboard')

@section('page_title', 'Dashboard')

@section('page_content')
    <div class="row">
        <div class="col-lg-6 col-6">
            <div class="small-box mdia-box-green">
                <div class="inner">
                    <h3>${{ number_format($member_total_paid, 2) }}</h3>
                    <p>My Total Paid</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-6">
            <div class="small-box mdia-box-gold">
                <div class="inner">
                    <h3>${{ number_format($member_total_pending, 2) }}</h3>
                    <p>My Pending Payments</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('validate_payment'))
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box mdia-box-green">
                <div class="inner">
                    <h3>{{ $total_members }}</h3>
                    <p>Total Members</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box mdia-box-red">
                <div class="inner">
                    <h3>${{ number_format($total_verified_collections, 2) }}</h3>
                    <p>Total Collections</p>
                </div>
                <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-6">
            <div class="small-box mdia-box-gold">
                <div class="inner">
                    <h3>${{ number_format($total_pending_collections, 2) }}</h3>
                    <p>Total Pending</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Payment Summary</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Verified Total</th>
                                <th>Pending Total</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($member_breakdown_by_payment_type as $breakdown)
                            <tr>
                                <td>{{ $breakdown->paymentType->name }}</td>
                                <td>${{ number_format($breakdown->verified_total, 2) }}</td>
                                <td>${{ number_format($breakdown->pending_total, 2) }}</td>
                                <td>{{ $breakdown->count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No payment types found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Recent Payments</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
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
                            @forelse($member_recent_payments as $payment)
                            <tr>
                                <td>{{ $payment->paymentType->name }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->year }}</td>
                                <td>
                                    @if($payment->status === 'verified')
                                        <span class="badge badge-success">Verified</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('validate_payment'))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Type Breakdown</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Payment Type</th>
                                <th>Verified Total</th>
                                <th>Pending Total</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($breakdown_by_payment_type as $breakdown)
                            <tr>
                                <td>{{ $breakdown->name }}</td>
                                <td>${{ number_format($breakdown->verified_total, 2) }}</td>
                                <td>${{ number_format($breakdown->pending_total, 2) }}</td>
                                <td>{{ $breakdown->count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No payment types found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Payments</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_payments as $payment)
                            <tr>
                                <td>{{ $payment->user->name }}</td>
                                <td>{{ $payment->paymentType->name }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->year }}</td>
                                <td>
                                    @if($payment->status === 'verified')
                                        <span class="badge badge-success">Verified</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No payments found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@stop
