<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGenerateReports
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->hasPermission('generate_reports')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to generate reports.',
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'You do not have permission to generate reports.');
        }

        return $next($request);
    }
}
