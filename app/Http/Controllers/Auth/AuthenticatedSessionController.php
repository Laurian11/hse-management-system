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
        // #region agent log
        try {
            $logPath = base_path('.cursor/debug.log');
            $logDir = dirname($logPath);
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logData = json_encode(['id'=>'log_'.time().'_auth_entry','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:22','message'=>'Authentication attempt started','data'=>['email'=>$request->input('email')],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
            // Silently fail logging
        }
        // #endregion
        
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // #region agent log
        $logData = json_encode(['id'=>'log_'.time().'_auth_validate','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:29','message'=>'Validation passed','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
        @file_put_contents($logPath, $logData, FILE_APPEND);
        // #endregion

        try {
            $authResult = Auth::attempt($request->only('email', 'password'), $request->boolean('remember'));
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_auth_attempt','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:35','message'=>'Auth attempt result','data'=>['success'=>$authResult],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
        } catch (\Exception $e) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_auth_ex','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:40','message'=>'Auth attempt exception','data'=>['message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
            throw $e;
        }

        if (!$authResult) {
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
        // #region agent log
        $logPath = base_path('.cursor/debug.log');
        $logData = json_encode(['id'=>'log_'.time().'_auth_user','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:49','message'=>'User retrieved after auth','data'=>['user_id'=>$user->id??null,'company_id'=>$user->company_id??null],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
        @file_put_contents($logPath, $logData, FILE_APPEND);
        // #endregion
        
        try {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
                'failed_login_attempts' => 0, // Reset failed attempts on successful login
            ]);
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_auth_update','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:58','message'=>'User login tracking updated','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
        } catch (\Exception $e) {
            // #region agent log
            $logData = json_encode(['id'=>'log_'.time().'_auth_update_err','timestamp'=>time()*1000,'location'=>'app/Http/Controllers/Auth/AuthenticatedSessionController.php:63','message'=>'User update failed','data'=>['message'=>$e->getMessage()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'A'])."\n";
            @file_put_contents($logPath, $logData, FILE_APPEND);
            // #endregion
        }

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
