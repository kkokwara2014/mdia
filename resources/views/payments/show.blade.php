@extends('layouts.app')

@section('title', 'Payment Details')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Payment Details</h2>
        </div>
        <div class="col-auto ms-auto">
            @if($payment->status === 'pending')
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#verifyModal">
                Verify Payment
            </button>
            @endif
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Back to Payments</a>
        </div>
    </div>
</div>

<div class="row row-deck row-cards">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Payment</h3>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Member Name</div>
                    <div class="col-8">{{ $payment->user->name ?? '—' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Payment Type</div>
                    <div class="col-8">{{ $payment->paymentType->name ?? '—' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Amount</div>
                    <div class="col-8">${{ number_format($payment->amount, 2) }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Year</div>
                    <div class="col-8">{{ $payment->year }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Payment Date</div>
                    <div class="col-8">{{ $payment->payment_date->format('M j, Y') }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Status</div>
                    <div class="col-8">
                        @if($payment->status === 'verified')
                            <span class="badge bg-success-lt">Verified</span>
                        @else
                            <span class="badge bg-yellow-lt">Pending</span>
                        @endif
                    </div>
                </div>
                @if($payment->notes)
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Notes</div>
                    <div class="col-8">{{ $payment->notes }}</div>
                </div>
                @endif
                @if($payment->status === 'verified')
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Verified By</div>
                    <div class="col-8">{{ $payment->verifiedBy->name ?? '—' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4 text-secondary">Verified At</div>
                    <div class="col-8">{{ $payment->verified_at?->format('M j, Y g:i A') ?? '—' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Payment Evidence</h3>
    </div>
    <div class="card-body">
        @forelse($payment->evidences as $evidence)
            @php
                $url = \Illuminate\Support\Facades\Storage::url($evidence->file_path);
                $ext = strtolower(pathinfo($evidence->file_path, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
            @endphp
            <div class="mb-2">
                @if($isImage)
                    <a href="{{ $url }}" target="_blank" rel="noopener">
                        <img src="{{ $url }}" alt="Evidence" class="rounded border" style="max-width: clamp(150px, 50vw, 300px); object-fit: contain;">
                    </a>
                @else
                    <a href="{{ $url }}" target="_blank" rel="noopener" download class="btn btn-outline-primary btn-sm">
                        <i class="ti ti-download"></i> {{ basename($evidence->file_path) }}
                    </a>
                @endif
            </div>
        @empty
            <p class="text-secondary mb-0">No evidence files uploaded</p>
        @endforelse
    </div>
</div>

<div class="modal modal-blur fade" id="verifyModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Verify Payment</div>
                <div>Are you sure you want to verify this payment for <strong>{{ $payment->user->name ?? 'this member' }}</strong>? This action cannot be undone.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('payments.verify', $payment) }}" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-primary">Verify Payment</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
