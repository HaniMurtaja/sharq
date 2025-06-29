<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\TokenAuthFormRequest;
use App\Models\User;
use Illuminate\Support\Str;

class TokenAuthController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(TokenAuthFormRequest $request)
    {

       if(Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])){

           if (Auth::user()->integration_token == null) {
               $token =  $this->GenerateToken();
               Auth::user()->update(['integration_token' => $token]);
               $user = User::findOrFail(Auth::user()->id);
                $user->integration_token = $token;
                $user->save();
               return response()->json([
                   'API_TOKEN' => $token
               ]);

       }
// dd(Auth::user()->integration_toke/n);

           return response()->json([
               'API_TOKEN' => Auth::user()->integration_token
           ]);
       }

       return response()->json([
            'message ' =>"UNAUTHORIZED",
            'code' => 401
        ],401);


    }


    public function GenerateToken()
    {

        do {
            $token = Str::random(80);
        } while (\App\Models\User::where('integration_token', $token)->exists());

        return $token;
    }
}
