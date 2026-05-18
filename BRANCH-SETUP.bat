@echo off
title MPR Group PVT Ltd - Branch First-Time Setup
color 0B

echo.
echo  ============================================
echo   MPR Group PVT Ltd
echo   BRANCH FIRST-TIME SETUP WIZARD
echo  ============================================
echo.
echo  This wizard will set up THIS PC as a branch.
echo  Run this ONLY ONCE on a new branch computer.
echo.

:: ---- Set the project directory ----
set "APP_DIR=%~dp0"
cd /d "%APP_DIR%"

:: ---- Check PHP ----
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

:: ---- Ask for Branch Name ----
echo.
echo  ============================================
echo   STEP 1: Enter a name for this branch
echo  ============================================
echo.
echo  Examples:
echo    Main Store
echo    Branch - Butwal
echo    Branch - Pokhara
echo    Warehouse Store
echo.
set /p BRANCH_NAME="  Enter Branch Name: "

if "%BRANCH_NAME%"=="" (
    set "BRANCH_NAME=Branch Store"
)

:: ---- Ask for Admin Password ----
echo.
echo  ============================================
echo   STEP 2: Set the Admin Password for this branch
echo  ============================================
echo.
set /p ADMIN_PASS="  Enter Admin Password (default: password): "
if "%ADMIN_PASS%"=="" (
    set "ADMIN_PASS=password"
)

:: ---- Ask for Admin Email ----
echo.
set /p ADMIN_EMAIL="  Enter Admin Email (default: admin@mprgroup.com): "
if "%ADMIN_EMAIL%"=="" (
    set "ADMIN_EMAIL=admin@mprgroup.com"
)

:: ---- Write .env with branch name ----
echo.
echo  [..] Configuring this branch...

php -r "
$env = file_get_contents('.env');
$env = preg_replace('/^APP_NAME=.*/m', 'APP_NAME=\"%BRANCH_NAME%\"', $env);
file_put_contents('.env', $env);
"

echo  [OK] Branch name set to: %BRANCH_NAME%

:: ---- Clear old database and start fresh ----
echo.
echo  [..] Setting up fresh database for this branch...

:: Remove old SQLite database if it exists
if exist "database\database.sqlite" (
    del /f "database\database.sqlite"
    echo  [OK] Old database cleared.
)

:: Create new empty sqlite file
type nul > "database\database.sqlite"
echo  [OK] Fresh database created.

:: ---- Run migrations ----
echo  [..] Running database setup...
php artisan migrate --force
if %ERRORLEVEL% NEQ 0 (
    color 0C
    echo  [ERROR] Database migration failed!
    pause
    exit /b 1
)
echo  [OK] Database tables created.

:: ---- Seed default data ----
echo  [..] Adding default data...
php artisan db:seed --force
echo  [OK] Default data added.

:: ---- Update admin credentials ----
echo  [..] Setting admin credentials...
php artisan branch:setup-admin --email="%ADMIN_EMAIL%" --password="%ADMIN_PASS%" --name="%BRANCH_NAME% Admin"
if %ERRORLEVEL% NEQ 0 (
    echo  [WARN] Could not set custom admin. Using defaults: admin@mprgroup.com / password
)

:: ---- Generate app key ----
echo  [..] Generating security key...
php artisan key:generate --force
echo  [OK] Security key generated.

:: ---- Clear caches ----
php artisan config:clear >nul 2>&1
php artisan cache:clear >nul 2>&1

echo.
color 0A
echo  ============================================
echo   SETUP COMPLETE!
echo  ============================================
echo.
echo   Branch Name : %BRANCH_NAME%
echo   Admin Email : %ADMIN_EMAIL%
echo   Password    : %ADMIN_PASS%
echo.
echo   To start this branch, double-click:
echo   START-APP.bat
echo.
echo  ============================================
echo.
pause
