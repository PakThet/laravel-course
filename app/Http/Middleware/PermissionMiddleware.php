<?php

// namespace App\Http\Middleware;

// use Closure;
// use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;

// class PermissionMiddleware
// {
//     /**
//      * Handle an incoming request.
//      *
//      * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
//      */
//     public function handle(Request $request, Closure $next, $permission): Response
//     {
//         if (auth()->guest()) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Unauthenticated'
//             ], 401);
//         }

//         if (! auth()->user()->hasPermissionTo($permission)) {
//             return response()->json([
//                 'success' => false,
//                 'message' => 'Access denied. Insufficient permissions.'
//             ], 403);
//         }

//         return $next($request);
//     }

// }
