<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitPaymentRequest;
use App\Models\Payment;
use App\Models\PaymentEvidence;
use App\Models\PaymentType;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class MemberPaymentController extends Controller
{
    #[OA\Post(
        path: '/payments/submit',
        summary: 'Submit a payment with evidence',
        description: 'Submit a payment on behalf of authenticated member with evidence files. Status is automatically set to pending until verified.',
        security: [['bearerAuth' => []]],
        tags: ['Member Payments'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['payment_type_uuid', 'year', 'payment_date', 'evidence_files'],
                properties: [
                    new OA\Property(property: 'payment_type_uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                    new OA\Property(property: 'year', type: 'integer', example: 2024),
                    new OA\Property(property: 'payment_date', type: 'string', format: 'date', example: '2024-01-15'),
                    new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Payment via bank transfer'),
                    new OA\Property(
                        property: 'evidence_files',
                        type: 'array',
                        items: new OA\Items(type: 'string', example: '/uploads/evidence/receipt_123.jpg'),
                        minItems: 1
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Payment submitted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Payment submitted successfully'),
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
                                        new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                        new OA\Property(property: 'verified_by', type: 'integer', nullable: true),
                                        new OA\Property(property: 'verified_at', type: 'string', format: 'date-time', nullable: true),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                        new OA\Property(
                                            property: 'evidences',
                                            type: 'array',
                                            items: new OA\Items(
                                                properties: [
                                                    new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                                    new OA\Property(property: 'payment_id', type: 'integer'),
                                                    new OA\Property(property: 'file_path', type: 'string'),
                                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                                ],
                                                type: 'object'
                                            )
                                        ),
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
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The payment type uuid field is required.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'payment_type_uuid',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The selected payment type uuid is invalid.')
                                ),
                                new OA\Property(
                                    property: 'evidence_files',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The evidence files field is required.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function submit(SubmitPaymentRequest $request): JsonResponse
    {
        $paymentType = PaymentType::where('uuid', $request->payment_type_uuid)->firstOrFail();

        $payment = Payment::create([
            'user_id' => $request->user()->id,
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

        $payment->load('evidences');

        return response()->json([
            'success' => true,
            'message' => 'Payment submitted successfully',
            'data' => [
                'payment' => $payment,
            ],
        ], 201);
    }
}
