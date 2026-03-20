<?php

namespace App\Http\Controllers\API\Permission;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignPermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class RolePermissionController extends Controller
{
    #[OA\Get(
        path: '/roles/{role}/permissions',
        summary: 'List all permissions for a role',
        description: 'Retrieves all permissions assigned to a specific role',
        security: [['bearerAuth' => []]],
        tags: ['Role Permissions'],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                description: 'Role UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Role permissions retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Role permissions retrieved successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'role',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Super Admin'),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'permissions',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '8c5d9a21-2b6a-3e8f-7a0b-1c2d3e4f5a6b'),
                                            new OA\Property(property: 'name', type: 'string', example: 'validate_payment'),
                                            new OA\Property(property: 'description', type: 'string', example: 'Allows user to validate member payments'),
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
                description: 'Role not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Role not found'),
                    ]
                )
            ),
        ]
    )]
    public function index(Role $role): JsonResponse
    {
        $permissions = $role->permissions;

        return response()->json([
            'success' => true,
            'message' => 'Role permissions retrieved successfully',
            'data' => [
                'role' => $role,
                'permissions' => $permissions,
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/roles/{role}/permissions/assign',
        summary: 'Assign a permission to a role',
        description: 'Assigns a specific permission to a role',
        security: [['bearerAuth' => []]],
        tags: ['Role Permissions'],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                description: 'Role UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['permission_uuid'],
                properties: [
                    new OA\Property(property: 'permission_uuid', type: 'string', format: 'uuid', example: '8c5d9a21-2b6a-3e8f-7a0b-1c2d3e4f5a6b'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permission assigned successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Permission assigned to role successfully'),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'role',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c'),
                                        new OA\Property(property: 'name', type: 'string', example: 'Super Admin'),
                                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                                    ],
                                    type: 'object'
                                ),
                                new OA\Property(
                                    property: 'permission',
                                    properties: [
                                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '8c5d9a21-2b6a-3e8f-7a0b-1c2d3e4f5a6b'),
                                        new OA\Property(property: 'name', type: 'string', example: 'validate_payment'),
                                        new OA\Property(property: 'description', type: 'string', example: 'Allows user to validate member payments'),
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
                description: 'Role not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Role not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The selected permission uuid is invalid.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'permission_uuid',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The selected permission uuid is invalid.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function assign(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        $permission = Permission::where('uuid', $request->permission_uuid)->firstOrFail();

        if ($role->permissions()->where('permission_id', $permission->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Permission is already assigned to this role',
            ], 422);
        }

        $role->permissions()->attach($permission->id);

        return response()->json([
            'success' => true,
            'message' => 'Permission assigned to role successfully',
            'data' => [
                'role' => $role,
                'permission' => $permission,
            ],
        ], 200);
    }

    #[OA\Delete(
        path: '/roles/{role}/permissions/revoke',
        summary: 'Revoke a permission from a role',
        description: 'Removes a specific permission from a role',
        security: [['bearerAuth' => []]],
        tags: ['Role Permissions'],
        parameters: [
            new OA\Parameter(
                name: 'role',
                in: 'path',
                required: true,
                description: 'Role UUID',
                schema: new OA\Schema(type: 'string', format: 'uuid', example: '9d4e8b32-3c7a-4f9e-8b1a-2d3e4f5a6b7c')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['permission_uuid'],
                properties: [
                    new OA\Property(property: 'permission_uuid', type: 'string', format: 'uuid', example: '8c5d9a21-2b6a-3e8f-7a0b-1c2d3e4f5a6b'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Permission revoked successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Permission revoked from role successfully'),
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
                description: 'Role or Permission not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Role not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The selected permission uuid is invalid.'),
                        new OA\Property(
                            property: 'errors',
                            properties: [
                                new OA\Property(
                                    property: 'permission_uuid',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The selected permission uuid is invalid.')
                                ),
                            ],
                            type: 'object'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function revoke(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        $permission = Permission::where('uuid', $request->permission_uuid)->firstOrFail();

        if (!$role->permissions()->where('permission_id', $permission->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Permission is not assigned to this role',
            ], 422);
        }

        $role->permissions()->detach($permission->id);

        return response()->json([
            'success' => true,
            'message' => 'Permission revoked from role successfully',
        ], 200);
    }
}
