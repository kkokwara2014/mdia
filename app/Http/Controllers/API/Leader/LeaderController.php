<?php

namespace App\Http\Controllers\API\Leader;

use App\Http\Controllers\Controller;
use App\Models\Leader;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class LeaderController extends Controller
{
    #[OA\Get(
        path: '/leaders',
        summary: 'Get published leaders',
        description: 'Returns organization leaders visible on the public about page. No authentication required.',
        security: [],
        tags: ['Leaders'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Published leaders retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            properties: [
                                new OA\Property(
                                    property: 'leaders',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                                            new OA\Property(property: 'member_uuid', type: 'string', format: 'uuid', description: 'Linked member user UUID'),
                                            new OA\Property(property: 'name', type: 'string'),
                                            new OA\Property(property: 'position', type: 'string'),
                                            new OA\Property(property: 'image_url', type: 'string', format: 'uri'),
                                            new OA\Property(property: 'order', type: 'integer'),
                                            new OA\Property(
                                                property: 'social_links',
                                                type: 'array',
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: 'platform', type: 'string'),
                                                        new OA\Property(property: 'url', type: 'string', format: 'uri'),
                                                        new OA\Property(property: 'icon', type: 'string', description: 'IcoFont class for the public site'),
                                                    ],
                                                    type: 'object'
                                                )
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
        ]
    )]
    public function index(): JsonResponse
    {
        $leaders = Leader::query()
            ->with('user')
            ->where('is_published', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $payload = $leaders->map(function (Leader $leader) {
            return [
                'uuid' => $leader->uuid,
                'member_uuid' => $leader->user?->uuid,
                'name' => $leader->getName(),
                'position' => $leader->position,
                'image_url' => $leader->getImageUrl(),
                'order' => $leader->order,
                'social_links' => $leader->socialLinksForDisplay(),
            ];
        })->values()->all();

        return response()->json([
            'success' => true,
            'data' => [
                'leaders' => $payload,
            ],
        ]);
    }
}
