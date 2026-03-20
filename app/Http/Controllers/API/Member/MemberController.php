<?php

namespace App\Http\Controllers\API\Member;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class MemberController extends Controller
{
    #[OA\Get(
        path: '/members',
        summary: 'List all members',
        description: 'Retrieve a paginated list of members. Search by name, email, or phone.',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                description: 'Search members by name, email, or phone',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'John')
            ),
            new OA\Parameter(
                name: 'page',
                in: 'query',
                description: 'Page number for pagination',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
            new OA\Parameter(
                name: 'per_page',
                in: 'query',
                description: 'Items per page (default 15)',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 15)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Members retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Members retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'members',
                                    type: 'array',
                                    items: new OA\Items(
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
                                    )
                                ),
                                new OA\Property(
                                    property: 'pagination',
                                    properties: [
                                        new OA\Property(property: 'current_page', type: 'integer'),
                                        new OA\Property(property: 'last_page', type: 'integer'),
                                        new OA\Property(property: 'total', type: 'integer'),
                                        new OA\Property(property: 'per_page', type: 'integer'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Admin access required.'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->with('roles');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->latest()->paginate(15)->withQueryString();

        return response()->json([
            'success' => true,
            'message' => 'Members retrieved successfully',
            'data' => [
                'members' => $paginator->items(),
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
        path: '/members/search',
        summary: 'Search members for autocomplete',
        description: 'Returns up to 10 members matching by name or email. Requires at least 2 characters in query.',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                description: 'Search query (min 2 characters; returns empty if omitted or shorter)',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'john')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'members',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'name', type: 'string'),
                                            new OA\Property(property: 'email', type: 'string'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Admin access required.'),
                    ]
                )
            ),
        ]
    )]
    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');
        if (strlen($q) < 2) {
            return response()->json([
                'success' => true,
                'data' => ['members' => []],
            ], 200);
        }

        $users = User::query()
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get(['uuid', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => ['members' => $users],
        ], 200);
    }

    #[OA\Get(
        path: '/members/{uuid}',
        summary: 'View a single member profile',
        description: 'Retrieve details of a specific member by UUID',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                description: 'Member UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'member',
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
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Member not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\User].'),
                    ]
                )
            ),
        ]
    )]
    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]);

        return response()->json([
            'success' => true,
            'message' => 'Member retrieved successfully',
            'data' => [
                'member' => $user,
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/members',
        summary: 'Create a new member',
        description: 'Create a new member with auto-generated password',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Jane Doe'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane@example.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 10, example: '1234567890'),
                    new OA\Property(property: 'user_image', type: 'string', nullable: true, example: 'https://example.com/image.jpg'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Member created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member created successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'member',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'name', type: 'string'),
                                        new OA\Property(property: 'email', type: 'string'),
                                        new OA\Property(property: 'phone', type: 'string'),
                                        new OA\Property(property: 'user_image', type: 'string', nullable: true),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The email has already been taken.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $userImage = null;
        if ($request->hasFile('user_image')) {
            $file = $request->file('user_image');
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $userImage = $file->storeAs('members', $filename, 'public');
        }

        $uppercase = chr(rand(65, 90));
        $lowercase = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4);
        $numbers = substr(str_shuffle('23456789'), 0, 2);
        $special = ['@', '#', '$', '!'][rand(0, 3)];
        $plainPassword = str_shuffle($uppercase . $lowercase . $numbers . $special);

        $member = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($plainPassword),
            'user_image' => $userImage,
        ]);

        $memberRole = Role::firstOrCreate(['name' => 'Member']);
        if ($request->has('roles') && count($request->roles) > 0) {
            $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
            $member->roles()->sync($roleIds->merge([$memberRole->id])->unique()->values()->all());
        } else {
            $member->roles()->sync([$memberRole->id]);
        }

        $member->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]);

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully',
            'data' => [
                'member' => $member,
                'generated_password' => $plainPassword,
            ],
        ], 201);
    }

    #[OA\Put(
        path: '/members/{uuid}',
        summary: 'Update a member profile',
        description: 'Update the profile information of a specific member',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                description: 'Member UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'Jane Doe Updated'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'jane.updated@example.com'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 10, example: '0987654321'),
                    new OA\Property(property: 'user_image', type: 'string', nullable: true, example: 'https://example.com/new-image.jpg'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member updated successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'member',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'name', type: 'string'),
                                        new OA\Property(property: 'email', type: 'string'),
                                        new OA\Property(property: 'phone', type: 'string'),
                                        new OA\Property(property: 'user_image', type: 'string', nullable: true),
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
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthorized. Admin access required.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Member not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\User].'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The email has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'email',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The email has already been taken.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(UpdateMemberRequest $request, User $user): JsonResponse
    {
        $targetIsSuperAdmin = $user->roles->contains('name', 'Super Admin');
        if ($targetIsSuperAdmin && !$request->user()->hasPermission('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Super Admin can modify a Super Admin.',
            ], 403);
        }

        $userImage = $user->user_image;
        if ($request->hasFile('user_image')) {
            if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
                Storage::disk('public')->delete($user->user_image);
            }
            $file = $request->file('user_image');
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $userImage = $file->storeAs('members', $filename, 'public');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_image' => $userImage,
        ]);

        if ($request->user()->hasPermission('super_admin')) {
            $memberRole = Role::firstOrCreate(['name' => 'Member']);
            if ($request->has('roles') && count($request->roles ?? []) > 0) {
                $roleIds = Role::whereIn('uuid', $request->roles)->pluck('id');
                $user->roles()->sync($roleIds->merge([$memberRole->id])->unique()->values()->all());
            } else {
                $user->roles()->sync([$memberRole->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully',
            'data' => [
                'member' => $user->fresh()->load('roles'),
            ],
        ], 200);
    }

    #[OA\Delete(
        path: '/members/{uuid}',
        summary: 'Delete a member',
        description: 'Permanently delete a member. Super Admin only. Cannot delete Super Admin users.',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                description: 'Member UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Member deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Member deleted successfully'),
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
                description: 'Forbidden - Super Admin only or cannot delete Super Admin',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Cannot delete a Super Admin.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Member not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\User].'),
                    ]
                )
            ),
        ]
    )]
    public function destroy(User $user): JsonResponse
    {
        if ($user->roles->contains('name', 'Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a Super Admin.',
            ], 403);
        }

        $user->verifiedPayments()->update(['verified_by' => null]);

        if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
            Storage::disk('public')->delete($user->user_image);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully',
        ], 200);
    }

    #[OA\Post(
        path: '/members/{uuid}/regenerate-password',
        summary: 'Regenerate member password',
        description: 'Generate a new random password for a member. Super Admin only. Cannot regenerate for Super Admin users.',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'path',
                description: 'Member UUID',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Password regenerated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Password regenerated successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(property: 'member', type: 'object', description: 'Updated member with roles and payments'),
                                new OA\Property(property: 'generated_password', type: 'string', example: 'Ab3x@', description: 'The new plain-text password'),
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
                description: 'Forbidden - Super Admin only or cannot regenerate for Super Admin',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Cannot regenerate password for Super Admin from dashboard.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Member not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No query results for model [App\\Models\\User].'),
                    ]
                )
            ),
        ]
    )]
    public function regeneratePassword(User $user): JsonResponse
    {
        if ($user->roles->contains('name', 'Super Admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot regenerate password for Super Admin from dashboard.',
            ], 403);
        }

        $uppercase = chr(rand(65, 90));
        $lowercase = substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4);
        $numbers = substr(str_shuffle('23456789'), 0, 2);
        $special = ['@', '#', '$', '!'][rand(0, 3)];
        $plainPassword = str_shuffle($uppercase . $lowercase . $numbers . $special);

        $user->update([
            'password' => Hash::make($plainPassword),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password regenerated successfully',
            'data' => [
                'member' => $user->fresh()->load(['roles', 'payments' => fn ($q) => $q->with(['paymentType', 'verifiedBy'])->orderBy('payment_date', 'desc')]),
                'generated_password' => $plainPassword,
            ],
        ], 200);
    }

    #[OA\Put(
        path: '/profile',
        summary: 'Update own profile',
        description: 'Allows an authenticated member to update their own profile',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'phone'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 255, example: 'John Doe Updated'),
                    new OA\Property(property: 'phone', type: 'string', maxLength: 10, example: '1234567890'),
                    new OA\Property(property: 'user_image', type: 'string', nullable: true, example: 'https://example.com/image.jpg'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Profile updated successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'user',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                        new OA\Property(property: 'name', type: 'string'),
                                        new OA\Property(property: 'email', type: 'string'),
                                        new OA\Property(property: 'phone', type: 'string'),
                                        new OA\Property(property: 'user_image', type: 'string', nullable: true),
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
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The phone has already been taken.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'phone',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The phone has already been taken.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->hasFile('user_image')) {
            if ($user->user_image && Storage::disk('public')->exists($user->user_image)) {
                Storage::disk('public')->delete($user->user_image);
            }
            $file = $request->file('user_image');
            $filename = \Illuminate\Support\Str::random(40) . '.' . $file->getClientOriginalExtension();
            $data['user_image'] = $file->storeAs('members', $filename, 'public');
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'user' => $user->fresh(),
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/profile/change-password',
        summary: 'Change own password',
        description: 'Change the authenticated user password. Requires current password for verification.',
        security: [['bearerAuth' => []]],
        tags: ['Members'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['current_password', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'current_password', type: 'string', format: 'password', example: 'oldpassword123'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, example: 'newpassword123'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'newpassword123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Password changed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Password changed successfully.'),
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
                description: 'Current password incorrect or validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Current password is incorrect.'),
                    ]
                )
            ),
        ]
    )]
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        if (!Hash::check($request->current_password, $request->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ], 200);
    }
}
