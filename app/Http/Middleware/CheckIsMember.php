<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasPermission('validate_payment') || $user->hasPermission('admin') || $user->hasPermission('super_admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This endpoint is for members only.',
                ], 403);
            }
            return redirect()->route('dashboard')->with('error', 'This page is for members only.');
        }

        return $next($request);
    }
}
