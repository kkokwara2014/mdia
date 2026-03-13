<?php

namespace App\Http\Controllers\API;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'MDIA API',
    description: 'API documentation for Mbaise Diaspora in the Americas application'
)]
#[OA\Server(
    url: '/api',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
class OpenApi
{
}
