@echo off
:: HUGO-Assistant — Manual Deploy / Pull Script
:: Run this anytime to pull latest code from GitHub and update the system

echo ========================================
echo   HUGO-Assistant — Deploying...
echo ========================================
echo.

cd /d "c:\laragon\www\HUGO-Assistant"

:: Pull latest code
echo [1/4] Pulling from GitHub...
git pull origin main
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] git pull failed! Check your internet or credentials.
    pause
    exit /b 1
)
echo [OK] Code updated

:: Install/update PHP dependencies (skip if composer not in PATH)
echo [2/4] Updating dependencies...
where composer >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    composer install --no-dev --optimize-autoloader --no-interaction
    echo [OK] Composer done
) else (
    echo [SKIP] composer not in PATH, skipping
)

:: Clear Laravel caches
echo [3/4] Clearing caches...
where php >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo [OK] Caches cleared
) else (
    echo [SKIP] php not in PATH, skipping artisan
)

echo [4/4] Done!
echo.
echo ========================================
echo   System updated successfully!
echo ========================================
pause
