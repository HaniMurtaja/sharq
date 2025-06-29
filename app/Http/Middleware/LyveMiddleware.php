<?php

namespace App\Http\Middleware;

use App\Traits\ResponseHandler;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class LyveMiddleware
{
    use ResponseHandler;
    public function handle(Request $request, Closure $next): Response
    {


        $token = $request->header('Authorization');

        if ($token) {
            if (strpos($token, 'Bearer ') === 0) {
                $token = substr($token, 7);
            }

            $user = User::where('integration_token', $token)->first();


            if ($user) {
                Auth::loginUsingId($user->id);
                return $next($request);

        }
            }


        return $this->responseError("Unauthorized", 401);
    }

}
