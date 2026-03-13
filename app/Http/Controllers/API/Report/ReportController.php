<?php

namespace App\Http\Controllers\API\Report;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Get(
        path: '/reports/member',
        summary: 'View member payment report',
        description: 'Retrieve full payment history for the authenticated member structured for printing with optional year filter',
        security: [['bearerAuth' => []]],
        tags: ['Reports'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                in: 'query',
                description: 'Filter payments by year',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 2024)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member report retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member report retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'member',
                                    properties: [
                                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                                        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
                                        new OA\Property(property: 'phone', type: 'string', example: '1234567890'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'payments',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'payment_type', type: 'string', example: 'Annual Membership'),
                                            new OA\Property(property: 'amount', type: 'number', format: 'float', example: 100.00),
                                            new OA\Property(property: 'year', type: 'integer', example: 2024),
                                            new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2024-01-15'),
                                            new OA\Property(property: 'notes', type: 'string', nullable: true),
                                            new OA\Property(property: 'status', type: 'string', example: 'verified'),
                                            new OA\Property(property: 'verified_by_name', type: 'string', nullable: true, example: 'Admin User'),
                                            new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                            new OA\Property(
                                                property: 'evidence_files',
                                                type: 'array',
                                                items: new OA\Items(type: 'string', example: '/uploads/evidence/receipt.jpg')
                                            ),
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
    public function memberReport(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Payment::with(['paymentType', 'verifiedBy', 'evidences'])
            ->where('user_id', $user->id);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $paymentsData = $payments->map(function ($payment) {
            return [
                'uuid' => $payment->uuid,
                'payment_type' => $payment->paymentType->name,
                'amount' => (float) $payment->amount,
                'year' => $payment->year,
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'notes' => $payment->notes,
                'status' => $payment->status,
                'verified_by_name' => $payment->verifiedBy?->name,
                'verified_at' => $payment->verified_at?->toIso8601String(),
                'evidence_files' => $payment->evidences->pluck('file_path')->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Member report retrieved successfully',
            'data' => [
                'member' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'payments' => $paymentsData,
            ],
        ], 200);
    }

    #[OA\Get(
        path: '/reports/admin',
        summary: 'View admin payment report',
        description: 'Retrieve full payment report structured for printing with comprehensive filters. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Reports'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                in: 'query',
                description: 'Filter payments by year',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 2024)
            ),
            new OA\Parameter(
                name: 'from',
                in: 'query',
                description: 'Filter payments from date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2024-01-01')
            ),
            new OA\Parameter(
                name: 'to',
                in: 'query',
                description: 'Filter payments to date (YYYY-MM-DD)',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'date', example: '2024-12-31')
            ),
            new OA\Parameter(
                name: 'user_uuid',
                in: 'query',
                description: 'Filter payments by member UUID',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'payment_type_uuid',
                in: 'query',
                description: 'Filter payments by payment type UUID',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'status',
                in: 'query',
                description: 'Filter payments by status',
                required: false,
                schema: new OA\Schema(type: 'string', enum: ['pending', 'verified'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Admin report retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Admin report retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'summary',
                                    properties: [
                                        new OA\Property(property: 'total_collections', type: 'number', format: 'float', example: 45000.00),
                                        new OA\Property(property: 'total_pending', type: 'number', format: 'float', example: 5000.00),
                                        new OA\Property(property: 'total_verified', type: 'number', format: 'float', example: 45000.00),
                                        new OA\Property(property: 'total_members', type: 'integer', example: 150),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'payments',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'member_name', type: 'string', example: 'John Doe'),
                                            new OA\Property(property: 'payment_type', type: 'string', example: 'Annual Membership'),
                                            new OA\Property(property: 'amount', type: 'number', format: 'float', example: 100.00),
                                            new OA\Property(property: 'year', type: 'integer', example: 2024),
                                            new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2024-01-15'),
                                            new OA\Property(property: 'notes', type: 'string', nullable: true),
                                            new OA\Property(property: 'status', type: 'string', example: 'verified'),
                                            new OA\Property(property: 'verified_by_name', type: 'string', nullable: true, example: 'Admin User'),
                                            new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                            new OA\Property(
                                                property: 'evidence_files',
                                                type: 'array',
                                                items: new OA\Items(type: 'string', example: '/uploads/evidence/receipt.jpg')
                                            ),
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
    public function adminReport(Request $request): JsonResponse
    {
        $query = Payment::with(['user', 'paymentType', 'verifiedBy', 'evidences']);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('payment_date', [$request->input('from'), $request->input('to')]);
        }

        if ($request->has('user_uuid')) {
            $user = User::where('uuid', $request->input('user_uuid'))->first();
            if ($user) {
                $query->where('user_id', $user->id);
            }
        }

        if ($request->has('payment_type_uuid')) {
            $paymentType = PaymentType::where('uuid', $request->input('payment_type_uuid'))->first();
            if ($paymentType) {
                $query->where('payment_type_id', $paymentType->id);
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $totalCollections = $payments->where('status', 'verified')->sum('amount');
        $totalPending = $payments->where('status', 'pending')->sum('amount');
        $totalVerified = $totalCollections;
        $totalMembers = $payments->pluck('user_id')->unique()->count();

        $paymentsData = $payments->map(function ($payment) {
            return [
                'uuid' => $payment->uuid,
                'member_name' => $payment->user->name,
                'payment_type' => $payment->paymentType->name,
                'amount' => (float) $payment->amount,
                'year' => $payment->year,
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'notes' => $payment->notes,
                'status' => $payment->status,
                'verified_by_name' => $payment->verifiedBy?->name,
                'verified_at' => $payment->verified_at?->toIso8601String(),
                'evidence_files' => $payment->evidences->pluck('file_path')->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Admin report retrieved successfully',
            'data' => [
                'summary' => [
                    'total_collections' => (float) $totalCollections,
                    'total_pending' => (float) $totalPending,
                    'total_verified' => (float) $totalVerified,
                    'total_members' => $totalMembers,
                ],
                'payments' => $paymentsData,
            ],
        ], 200);
    }
}
