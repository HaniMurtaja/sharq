<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('accounting.dashboard');
        }
        
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Prepare credentials
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Add additional constraints for admin login
        $user = User::where('email', $request->email)
                   ->where('is_active', 1)
                   ->first();

        if (!$user) {
            return redirect()->back()
                ->withErrors(['email' => 'User not found or inactive'])
                ->withInput($request->except('password'));
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Authentication successful
            $request->session()->regenerate();
            
            // Log the successful login
            \Log::info('User logged in successfully: ' . Auth::user()->email);
            \Log::info('User ID: ' . Auth::id());
            \Log::info('User has accounting_access: ' . (Auth::user()->hasPermissionTo('accounting_access') ? 'Yes' : 'No'));

            // Redirect to intended page or dashboard
            return redirect()->intended(route('accounting.dashboard'));
        }

        // Authentication failed
        \Log::warning('Login failed for email: ' . $request->email);
        
        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials'])
            ->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('show-login');
    }

    // Test method to verify setup
    public function test_fathy()
    {
        $user = User::where('email', 'admin@example.com')->first();
        
        if ($user) {
            return response()->json([
                'user_exists' => true,
                'email' => $user->email,
                'has_accounting_access' => $user->hasPermissionTo('accounting_access'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
                'roles' => $user->getRoleNames(),
            ]);
        }
        
        return response()->json(['user_exists' => false]);
    }

    public function saveFirebaseToken(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->firebase_token = $request->token;
            $user->save();
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }
}
