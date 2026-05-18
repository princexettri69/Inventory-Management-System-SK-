@echo off
title S.K Trade & Suppliers - Inventory System
color 0A

echo.
echo  ============================================
echo   S.K Trade ^& Suppliers - Inventory System
echo  ============================================
echo.

:: ---- Set the project directory (this bat file must stay in the project root) ----
set "APP_DIR=%~dp0"
cd /d "%APP_DIR%"

:: ---- Check PHP is installed ----
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo  [ERROR] PHP is not found on this computer!
    echo.
    echo  Please install Laragon from: https://laragon.org/download/
    echo  Then re-run this file.
    echo.
    pause
    exit /b 1
)

echo  [OK] PHP found.

:: ---- Run migrations (safe - only runs if needed) ----
echo  [..] Checking database...
php artisan migrate --force >nul 2>&1
echo  [OK] Database ready.

:: ---- Clear caches for clean start ----
echo  [..] Clearing caches...
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1
echo  [OK] Caches cleared.

:: ---- Start the Laravel server in the background ----
echo  [..] Starting web server on http://192.168.18.11:8000 ...
echo.

:: Open browser after 2 seconds
start "" /B cmd /c "timeout /t 2 >nul && start http://192.168.18.11:8000"

:: Start server (this keeps the window open - closing it stops the app)
echo  ============================================
echo   App is running at:
echo.
echo   YOUR PC (Main)  : http://localhost:8000
echo   BRANCH PCs (LAN): http://192.168.18.11:8000
echo.
echo   LOGIN DETAILS:
echo   Username : admin
echo   Password : password
echo.
echo   [Keep this window open while using the app]
echo   [Close this window to STOP the server]
echo  ============================================
echo.

php artisan serve --host=0.0.0.0 --port=8000

echo.
echo  Server stopped. Press any key to exit.
pause >nul
