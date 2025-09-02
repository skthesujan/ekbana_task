<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provided_api_key = $request->header('API_KEY');
        $validApiKey = config('checkapi.key');

        if (!$provided_api_key || $provided_api_key !== $validApiKey) {
            return response()->json(['success' => false, 'message' => 'Invalid or Missing API Key'], 401);
        }

        return $next($request);
    }
}
