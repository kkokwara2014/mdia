@extends('layouts.app')

@section('title', 'Member Profile')

@section('content')
@if(isset($successMessage))
    <div class="alert alert-success alert-dismissible" role="alert">
        <div class="d-flex">
            <div>{{ $successMessage }}</div>
        </div>
        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
    </div>
@endif
<div class="page-header d-print-none mb-3">
    <div class="page-header-actions">
        <h2 class="page-title mb-3 mb-md-0">{{ $member->name }}</h2>
        <div class="page-header-buttons d-flex flex-column flex-md-row flex-md-wrap gap-2 justify-content-md-end">
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary order-first">Back to Members</a>
            <a href="{{ route('members.edit', ['user' => $member->uuid]) }}" class="btn btn-primary">Edit Member</a>
            @if(auth()->user()->hasPermission('super_admin') && !$member->roles->contains('name', 'Super Admin'))
            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#regenerateModal">
                Regenerate Password
            </button>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                Delete Member
            </button>
            @endif
        </div>
    </div>
</div>

<div class="member-show-content w-100" style="max-width: 100%; min-width: 0;">
        <div class="card-responsive-stack">
            <div class="card">
                <div class="card-body">
                    <div class="member-info-row">
                        <div class="member-info-avatar">
                            <img src="{{ $member->getAvatarUrl() }}" alt="{{ $member->name }}" style="width: var(--avatar-lg); height: var(--avatar-lg); object-fit: cover; border-radius: 50%;">
                        </div>
                        <div class="member-info-details">
                            <h3 class="mb-1">{{ $member->name }}</h3>
                            <div class="text-secondary">{{ $member->email }}</div>
                            <div class="text-secondary">{{ $member->phone ?? '—' }}</div>
                        </div>
                        <div class="member-info-meta">
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

            <div class="card">
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
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Delete Member?</div>
                <div>
                    This will permanently delete <strong>{{ $member->name }}</strong> and all their payment records. This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('members.destroy', ['user' => $member->uuid]) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-blur fade" id="regenerateModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Regenerate Password?</div>
                <div>
                    This will replace the current password for <strong>{{ $member->name }}</strong> with a new one.
                    The new password will be shown once.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('members.regenerate-password', ['user' => $member->uuid]) }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-warning">Yes, Regenerate</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(isset($generatedPassword))
<div class="modal modal-blur fade" id="passwordModal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Password Generated</h5>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning mb-3" role="alert">
                    <div class="d-flex">
                        <div>
                            <i class="ti ti-alert-triangle me-2"></i>
                        </div>
                        <div>
                            <strong>Important:</strong> This password will only be shown once. Please copy it now before closing this window.
                        </div>
                    </div>
                </div>
                <div class="mb-2 text-muted small">Member Email</div>
                <div class="mb-3 d-flex align-items-center gap-2">
                    <code class="flex-grow-1 p-2 bg-light rounded">{{ $member->email }}</code>
                    <button type="button" class="btn btn-icon btn-ghost-secondary" onclick="copyToClipboard('{{ $member->email }}', this)" title="Copy email">
                        <i class="ti ti-copy"></i>
                    </button>
                </div>
                <div class="mb-2 text-muted small">Generated Password</div>
                <div class="d-flex align-items-center gap-2">
                    <code id="passwordDisplay" class="flex-grow-1 p-2 bg-light rounded" style="font-family: monospace; font-size: var(--font-size-base); letter-spacing: 0.125em;">{{ $generatedPassword }}</code>
                    <button type="button" class="btn btn-icon btn-ghost-secondary" onclick="copyToClipboard('{{ $generatedPassword }}', this)" title="Copy password">
                        <i class="ti ti-copy"></i>
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I've Copied the Password</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('js')
<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = button.querySelector('i');
        const originalClass = icon.className;
        icon.className = 'ti ti-check';
        button.classList.add('text-success');
        setTimeout(() => {
            icon.className = originalClass;
            button.classList.remove('text-success');
        }, 2000);
    });
}

@if(isset($generatedPassword))
document.addEventListener('DOMContentLoaded', function() {
    const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
    passwordModal.show();
});
@endif
</script>
@endsection
