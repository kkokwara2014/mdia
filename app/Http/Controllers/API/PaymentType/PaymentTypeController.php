<?php

namespace App\Http\Controllers\API\PaymentType;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Models\PaymentType;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class PaymentTypeController extends Controller
{
    #[OA\Get(
        path: '/payment-types',
        summary: 'List all payment types',
        description: 'Retrieves a list of all available payment types in the system',
        security: [['bearerAuth' => []]],
        tags: ['Payment Types'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment types retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment types retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment_types',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                                            new OA\Property(property: 'name', type: 'string', example: 'Annual Membership'),
                                            new OA\Property(property: 'amount', type: 'number', format: 'float', example: 100.00),
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
    public function index(): JsonResponse
    {
        $paymentTypes = PaymentType::all();

        return response()->json([
            'success' => true,
            'message' => 'Payment types retrieved successfully',
            'data' => [
                'payment_types' => $paymentTypes,
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/payment-types',
        summary: 'Create a new payment type',
        description: 'Creates a new payment type in the system',
        security: [['bearerAuth' => []]],
        tags: ['Payment Types'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'amount'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Annual Membership'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 100.00),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Payment type created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment type created successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment_type',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Annual Membership'),
                                        new OA\Property(property: 'amount', type: 'number', format: 'float', example: 100.00),
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
                description: 'Forbidden - Super Admin access required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Super Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The name has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'name',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The name has already been taken.')
                                ),
                                new OA\Property(
                                    property: 'amount',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The amount must be at least 0.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(StorePaymentTypeRequest $request): JsonResponse
    {
        $paymentType = PaymentType::create([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment type created successfully',
            'data' => [
                'payment_type' => $paymentType,
            ],
        ], 201);
    }

    #[OA\Put(
        path: '/payment-types/{paymentType}',
        summary: 'Update a payment type',
        description: 'Updates an existing payment type',
        security: [['bearerAuth' => []]],
        tags: ['Payment Types'],
        parameters: [
            new OA\Parameter(
                name: 'paymentType',
                in: 'path',
                required: true,
                description: 'Payment Type UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'amount'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Annual Membership'),
                    new OA\Property(property: 'amount', type: 'number', format: 'float', example: 150.00),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment type updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment type updated successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'payment_type',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Annual Membership'),
                                        new OA\Property(property: 'amount', type: 'number', format: 'float', example: 150.00),
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
                description: 'Forbidden - Super Admin access required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Super Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Payment type not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment type not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The name has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'name',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The name has already been taken.')
                                ),
                                new OA\Property(
                                    property: 'amount',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The amount must be at least 0.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(UpdatePaymentTypeRequest $request, PaymentType $paymentType): JsonResponse
    {
        $paymentType->update([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment type updated successfully',
            'data' => [
                'payment_type' => $paymentType,
            ],
        ], 200);
    }

    #[OA\Delete(
        path: '/payment-types/{paymentType}',
        summary: 'Delete a payment type',
        description: 'Deletes a payment type from the system',
        security: [['bearerAuth' => []]],
        tags: ['Payment Types'],
        parameters: [
            new OA\Parameter(
                name: 'paymentType',
                in: 'path',
                required: true,
                description: 'Payment Type UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment type deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment type deleted successfully'),
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
                description: 'Forbidden - Super Admin access required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Super Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Payment type not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment type not found'),
                    ]
                )
            ),
        ]
    )]
    public function destroy(PaymentType $paymentType): JsonResponse
    {
        $paymentType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment type deleted successfully',
        ], 200);
    }
}
