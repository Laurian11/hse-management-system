<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        // If user is already authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('auth.failed'),
                    'errors' => [
                        'email' => [__('auth.failed')]
                    ]
                ], 422);
            }
            
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        // Update user login tracking
        $user = Auth::user();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'failed_login_attempts' => 0, // Reset failed attempts on successful login
        ]);

        // Create user session record
        \App\Models\UserSession::startSession(
            $user,
            $request->session()->getId(),
            $request->ip(),
            $request->userAgent()
        );

        // Log the login activity
        \App\Models\ActivityLog::log('login', 'auth', 'User', Auth::id(), 'User logged in');

        // Check if this is an AJAX request
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login successful!',
                'redirect' => '/dashboard'
            ]);
        }

        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        // Log the logout activity
        \App\Models\ActivityLog::log('logout', 'auth', 'User', Auth::id(), 'User logged out');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
