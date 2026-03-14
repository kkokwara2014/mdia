<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCanValidatePayment
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasPermission('validate_payment')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to validate payments.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'You do not have permission to validate payments.');
        }

        return $next($request);
    }
}
