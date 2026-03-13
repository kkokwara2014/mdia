<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !($request->user()->hasPermission('admin') || $request->user()->hasPermission('super_admin'))) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin access required.',
            ], 403);
        }

        return $next($request);
    }
}
