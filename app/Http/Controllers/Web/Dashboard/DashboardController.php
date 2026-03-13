<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $data = [];

        $data['member_total_paid'] = Payment::where('user_id', $user->id)
            ->where('status', 'verified')
            ->sum('amount');

        $data['member_total_pending'] = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        $data['member_breakdown_by_payment_type'] = Payment::where('user_id', $user->id)
            ->select('payment_type_id', DB::raw('
                SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_total,
                SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_total,
                COUNT(*) as count
            '))
            ->groupBy('payment_type_id')
            ->with('paymentType')
            ->get();

        $data['member_recent_payments'] = Payment::where('user_id', $user->id)
            ->with('paymentType')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        if ($user->hasPermission('validate_payment')) {
            $memberRole = Role::where('name', 'Member')->first();
            $data['total_members'] = $memberRole ? $memberRole->users()->count() : 0;

            $data['total_verified_collections'] = Payment::where('status', 'verified')
                ->sum('amount');

            $data['total_pending_collections'] = Payment::where('status', 'pending')
                ->sum('amount');

            $data['breakdown_by_payment_type'] = PaymentType::leftJoin('payments', 'payment_types.id', '=', 'payments.payment_type_id')
                ->select('payment_types.id', 'payment_types.name', DB::raw('
                    SUM(CASE WHEN payments.status = "verified" THEN payments.amount ELSE 0 END) as verified_total,
                    SUM(CASE WHEN payments.status = "pending" THEN payments.amount ELSE 0 END) as pending_total,
                    COUNT(payments.id) as count
                '))
                ->groupBy('payment_types.id', 'payment_types.name')
                ->get();

            $data['recent_payments'] = Payment::with(['user', 'paymentType'])
                ->orderBy('payment_date', 'desc')
                ->limit(10)
                ->get();
        }

        return view('dashboard', $data);
    }
}
