<?php

namespace App\Http\Controllers\Web\Report;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('reports.index', compact('paymentTypes'));
    }

    public function filter(Request $request): JsonResponse
    {
        $payments = $this->getFilteredPayments($request);

        $totalVerified = (float) $payments->where('status', 'verified')->sum('amount');
        $totalPending = (float) $payments->where('status', 'pending')->sum('amount');
        $totalCollections = $totalVerified + $totalPending;
        $totalMembers = $payments->pluck('user_id')->unique()->filter()->count();

        $breakdown = $payments->groupBy('payment_type_id')->map(function ($group, $paymentTypeId) {
            $first = $group->first();
            $name = $first->paymentType?->name ?? '—';
            $verifiedTotal = (float) $group->where('status', 'verified')->sum('amount');
            $pendingTotal = (float) $group->where('status', 'pending')->sum('amount');

            return [
                'name' => $name,
                'verified_total' => round($verifiedTotal, 2),
                'pending_total' => round($pendingTotal, 2),
                'count' => $group->count(),
            ];
        })->values()->all();

        $paymentsData = $this->mapPaymentsToReportData($payments);

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_verified' => round($totalVerified, 2),
                    'total_pending' => round($totalPending, 2),
                    'total_collections' => round($totalCollections, 2),
                    'total_payments' => $payments->count(),
                    'total_members' => $totalMembers,
                ],
                'breakdown_by_payment_type' => $breakdown,
                'payments' => $paymentsData,
            ],
        ]);
    }

    public function downloadPdf(Request $request): Response
    {
        $payments = $this->getFilteredPayments($request);

        $totalVerified = (float) $payments->where('status', 'verified')->sum('amount');
        $totalPending = (float) $payments->where('status', 'pending')->sum('amount');
        $totalCollections = $totalVerified + $totalPending;

        $breakdown = $payments->groupBy('payment_type_id')->map(function ($group, $paymentTypeId) {
            $first = $group->first();
            $name = $first->paymentType?->name ?? '—';
            $verifiedTotal = (float) $group->where('status', 'verified')->sum('amount');
            $pendingTotal = (float) $group->where('status', 'pending')->sum('amount');

            return [
                'name' => $name,
                'verified_total' => round($verifiedTotal, 2),
                'pending_total' => round($pendingTotal, 2),
                'count' => $group->count(),
            ];
        })->values()->all();

        $paymentsData = $this->mapPaymentsToReportData($payments);

        $summary = [
            'total_verified' => round($totalVerified, 2),
            'total_pending' => round($totalPending, 2),
            'total_collections' => round($totalCollections, 2),
            'total_payments' => $payments->count(),
        ];

        $filters = [
            'year' => $request->input('year'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];

        $pdf = Pdf::loadView('reports.pdf', [
            'summary' => $summary,
            'breakdown' => $breakdown,
            'payments' => $paymentsData,
            'filters' => $filters,
        ]);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('mdia-report-' . now()->format('Y-m-d') . '.pdf');
    }

    private function getFilteredPayments(Request $request): Collection
    {
        $year = $request->input('year');
        $from = $request->input('from');
        $to = $request->input('to');
        $paymentTypeUuid = $request->input('payment_type_uuid');
        $status = $request->input('status');
        $memberUuid = $request->input('member_uuid');

        return Payment::with(['user', 'paymentType', 'verifiedBy'])
            ->when($year, fn ($q) => $q->where('year', $year))
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->when($paymentTypeUuid, fn ($q) => $q->whereHas('paymentType', fn ($q) => $q->where('uuid', $paymentTypeUuid)))
            ->when($memberUuid, fn ($q) => $q->whereHas('user', fn ($q) => $q->where('uuid', $memberUuid)))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('payment_date')
            ->get();
    }

    private function mapPaymentsToReportData(Collection $payments): array
    {
        return $payments->map(fn ($payment) => [
            'member_name' => $payment->user?->name ?? '-',
            'payment_type_name' => $payment->paymentType?->name ?? '-',
            'amount' => (float) $payment->amount,
            'year' => $payment->year,
            'payment_date' => $payment->payment_date?->format('M d, Y') ?? '-',
            'status' => $payment->status,
            'notes' => $payment->notes ?? '-',
            'verified_by_name' => $payment->verifiedBy?->name ?? null,
            'verified_at' => $payment->verified_at?->format('M d, Y h:i A') ?? null,
        ])->values()->all();
    }
}
