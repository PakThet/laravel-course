<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user('api')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$request->user('api')->hasAnyPermission($permissions)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden: You do not have the required permission',
            ], 403);
        }

        return $next($request);
    }
}
