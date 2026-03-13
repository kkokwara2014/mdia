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
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Unauthorized. Admin access required.');
        }

        return $next($request);
    }
}
