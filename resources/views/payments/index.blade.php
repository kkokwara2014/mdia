@extends('layouts.app')

@section('title', 'Payments')

@section('content')
<div class="page-header d-print-none mb-3">
    <div class="row g-2 align-items-center">
        <div class="col">
            <h2 class="page-title">All Payments</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('payments.create') }}" class="btn btn-primary">Add Payment</a>
        </div>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-auto">
        <select id="filterYear" class="form-select">
            <option value="">All years</option>
            @php
                $yr = $yearRange ?? ['min' => 1900, 'max' => (int) date('Y') + 1];
                for ($y = $yr['max']; $y >= $yr['min']; $y--) {
                    echo "<option value=\"{$y}\">{$y}</option>";
                }
            @endphp
        </select>
    </div>
    <div class="col-auto">
        <input type="date"
               id="filterFrom"
               class="form-control"
               placeholder="From date">
    </div>
    <div class="col-auto">
        <input type="date"
               id="filterTo"
               class="form-control"
               placeholder="To date">
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
             style="position: absolute; z-index: 9999; background: #ffffff; border: 1px solid #dee2e6; border-radius: 4px; max-height: clamp(200px, 40vh, 300px); overflow-y: auto; display: none; min-width: clamp(200px, 50vw, 350px);">
        </div>
    </div>
    <div class="col-auto">
        <button id="clearFilters" type="button" class="btn btn-outline-secondary">Clear</button>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter card-table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Member Name</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Verified By</th>
                    <th class="w-1">Actions</th>
                </tr>
            </thead>
            <tbody id="paymentsTableBody">
                <tr id="loadingRow" style="display: none;">
                    <td colspan="9" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="paymentsPagination" class="mt-3"></div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let memberSearchTimeout;
    let filterTimeout;

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

    function loadPayments(page) {
        if (page == null) page = 1;
        const filters = getFilters();
        const params = new URLSearchParams(filters);
        params.set('page', String(page));

        const tbody = document.getElementById('paymentsTableBody');
        const loadingRow = document.getElementById('loadingRow');

        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        tbody.appendChild(loadingRow);
        loadingRow.style.display = 'table-row';

        fetch('{{ route("payments.filter") }}?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            loadingRow.style.display = 'none';
            if (!data.success) return;

            const payments = data.data.payments;
            if (payments.length === 0) {
                const empty = document.createElement('tr');
                empty.innerHTML = '<td colspan="9" class="text-center text-muted py-4">No payments found</td>';
                tbody.appendChild(empty);
            } else {
                const startIndex = (page - 1) * 15;
                payments.forEach(function (p, i) {
                    const statusBadge = p.status === 'verified'
                        ? '<span class="badge bg-success-lt">Verified</span>'
                        : '<span class="badge bg-warning-lt">Pending</span>';
                    const verifiedBy = p.verified_by_name || '-';
                    const row = document.createElement('tr');
                    row.innerHTML = '<td>' + (startIndex + i + 1) + '</td>' +
                        '<td>' + escapeHtml(p.member_name) + '</td>' +
                        '<td>' + escapeHtml(p.payment_type_name) + '</td>' +
                        '<td>$' + parseFloat(p.amount).toLocaleString('en-US', { minimumFractionDigits: 2 }) + '</td>' +
                        '<td>' + escapeHtml(String(p.year)) + '</td>' +
                        '<td>' + statusBadge + '</td>' +
                        '<td>' + escapeHtml(p.payment_date) + '</td>' +
                        '<td>' + escapeHtml(verifiedBy) + '</td>' +
                        '<td><a href="{{ url("/payments") }}/' + escapeHtml(p.uuid) + '" class="btn btn-sm btn-ghost-primary" title="View">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none">' +
                        '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>' +
                        '<circle cx="12" cy="12" r="2"/>' +
                        '<path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7"/>' +
                        '</svg></a></td>';
                    tbody.appendChild(row);
                });
            }
            tbody.appendChild(loadingRow);

            renderPagination(data.data.pagination, page);
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function renderPagination(pagination, currentPage) {
        const container = document.getElementById('paymentsPagination');
        container.innerHTML = '';
        if (pagination.last_page <= 1) return;

        let html = '<ul class="pagination">';
        for (let i = 1; i <= pagination.last_page; i++) {
            html += '<li class="page-item' + (i === currentPage ? ' active' : '') + '">' +
                '<a href="#" class="page-link" data-page="' + i + '">' + i + '</a></li>';
        }
        html += '</ul>';
        container.innerHTML = html;

        container.querySelectorAll('.page-link').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                loadPayments(parseInt(this.getAttribute('data-page'), 10));
            });
        });
    }

    ['filterYear', 'filterFrom', 'filterTo', 'filterPaymentType', 'filterStatus'].forEach(function (id) {
        document.getElementById(id).addEventListener('change', function () {
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(function () { loadPayments(1); }, 300);
        });
    });

    document.getElementById('filterMember').addEventListener('input', function () {
        clearTimeout(memberSearchTimeout);
        const q = this.value.trim();
        const resultsDiv = document.getElementById('filterMemberResults');
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
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action py-2';
                        item.textContent = member.name + ' (' + member.email + ')';
                        item.addEventListener('click', function (e) {
                            e.preventDefault();
                            document.getElementById('filterMemberUuid').value = member.uuid;
                            document.getElementById('filterMember').value = member.name;
                            resultsDiv.style.display = 'none';
                            loadPayments(1);
                        });
                        resultsDiv.appendChild(item);
                    });
                }
                resultsDiv.style.display = 'block';
            });
        }, 300);
    });

    document.getElementById('clearFilters').addEventListener('click', function () {
        document.getElementById('filterYear').value = '';
        document.getElementById('filterFrom').value = '';
        document.getElementById('filterTo').value = '';
        document.getElementById('filterPaymentType').value = '';
        document.getElementById('filterStatus').value = '';
        document.getElementById('filterMember').value = '';
        document.getElementById('filterMemberUuid').value = '';
        loadPayments(1);
    });

    document.addEventListener('click', function (e) {
        const resultsDiv = document.getElementById('filterMemberResults');
        const filterMember = document.getElementById('filterMember');
        if (!filterMember.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });

    loadPayments(1);
});
</script>
@endsection
