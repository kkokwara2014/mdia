<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimAccountRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class ClaimAccountController extends Controller
{
    #[OA\Post(
        path: '/auth/claim',
        summary: 'Claim account',
        description: 'Allows a member to claim their account by providing email or phone and setting a password',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['identifier', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'identifier', type: 'string', description: 'Email or phone number', example: 'john@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'password123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'password123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Account claimed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Account claimed successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'name', type: 'string'),
                                        new OA\Property(property: 'email', type: 'string'),
                                        new OA\Property(property: 'phone', type: 'string', nullable: true),
                                        new OA\Property(property: 'user_image', type: 'string', nullable: true),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(property: 'token', type: 'string', example: '3|abcdef123456...'),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Account already claimed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Account has already been claimed.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Member not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'No member found with the provided email or phone.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The password field confirmation does not match.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'password',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The password field confirmation does not match.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function claim(ClaimAccountRequest $request): JsonResponse
    {
        $identifier = $request->identifier;

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No member found with the provided email or phone.',
            ], 404);
        }

        if ($user->password !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Account has already been claimed.',
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $memberRole = Role::firstOrCreate(
            ['name' => 'Member'],
            ['can_validate_payment' => false]
        );

        if (!$user->roles()->where('role_id', $memberRole->id)->exists()) {
            $user->roles()->attach($memberRole);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Account claimed successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 200);
    }
}
