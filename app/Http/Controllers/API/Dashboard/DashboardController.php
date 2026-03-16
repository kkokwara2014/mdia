<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    #[OA\Get(
        path: '/dashboard/member',
        summary: 'View member statistics',
        description: 'Retrieve payment statistics for the authenticated member including totals and breakdowns by payment type and year',
        security: [['bearerAuth' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member statistics retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member statistics retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'total_paid', type: 'number', format: 'float', example: 500.00),
                                new OA\Property(property: 'total_pending', type: 'number', format: 'float', example: 100.00),
                                new OA\Property(property: 'total_payments', type: 'integer', example: 10),
                                new OA\Property(
                                    property: 'breakdown_by_payment_type',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'payment_type', type: 'string', example: 'Annual Membership'),
                                            new OA\Property(property: 'verified_total', type: 'number', format: 'float', example: 200.00),
                                            new OA\Property(property: 'pending_total', type: 'number', format: 'float', example: 50.00),
                                            new OA\Property(property: 'count', type: 'integer', example: 5),
                                        ],
                                        type: 'object'
                                    )
                                ),
                                new OA\Property(
                                    property: 'breakdown_by_year',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'year', type: 'integer', example: 2024),
                                            new OA\Property(property: 'verified_total', type: 'number', format: 'float', example: 300.00),
                                            new OA\Property(property: 'pending_total', type: 'number', format: 'float', example: 75.00),
                                            new OA\Property(property: 'count', type: 'integer', example: 7),
                                        ],
                                        type: 'object'
                                    )
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function memberStats(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $totalPaid = Payment::where('user_id', $userId)
            ->where('status', 'verified')
            ->sum('amount');

        $totalPending = Payment::where('user_id', $userId)
            ->where('status', 'pending')
            ->sum('amount');

        $totalPayments = Payment::where('user_id', $userId)->count();

        $breakdownByPaymentType = Payment::select(
            'payment_types.name as payment_type',
            DB::raw('SUM(CASE WHEN payments.status = "verified" THEN payments.amount ELSE 0 END) as verified_total'),
            DB::raw('SUM(CASE WHEN payments.status = "pending" THEN payments.amount ELSE 0 END) as pending_total'),
            DB::raw('COUNT(*) as count')
        )
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->where('payments.user_id', $userId)
            ->groupBy('payment_types.id', 'payment_types.name')
            ->get();

        $breakdownByYear = Payment::select(
            'year',
            DB::raw('SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_total'),
            DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('user_id', $userId)
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Member statistics retrieved successfully',
            'data' => [
                'total_paid' => (float) $totalPaid,
                'total_pending' => (float) $totalPending,
                'total_payments' => $totalPayments,
                'breakdown_by_payment_type' => $breakdownByPaymentType,
                'breakdown_by_year' => $breakdownByYear,
            ],
        ], 200);
    }

    #[OA\Get(
        path: '/dashboard/admin',
        summary: 'View admin statistics',
        description: 'Retrieve comprehensive payment statistics for administrators including member counts, totals, and detailed breakdowns. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Dashboard'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                in: 'query',
                description: 'Filter statistics by year',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 2024)
            ),
            new OA\Parameter(
                name: 'from',
                in: 'query',
                description: 'Filter statistics from date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2024-01-01')
            ),
            new OA\Parameter(
                name: 'to',
                in: 'query',
                description: 'Filter statistics to date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2024-12-31')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Admin statistics retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Admin statistics retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'total_members', type: 'integer', example: 150),
                                new OA\Property(property: 'total_verified_payments', type: 'number', format: 'float', example: 45000.00),
                                new OA\Property(property: 'total_pending_payments', type: 'number', format: 'float', example: 5000.00),
                                new OA\Property(property: 'total_collections', type: 'number', format: 'float', example: 45000.00),
                                new OA\Property(
                                    property: 'breakdown_by_payment_type',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'payment_type', type: 'string', example: 'Annual Membership'),
                                            new OA\Property(property: 'verified_total', type: 'number', format: 'float', example: 20000.00),
                                            new OA\Property(property: 'pending_total', type: 'number', format: 'float', example: 2000.00),
                                            new OA\Property(property: 'count', type: 'integer', example: 100),
                                        ],
                                        type: 'object'
                                    )
                                ),
                                new OA\Property(
                                    property: 'breakdown_by_year',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'year', type: 'integer', example: 2024),
                                            new OA\Property(property: 'verified_total', type: 'number', format: 'float', example: 30000.00),
                                            new OA\Property(property: 'pending_total', type: 'number', format: 'float', example: 3000.00),
                                            new OA\Property(property: 'count', type: 'integer', example: 200),
                                        ],
                                        type: 'object'
                                    )
                                ),
                                new OA\Property(
                                    property: 'per_member_summary',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'member_uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'member_name', type: 'string', example: 'John Doe'),
                                            new OA\Property(property: 'verified_total', type: 'number', format: 'float', example: 500.00),
                                            new OA\Property(property: 'pending_total', type: 'number', format: 'float', example: 100.00),
                                            new OA\Property(property: 'payment_count', type: 'integer', example: 5),
                                        ],
                                        type: 'object'
                                    )
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. You do not have permission to validate payments.'),
                    ]
                )
            ),
        ]
    )]
    public function adminStats(Request $request): JsonResponse
    {
        if (!$request->user()?->hasPermission('validate_payment')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You do not have permission to view admin statistics.',
            ], 403);
        }

        $memberRole = Role::where('name', 'Member')->first();
        $totalMembers = $memberRole ? $memberRole->users()->count() : 0;

        $query = Payment::query();

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('payment_date', [$request->input('from'), $request->input('to')]);
        }

        $totalVerifiedPayments = (clone $query)->where('status', 'verified')->sum('amount');
        $totalPendingPayments = (clone $query)->where('status', 'pending')->sum('amount');
        $totalCollections = $totalVerifiedPayments;

        $breakdownByPaymentType = (clone $query)
            ->select(
                'payment_types.name as payment_type',
                DB::raw('SUM(CASE WHEN payments.status = "verified" THEN payments.amount ELSE 0 END) as verified_total'),
                DB::raw('SUM(CASE WHEN payments.status = "pending" THEN payments.amount ELSE 0 END) as pending_total'),
                DB::raw('COUNT(*) as count')
            )
            ->join('payment_types', 'payments.payment_type_id', '=', 'payment_types.id')
            ->groupBy('payment_types.id', 'payment_types.name')
            ->get();

        $breakdownByYear = (clone $query)
            ->select(
                'year',
                DB::raw('SUM(CASE WHEN status = "verified" THEN amount ELSE 0 END) as verified_total'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN amount ELSE 0 END) as pending_total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        $perMemberSummary = (clone $query)
            ->select(
                'users.uuid as member_uuid',
                'users.name as member_name',
                DB::raw('SUM(CASE WHEN payments.status = "verified" THEN payments.amount ELSE 0 END) as verified_total'),
                DB::raw('SUM(CASE WHEN payments.status = "pending" THEN payments.amount ELSE 0 END) as pending_total'),
                DB::raw('COUNT(*) as payment_count')
            )
            ->join('users', 'payments.user_id', '=', 'users.id')
            ->groupBy('users.id', 'users.uuid', 'users.name')
            ->orderBy('users.name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Admin statistics retrieved successfully',
            'data' => [
                'total_members' => $totalMembers,
                'total_verified_payments' => (float) $totalVerifiedPayments,
                'total_pending_payments' => (float) $totalPendingPayments,
                'total_collections' => (float) $totalCollections,
                'breakdown_by_payment_type' => $breakdownByPaymentType,
                'breakdown_by_year' => $breakdownByYear,
                'per_member_summary' => $perMemberSummary,
            ],
        ], 200);
    }
}
