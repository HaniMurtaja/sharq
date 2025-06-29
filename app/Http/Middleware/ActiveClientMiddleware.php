<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class ActiveClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        
        $token = $request->route('API_TOKEN');
    
       
       
        $user = User::where('integration_token', $token)->first();
    

       

        if ($user->is_active != 1) {
            return response()->json(['message' => 'Unactive user'], 401);
        }
    
       
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
    
        return $next($request);
    }
    
}
