<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class TokenAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the route parameters
        $token = $request->route('API_TOKEN');
    
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }
    
        // Check if a user exists with the provided integration token
        $user = User::where('integration_token', $token)->first();
    

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        if ($user->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }
    
        // Authenticate the user associated with the integration token
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
    
        return $next($request);
    }
    
}
