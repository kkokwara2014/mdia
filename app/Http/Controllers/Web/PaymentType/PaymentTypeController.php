<?php

namespace App\Http\Controllers\Web\PaymentType;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Models\PaymentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentTypeController extends Controller
{
    public function index(): View
    {
        $paymentTypes = PaymentType::orderBy('name')->get();

        return view('payment-types.index', compact('paymentTypes'));
    }

    public function create(): View
    {
        return view('payment-types.create');
    }

    public function store(StorePaymentTypeRequest $request): RedirectResponse
    {
        if (!auth()->user()->hasPermission('super_admin')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }
        PaymentType::create([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return redirect()->route('payment-types.index')->with('success', 'Payment type created successfully.');
    }

    public function edit(PaymentType $paymentType): View
    {
        return view('payment-types.edit', compact('paymentType'));
    }

    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType): RedirectResponse
    {
        if (!auth()->user()->hasPermission('super_admin')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }
        $paymentType->update([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return redirect()->route('payment-types.index')->with('success', 'Payment type updated successfully.');
    }

    public function destroy(PaymentType $paymentType): RedirectResponse
    {
        if (!auth()->user()->hasPermission('super_admin')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized.');
        }
        if ($paymentType->payments()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete payment type with existing payments.');
        }

        $paymentType->delete();

        return redirect()->route('payment-types.index')->with('success', 'Payment type deleted successfully.');
    }
}
