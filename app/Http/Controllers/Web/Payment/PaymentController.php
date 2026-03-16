<?php

namespace App\Http\Controllers\Web\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\SubmitPaymentRequest;
use App\Models\Payment;
use App\Models\PaymentEvidence;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('payments.index', compact('paymentTypes'));
    }

    public function filter(Request $request): JsonResponse
    {
        $query = Payment::with(['user', 'paymentType']);

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('payment_date', [$request->input('from'), $request->input('to')]);
        }

        if ($request->filled('payment_type_uuid')) {
            $paymentType = PaymentType::where('uuid', $request->input('payment_type_uuid'))->first();
            if ($paymentType) {
                $query->where('payment_type_id', $paymentType->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('member_uuid')) {
            $user = User::where('uuid', $request->input('member_uuid'))->first();
            if ($user) {
                $query->where('user_id', $user->id);
            }
        }

        $paginator = $query->latest('payment_date')->paginate(15);

        $payments = $paginator->getCollection()->map(function ($payment) {
            return [
                'uuid' => $payment->uuid,
                'member_name' => $payment->user?->name ?? '—',
                'payment_type_name' => $payment->paymentType?->name ?? '—',
                'amount' => number_format((float) $payment->amount, 2, '.', ''),
                'year' => $payment->year,
                'status' => $payment->status,
                'payment_date' => $payment->payment_date->format('M d, Y'),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                ],
            ],
        ]);
    }

    public function create(): View
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('payments.create', compact('paymentTypes'));
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $user = User::where('uuid', $request->user_uuid)->firstOrFail();
        $paymentType = PaymentType::where('uuid', $request->payment_type_uuid)->firstOrFail();

        $payment = Payment::create([
            'user_id' => $user->id,
            'payment_type_id' => $paymentType->id,
            'amount' => $paymentType->amount,
            'year' => $request->year,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        if ($request->hasFile('evidence_files')) {
            foreach ($request->file('evidence_files') as $file) {
                $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('evidence', $filename, 'public');
                PaymentEvidence::create([
                    'payment_id' => $payment->id,
                    'file_path' => $path,
                ]);
            }
        }

        return redirect()->route('payments.index')->with('success', 'Payment logged successfully.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['user', 'paymentType', 'verifiedBy', 'evidences']);

        return view('payments.show', compact('payment'));
    }

    public function verify(Payment $payment): RedirectResponse
    {
        if ($payment->status === 'verified') {
            return redirect()->back()->with('error', 'Payment has already been verified.');
        }

        $payment->update([
            'status' => 'verified',
            'verified_by' => request()->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()->route('payments.show', $payment)->with('success', 'Payment verified successfully.');
    }

    public function myPayments(Request $request): View
    {
        $query = Payment::query()
            ->where('user_id', auth()->id())
            ->with(['paymentType', 'verifiedBy', 'evidences']);

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $payments = $query->latest('payment_date')->paginate(15);

        return view('payments.my-payments', compact('payments'));
    }

    public function submitPayment(): View
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('payments.submit', compact('paymentTypes'));
    }

    public function storeSubmitPayment(SubmitPaymentRequest $request): RedirectResponse
    {
        $paymentType = PaymentType::where('uuid', $request->payment_type_uuid)->firstOrFail();

        $payment = Payment::create([
            'user_id' => auth()->id(),
            'payment_type_id' => $paymentType->id,
            'amount' => $paymentType->amount,
            'year' => $request->year,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        foreach ($request->file('evidence_files', []) as $file) {
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('evidence', $filename, 'public');
            PaymentEvidence::create([
                'payment_id' => $payment->id,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('payments.my-payments')->with('success', 'Payment submitted successfully. It will be reviewed shortly.');
    }
}
