@echo off
echo ========================================
echo   Honehube - XAMPP Startup Script
echo ========================================
echo.

REM Check if XAMPP is installed
if not exist "C:\xampp\xampp-control.exe" (
    echo ERROR: XAMPP not found at C:\xampp\
    echo.
    echo Please install XAMPP first:
    echo https://www.apachefriends.org/
    echo.
    pause
    exit /b 1
)

echo Starting XAMPP Control Panel...
start "" "C:\xampp\xampp-control.exe"

echo.
echo Waiting for XAMPP Control Panel to open...
timeout /t 3 /nobreak >nul

echo.
echo ========================================
echo   Next Steps:
echo ========================================
echo 1. In XAMPP Control Panel:
echo    - Click START next to Apache
echo    - Click START next to MySQL
echo.
echo 2. Wait for both to show "Running"
echo.
echo 3. Open your browser and visit:
echo    http://localhost/honehube/install.php
echo.
echo ========================================
echo.

REM Ask if user wants to open browser
set /p OPEN_BROWSER="Open browser now? (Y/N): "
if /i "%OPEN_BROWSER%"=="Y" (
    echo.
    echo Opening browser...
    timeout /t 2 /nobreak >nul
    start "" "http://localhost/honehube/"
)

echo.
echo Script complete!
pause
