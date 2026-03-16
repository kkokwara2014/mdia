<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\VerifyPaymentRequest;
use App\Models\Payment;
use App\Models\PaymentEvidence;
use App\Models\PaymentType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!$request->user()?->hasPermission('validate_payment')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to validate payments.',
                ], 403);
            }
            return $next($request);
        })->only(['index', 'show', 'store', 'verify']);
    }

    #[OA\Get(
        path: '/payments',
        summary: 'List all payments',
        description: 'Retrieve a list of all payments with optional filters. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Payments'],
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
                description: 'Payments retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payments retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payments',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'user_id', type: 'integer'),
                                            new OA\Property(property: 'payment_type_id', type: 'integer'),
                                            new OA\Property(property: 'amount', type: 'number', format: 'float'),
                                            new OA\Property(property: 'year', type: 'integer'),
                                            new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                                            new OA\Property(property: 'notes', type: 'string', nullable: true),
                                            new OA\Property(property: 'status', type: 'string'),
                                            new OA\Property(property: 'verified_by', type: 'integer', nullable: true),
                                            new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
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
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['user', 'paymentType', 'verifiedBy', 'evidences']);

        if ($request->has('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('payment_date', [$request->input('from'), $request->input('to')]);
        }

        $memberUuid = $request->input('member_uuid') ?? $request->input('user_uuid');
        if ($memberUuid) {
            $user = User::where('uuid', $memberUuid)->first();
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

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $paginator = $query->latest('payment_date')->paginate(15)->withQueryString();

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
            'message' => 'Payments retrieved successfully',
            'data' => [
                'payments' => $payments,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                ],
            ],
        ], 200);
    }

    #[OA\Get(
        path: '/payments/{payment}',
        summary: 'View a single payment',
        description: 'Retrieve details of a specific payment including evidence files. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Payments'],
        parameters: [
            new OA\Parameter(
                name: 'payment',
                in: 'path',
                description: 'Payment UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'user_id', type: 'integer'),
                                        new OA\Property(property: 'payment_type_id', type: 'integer'),
                                        new OA\Property(property: 'amount', type: 'number', format: 'float'),
                                        new OA\Property(property: 'year', type: 'integer'),
                                        new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                                        new OA\Property(property: 'notes', type: 'string', nullable: true),
                                        new OA\Property(property: 'status', type: 'string'),
                                        new OA\Property(property: 'verified_by', type: 'integer', nullable: true),
                                        new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
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
            new OA\Response(
                response: 404,
                description: 'Payment not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\Payment].'),
                    ]
                )
            ),
        ]
    )]
    public function show(Payment $payment): JsonResponse
    {
        $payment->load(['user', 'paymentType', 'verifiedBy', 'evidences']);

        return response()->json([
            'success' => true,
            'message' => 'Payment retrieved successfully',
            'data' => [
                'payment' => $payment,
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/payments',
        summary: 'Log a payment on behalf of a member',
        description: 'Create a payment directly on behalf of a member. Status is automatically set to verified. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Payments'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_uuid', 'payment_type_uuid', 'year', 'payment_date'],
                properties: [
                    new OA\Property(property: 'user_uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                    new OA\Property(property: 'payment_type_uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                    new OA\Property(property: 'year', type: 'integer', example: 2024),
                    new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2024-01-15'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Payment received in cash'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Payment logged successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment logged successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'user_id', type: 'integer'),
                                        new OA\Property(property: 'payment_type_id', type: 'integer'),
                                        new OA\Property(property: 'amount', type: 'number', format: 'float'),
                                        new OA\Property(property: 'year', type: 'integer'),
                                        new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                                        new OA\Property(property: 'notes', type: 'string', nullable: true),
                                        new OA\Property(property: 'status', type: 'string', example: 'verified'),
                                        new OA\Property(property: 'verified_by', type: 'integer'),
                                        new OA\Property(property: 'verified_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
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
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The user uuid field is required.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'user_uuid',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The selected user uuid is invalid.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(StorePaymentRequest $request): JsonResponse
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

        return response()->json([
            'success' => true,
            'message' => 'Payment logged successfully',
            'data' => [
                'payment' => $payment,
            ],
        ], 201);
    }

    #[OA\Put(
        path: '/payments/{payment}/verify',
        summary: 'Verify a pending payment',
        description: 'Verify a payment that was submitted by a member. Sets status to verified and records verification timestamp. Accessible by Admin, Super Admin, Treasurer, and Financial Secretary only.',
        security: [['bearerAuth' => []]],
        tags: ['Payments'],
        parameters: [
            new OA\Parameter(
                name: 'payment',
                in: 'path',
                description: 'Payment UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment verified successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment verified successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'user_id', type: 'integer'),
                                        new OA\Property(property: 'payment_type_id', type: 'integer'),
                                        new OA\Property(property: 'amount', type: 'number', format: 'float'),
                                        new OA\Property(property: 'year', type: 'integer'),
                                        new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                                        new OA\Property(property: 'notes', type: 'string', nullable: true),
                                        new OA\Property(property: 'status', type: 'string', example: 'verified'),
                                        new OA\Property(property: 'verified_by', type: 'integer'),
                                        new OA\Property(property: 'verified_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Payment already verified',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment has already been verified.'),
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
            new OA\Response(
                response: 404,
                description: 'Payment not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\Payment].'),
                    ]
                )
            ),
        ]
    )]
    public function verify(VerifyPaymentRequest $request, Payment $payment): JsonResponse
    {
        if ($payment->status === 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Payment has already been verified.',
            ], 400);
        }

        $payment->update([
            'status' => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment verified successfully',
            'data' => [
                'payment' => $payment->fresh(),
            ],
        ], 200);
    }

    #[OA\Get(
        path: '/payments/my',
        summary: 'View own payment history',
        description: 'Retrieve payment history for the authenticated member with optional filters',
        security: [['bearerAuth' => []]],
        tags: ['Payments'],
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
                name: 'payment_type_uuid',
                in: 'query',
                description: 'Filter payments by payment type UUID',
                required: false,
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payments retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payments retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payments',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'user_id', type: 'integer'),
                                            new OA\Property(property: 'payment_type_id', type: 'integer'),
                                            new OA\Property(property: 'amount', type: 'number', format: 'float'),
                                            new OA\Property(property: 'year', type: 'integer'),
                                            new OA\Property(property: 'payment_date', type: 'string', format: 'date'),
                                            new OA\Property(property: 'notes', type: 'string', nullable: true),
                                            new OA\Property(property: 'status', type: 'string'),
                                            new OA\Property(property: 'verified_by', type: 'integer', nullable: true),
                                            new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                            new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
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
    public function myPayments(Request $request): JsonResponse
    {
        $query = Payment::with(['paymentType', 'evidences'])
            ->where('user_id', $request->user()->id);

        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('payment_date', [$request->input('from'), $request->input('to')]);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('payment_type_uuid')) {
            $paymentType = PaymentType::where('uuid', $request->input('payment_type_uuid'))->first();
            if ($paymentType) {
                $query->where('payment_type_id', $paymentType->id);
            }
        }

        $paginator = $query->latest('payment_date')->paginate(15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Payments retrieved successfully',
            'data' => [
                'payments' => $paginator->items(),
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                ],
            ],
        ], 200);
    }
}
