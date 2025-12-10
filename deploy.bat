@echo off
REM ============================================
REM HSE Management System - Deployment Script (Windows)
REM ============================================
REM This script automates the deployment process on Windows
REM Usage: deploy.bat [environment]
REM Example: deploy.bat production

setlocal enabledelayedexpansion

set ENVIRONMENT=%1
if "%ENVIRONMENT%"=="" set ENVIRONMENT=production

set APP_DIR=%~dp0
set DATE=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set DATE=%DATE: =0%

echo [INFO] Starting deployment to %ENVIRONMENT% environment...
echo [INFO] Deployment started at: %date% %time%

REM Check if PHP is available
where php >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] PHP is not installed or not in PATH
    exit /b 1
)

REM Check if Composer is available
where composer >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Composer is not installed or not in PATH
    exit /b 1
)

REM Check if Node.js is available
where node >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Node.js is not installed or not in PATH
    exit /b 1
)

REM Check if in Laravel directory
if not exist "artisan" (
    echo [ERROR] Not in Laravel application directory
    exit /b 1
)

echo [INFO] Requirements check passed

REM Enable maintenance mode
echo [INFO] Enabling maintenance mode...
php artisan down --render="errors::503" --retry=60

REM Pull latest code (if using git)
echo [INFO] Pulling latest code from repository...
git pull origin main 2>nul || git pull origin master 2>nul

REM Install PHP dependencies
echo [INFO] Installing PHP dependencies...
call composer install --no-dev --optimize-autoloader --no-interaction
if %errorlevel% neq 0 (
    echo [ERROR] Failed to install PHP dependencies
    php artisan up
    exit /b 1
)

REM Install Node dependencies
echo [INFO] Installing Node dependencies...
call npm ci --production
if %errorlevel% neq 0 (
    echo [WARNING] Failed to install Node dependencies, trying npm install...
    call npm install --production
)

REM Build assets
echo [INFO] Building production assets...
call npm run build
if %errorlevel% neq 0 (
    echo [ERROR] Failed to build assets
    php artisan up
    exit /b 1
)

REM Run migrations
echo [INFO] Running database migrations...
php artisan migrate --force
if %errorlevel% neq 0 (
    echo [ERROR] Failed to run migrations
    php artisan up
    exit /b 1
)

REM Optimize application
echo [INFO] Optimizing application...

REM Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

REM Cache configuration
php artisan config:cache

REM Cache routes
php artisan route:cache

REM Cache views
php artisan view:cache

REM Optimize autoloader
composer dump-autoload --optimize --classmap-authoritative

echo [INFO] Application optimized

REM Disable maintenance mode
echo [INFO] Disabling maintenance mode...
php artisan up

REM Verify deployment
echo [INFO] Verifying deployment...
php artisan about
php artisan migrate:status

echo [INFO] Deployment completed successfully!
echo [INFO] Deployment finished at: %date% %time%

pause

