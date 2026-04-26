@echo off
REM HoneHube Database Backup Script for Windows
REM This script backs up the database using PHP

echo ========================================
echo HoneHube Database Backup
echo ========================================
echo.

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP or add it to your system PATH
    pause
    exit /b 1
)

REM Run backup script
echo Running backup...
php backup_database.php

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo Backup completed successfully!
    echo ========================================
) else (
    echo.
    echo ========================================
    echo Backup failed! Check the error messages above.
    echo ========================================
)

echo.
pause
