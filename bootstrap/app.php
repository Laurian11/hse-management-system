<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log all exceptions for debugging
        $exceptions->report(function (\Throwable $e) {
            // #region agent log
            try {
                $logPath = base_path('.cursor/debug.log');
                $logDir = dirname($logPath);
                if (!is_dir($logDir)) {
                    @mkdir($logDir, 0755, true);
                }
                $logData = json_encode(['id'=>'log_'.time().'_exception','timestamp'=>time()*1000,'location'=>'bootstrap/app.php:19','message'=>'Exception caught','data'=>['message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine(),'class'=>get_class($e)],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
                @file_put_contents($logPath, $logData, FILE_APPEND | LOCK_EX);
            } catch (\Exception $logEx) {
                // Silently fail logging
            }
            // #endregion
        });
        
        // Ensure exceptions are rendered properly
        $exceptions->render(function (\Throwable $e, $request) {
            // Let Laravel handle the rendering, but ensure we always return a response
            return null; // Return null to use default Laravel error handling
        });
    })->create();
