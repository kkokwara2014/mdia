@extends('layouts.app')

@section('title', 'Payment Reports')

@section('css')
<style>
@media print {
    .navbar-vertical,
    .page-wrapper .navbar.navbar-expand-md,
    .d-print-none,
    #reportFilters,
    #downloadPdfBtn,
    #generateReportBtn,
    #clearReportBtn,
    #reportLoading {
        display: none !important;
    }
    .print-only-header {
        display: block !important;
        font-family: 'Space Grotesk', sans-serif;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #206bc4;
    }
    .print-only-header img {
        height: 40px;
        width: 40px;
        object-fit: cover;
        border-radius: 6px;
        vertical-align: middle;
        margin-right: 8px;
    }
    .print-only-header h1 {
        font-size: 1.5rem;
        margin: 0;
        display: inline-block;
        vertical-align: middle;
    }
    #reportCard {
        page-break-before: always;
    }
    .card {
        break-inside: avoid;
    }
    body, .card, .table {
        font-family: 'Space Grotesk', sans-serif;
    }
}
</style>
@endsection

@section('content')
<div class="print-only-header d-print-none" id="printReportHeader" style="display: none;">
    <img src="{{ asset('assets/logo_full.jpeg') }}" alt="MDIA">
    <h1>Payment Report</h1>
</div>

<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">Payment Reports</h2>
        </div>
        <div class="col-auto ms-auto">
            <button type="button" id="downloadPdfBtn" class="btn btn-primary">Download PDF</button>
        </div>
    </div>
</div>

<div id="reportFilters" class="row g-2 mb-3 d-print-none">
    <div class="col-auto">
        <input type="number"
               id="filterYear"
               class="form-control"
               placeholder="Year"
               min="1900">
    </div>
    <div class="col-auto">
        <input type="date" id="filterFrom" class="form-control" placeholder="From">
    </div>
    <div class="col-auto">
        <input type="date" id="filterTo" class="form-control" placeholder="To">
    </div>
    <div class="col-auto">
        <select id="filterPaymentType" class="form-select">
            <option value="">All Payment Types</option>
            @foreach($paymentTypes as $type)
                <option value="{{ $type->uuid }}">{{ $type->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <select id="filterStatus" class="form-select">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
        </select>
    </div>
    <div class="col-auto position-relative">
        <input type="text"
               id="filterMember"
               class="form-control"
               placeholder="Search member..."
               autocomplete="off">
        <input type="hidden" id="filterMemberUuid">
        <div id="filterMemberResults"
             class="list-group shadow-sm"
             style="position: absolute; top: 100%; left: 0; z-index: 9999; background: #ffffff; border: 1px solid #dee2e6; border-radius: 4px; max-height: 200px; overflow-y: auto; display: none; min-width: 250px;">
        </div>
    </div>
    <div class="col-auto">
        <button type="button" id="generateReportBtn" class="btn btn-primary">Generate Report</button>
    </div>
    <div class="col-auto">
        <button type="button" id="clearReportBtn" class="btn btn-outline-secondary">Clear</button>
    </div>
</div>

<div id="summaryCards" class="row row-deck row-cards mb-3">
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md bg-blue-lt text-blue me-3"><i class="ti ti-cash"></i></span>
                    <div>
                        <div class="text-secondary text-uppercase fw-bold small">Total Collections</div>
                        <div class="h2 mb-0" id="summaryTotalCollections">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md bg-green-lt text-green me-3"><i class="ti ti-circle-check"></i></span>
                    <div>
                        <div class="text-secondary text-uppercase fw-bold small">Total Verified</div>
                        <div class="h2 mb-0" id="summaryTotalVerified">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md bg-yellow-lt text-yellow me-3"><i class="ti ti-clock"></i></span>
                    <div>
                        <div class="text-secondary text-uppercase fw-bold small">Total Pending</div>
                        <div class="h2 mb-0" id="summaryTotalPending">$0.00</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-md bg-azure-lt text-azure me-3"><i class="ti ti-receipt"></i></span>
                    <div>
                        <div class="text-secondary text-uppercase fw-bold small">Total Payments</div>
                        <div class="h2 mb-0" id="summaryTotalPayments">0</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3" id="breakdownCard">
    <div class="card-header">
        <h3 class="card-title">Breakdown by Payment Type</h3>
    </div>
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
            <tbody id="breakdownTableBody">
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">No data found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card" id="reportCard">
    <div class="card-header">
        <h3 class="card-title">Payment Records</h3>
    </div>
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
                    <th>Verified By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody id="reportTableBody">
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No payments found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="reportLoading" class="text-center py-4 d-print-none" style="display: none;">
    <div class="spinner-border text-primary" role="status"></div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let memberSearchTimeout;

    function getFilters() {
        return {
            year: document.getElementById('filterYear').value,
            from: document.getElementById('filterFrom').value,
            to: document.getElementById('filterTo').value,
            payment_type_uuid: document.getElementById('filterPaymentType').value,
            status: document.getElementById('filterStatus').value,
            member_uuid: document.getElementById('filterMemberUuid').value,
        };
    }

    function formatAmount(n) {
        return '$' + parseFloat(n).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function escapeHtml(text) {
        if (text == null) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function loadReport() {
        var params = new URLSearchParams(getFilters());
        var loadingEl = document.getElementById('reportLoading');
        loadingEl.style.display = 'block';

        fetch('{{ route("reports.filter") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            loadingEl.style.display = 'none';
            if (!data.success) return;

            var summary = data.data.summary;
            document.getElementById('summaryTotalCollections').textContent = formatAmount(summary.total_collections);
            document.getElementById('summaryTotalVerified').textContent = formatAmount(summary.total_verified);
            document.getElementById('summaryTotalPending').textContent = formatAmount(summary.total_pending);
            document.getElementById('summaryTotalPayments').textContent = String(summary.total_payments);

            var breakdownBody = document.getElementById('breakdownTableBody');
            var breakdown = data.data.breakdown_by_payment_type;
            if (!breakdown || breakdown.length === 0) {
                breakdownBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No data found</td></tr>';
            } else {
                breakdownBody.innerHTML = breakdown.map(function (row) {
                    return '<tr><td>' + escapeHtml(row.name) + '</td><td>' + formatAmount(row.verified_total) + '</td><td>' + formatAmount(row.pending_total) + '</td><td>' + row.count + '</td></tr>';
                }).join('');
            }

            var reportBody = document.getElementById('reportTableBody');
            var payments = data.data.payments;
            if (!payments || payments.length === 0) {
                reportBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">No payments found</td></tr>';
            } else {
                reportBody.innerHTML = payments.map(function (p) {
                    var statusBadge = p.status === 'verified' ? '<span class="badge bg-success-lt">Verified</span>' : '<span class="badge bg-warning-lt">Pending</span>';
                    return '<tr><td>' + escapeHtml(p.member_name) + '</td><td>' + escapeHtml(p.payment_type_name) + '</td><td>' + formatAmount(p.amount) + '</td><td>' + escapeHtml(String(p.year)) + '</td><td>' + statusBadge + '</td><td>' + escapeHtml(p.payment_date) + '</td><td>' + escapeHtml(p.verified_by_name) + '</td><td>' + escapeHtml(p.notes) + '</td></tr>';
                }).join('');
            }
        })
        .catch(function () {
            loadingEl.style.display = 'none';
        });
    }

    document.getElementById('downloadPdfBtn').addEventListener('click', function () {
        var params = new URLSearchParams(getFilters());
        var base = '{{ route("reports.download-pdf") }}';
        window.location.href = params.toString() ? base + '?' + params.toString() : base;
    });

    document.getElementById('generateReportBtn').addEventListener('click', loadReport);

    document.getElementById('clearReportBtn').addEventListener('click', function () {
        document.getElementById('filterYear').value = '';
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterTo').value = '';
        document.getElementById('filterPaymentType').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterMember').value = '';
        document.getElementById('filterMemberUuid').value = '';
        loadReport();
    });

    document.getElementById('filterMember').addEventListener('input', function () {
        clearTimeout(memberSearchTimeout);
        var q = this.value.trim();
        var resultsDiv = document.getElementById('filterMemberResults');
        if (q.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }
        memberSearchTimeout = setTimeout(function () {
            fetch('{{ route("members.search") }}?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                resultsDiv.innerHTML = '';
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="list-group-item">No members found</div>';
                } else {
                    data.forEach(function (member) {
                        var item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action py-2';
                        item.textContent = member.name + ' (' + member.email + ')';
                        item.addEventListener('click', function (e) {
                            e.preventDefault();
                            document.getElementById('filterMemberUuid').value = member.uuid;
                            document.getElementById('filterMember').value = member.name;
                            resultsDiv.style.display = 'none';
                        });
                        resultsDiv.appendChild(item);
                    });
                }
                resultsDiv.style.display = 'block';
            });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        var resultsDiv = document.getElementById('filterMemberResults');
        var filterMember = document.getElementById('filterMember');
        if (!filterMember.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    loadReport();
});
</script>
@endsection
