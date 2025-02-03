<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() == "OPTIONS") {
            return response()->json([], 200);  // Handle preflight requests
        }
    
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:5173')  // Allow your specific frontend domain
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With, Authorization')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
    
}
