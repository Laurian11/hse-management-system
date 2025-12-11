<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// #region agent log
try {
    $logPath = __DIR__.'/../.cursor/debug.log';
    $logDir = dirname($logPath);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logData = json_encode(['id'=>'log_'.time().'_entry','timestamp'=>time()*1000,'location'=>'public/index.php:7','message'=>'Request entry point','data'=>['uri'=>$_SERVER['REQUEST_URI']??'','method'=>$_SERVER['REQUEST_METHOD']??''],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    @file_put_contents($logPath, $logData, FILE_APPEND | LOCK_EX);
} catch (\Exception $e) {
    // Silently fail logging
}
// #endregion

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    // #region agent log
    $logData = json_encode(['id'=>'log_'.time().'_maint','timestamp'=>time()*1000,'location'=>'public/index.php:13','message'=>'Maintenance mode detected','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    @file_put_contents($logPath, $logData, FILE_APPEND);
    // #endregion
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// #region agent log
$logData = json_encode(['id'=>'log_'.time().'_autoload','timestamp'=>time()*1000,'location'=>'public/index.php:22','message'=>'Autoloader loaded','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
@file_put_contents($logPath, $logData, FILE_APPEND);
// #endregion

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// #region agent log
$logData = json_encode(['id'=>'log_'.time().'_bootstrap','timestamp'=>time()*1000,'location'=>'public/index.php:28','message'=>'Laravel app bootstrapped','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
@file_put_contents($logPath, $logData, FILE_APPEND);
// #endregion

try {
    $request = Request::capture();
    // #region agent log
    $logData = json_encode(['id'=>'log_'.time().'_req','timestamp'=>time()*1000,'location'=>'public/index.php:34','message'=>'Request captured','data'=>['path'=>$request->path(),'method'=>$request->method()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    @file_put_contents($logPath, $logData, FILE_APPEND);
    // #endregion
    $response = $app->handleRequest($request);
    // #region agent log
    if ($response) {
        $logData = json_encode(['id'=>'log_'.time().'_resp','timestamp'=>time()*1000,'location'=>'public/index.php:38','message'=>'Request handled','data'=>['status'=>$response->getStatusCode()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    } else {
        $logData = json_encode(['id'=>'log_'.time().'_resp_null','timestamp'=>time()*1000,'location'=>'public/index.php:38','message'=>'Request handled but response is null','data'=>[],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    }
    @file_put_contents($logPath, $logData, FILE_APPEND);
    // #endregion
    if ($response) {
        $response->send();
    } else {
        // If response is null, something went wrong - let Laravel handle it
        $app->terminate();
    }
} catch (\Exception $e) {
    // #region agent log
    $logData = json_encode(['id'=>'log_'.time().'_err','timestamp'=>time()*1000,'location'=>'public/index.php:42','message'=>'Exception in request handling','data'=>['message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    @file_put_contents($logPath, $logData, FILE_APPEND);
    // #endregion
    throw $e;
} catch (\Throwable $e) {
    // #region agent log
    $logData = json_encode(['id'=>'log_'.time().'_throwable','timestamp'=>time()*1000,'location'=>'public/index.php:42','message'=>'Throwable in request handling','data'=>['message'=>$e->getMessage(),'file'=>$e->getFile(),'line'=>$e->getLine()],'sessionId'=>'debug-session','runId'=>'run1','hypothesisId'=>'E'])."\n";
    @file_put_contents($logPath, $logData, FILE_APPEND);
    // #endregion
    throw $e;
}
