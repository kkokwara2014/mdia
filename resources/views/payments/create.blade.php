@extends('layouts.app')

@section('title', 'Add Payment')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Add Payment</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Back to Payments</a>
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

        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3 position-relative" id="memberSearchWrapper">
                <label class="form-label">Member <span class="text-danger">*</span></label>
                <input type="text"
                       id="memberSearch"
                       class="form-control @error('user_uuid') is-invalid @enderror"
                       placeholder="Search member by name or email..."
                       autocomplete="off">
                <div id="memberSearchResults"
                     class="list-group shadow-sm"
                     style="position: absolute; top: 100%; left: 0; right: 0; z-index: 9999; background: #ffffff; border: 1px solid #dee2e6; border-radius: 4px; max-height: clamp(200px, 40vh, 300px); overflow-y: auto; display: none;">
                </div>
                <input type="hidden"
                       id="selectedMemberUuid"
                       name="user_uuid"
                       value="{{ old('user_uuid') }}">
                <div id="selectedMemberDisplay" class="mt-2" style="display: none;">
                    <span class="badge bg-blue-lt px-2 py-1" id="selectedMemberName" style="font-size: var(--font-size-sm);"></span>
                    <a href="#" id="clearMember" class="text-danger ms-2" style="font-size: var(--font-size-xs);">Clear</a>
                </div>
                @error('user_uuid')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
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
                <select name="year" class="form-select @error('year') is-invalid @enderror" required>
                    <option value="">Select year</option>
                    @php
                        $yr = $yearRange ?? ['min' => 1900, 'max' => (int) date('Y') + 1];
                        for ($y = $yr['max']; $y >= $yr['min']; $y--) {
                            $sel = old('year', date('Y')) == $y ? 'selected' : '';
                            echo "<option value=\"{$y}\" {$sel}>{$y}</option>";
                        }
                    @endphp
                </select>
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
                <label class="form-label">Payment Evidence (optional)</label>
                <input type="file"
                       class="form-control"
                       name="evidence_files[]"
                       multiple
                       accept="image/jpeg,image/png,image/jpg,image/webp,application/pdf">
                <small class="text-muted">You can upload multiple files. Formats: JPG, PNG, WEBP, PDF. Max 2MB each.</small>
            </div>
            <button type="submit" class="btn btn-primary">Add Payment</button>
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
    const searchInput = document.getElementById('memberSearch');
    const resultsDiv = document.getElementById('memberSearchResults');
    const hiddenInput = document.getElementById('selectedMemberUuid');
    const selectedDisplay = document.getElementById('selectedMemberDisplay');
    const selectedName = document.getElementById('selectedMemberName');
    const clearBtn = document.getElementById('clearMember');
    const memberSearchWrapper = document.getElementById('memberSearchWrapper');
    let searchTimeout;

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

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(function () {
            fetch('{{ route("members.search") }}?q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    resultsDiv.innerHTML = '';
                    if (data.length === 0) {
                        const empty = document.createElement('div');
                        empty.className = 'list-group-item py-2';
                        empty.style.background = '#ffffff';
                        empty.style.borderBottom = '1px solid #f0f0f0';
                        empty.textContent = 'No members found';
                        resultsDiv.appendChild(empty);
                    } else {
                        data.forEach(function (member) {
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action py-2';
                            item.style.background = '#ffffff';
                            item.style.borderBottom = '1px solid #f0f0f0';
                            item.textContent = member.name + ' (' + member.email + ')';
                            item.addEventListener('click', function (e) {
                                e.preventDefault();
                                hiddenInput.value = member.uuid;
                                selectedName.textContent = member.name;
                                selectedDisplay.style.display = 'block';
                                searchInput.style.display = 'none';
                                resultsDiv.style.display = 'none';
                            });
                            resultsDiv.appendChild(item);
                        });
                    }
                    resultsDiv.style.display = 'block';
                });
        }, 300);
    });

    clearBtn.addEventListener('click', function (e) {
        e.preventDefault();
        hiddenInput.value = '';
        selectedName.textContent = '';
        selectedDisplay.style.display = 'none';
        searchInput.style.display = 'block';
        searchInput.value = '';
    });

    document.addEventListener('click', function (e) {
        if (memberSearchWrapper && !memberSearchWrapper.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>
@endsection
