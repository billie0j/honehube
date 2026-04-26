# Honehube Startup Script
# This script helps you start XAMPP and open the installation page

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Honehube - Startup Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if XAMPP is installed
$xamppPath = "C:\xampp\xampp-control.exe"
if (-not (Test-Path $xamppPath)) {
    Write-Host "ERROR: XAMPP not found!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please install XAMPP first:" -ForegroundColor Yellow
    Write-Host "https://www.apachefriends.org/" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "✓ XAMPP found" -ForegroundColor Green
Write-Host ""

# Start XAMPP Control Panel
Write-Host "Starting XAMPP Control Panel..." -ForegroundColor Yellow
Start-Process $xamppPath
Start-Sleep -Seconds 2

Write-Host "✓ XAMPP Control Panel opened" -ForegroundColor Green
Write-Host ""

# Check if Apache is running
Write-Host "Checking Apache status..." -ForegroundColor Yellow
$apacheRunning = Test-NetConnection -ComputerName localhost -Port 80 -InformationLevel Quiet -WarningAction SilentlyContinue

if ($apacheRunning) {
    Write-Host "✓ Apache is running" -ForegroundColor Green
} else {
    Write-Host "⚠ Apache is not running" -ForegroundColor Red
    Write-Host "  Please start Apache in XAMPP Control Panel" -ForegroundColor Yellow
}
Write-Host ""

# Check if MySQL is running
Write-Host "Checking MySQL status..." -ForegroundColor Yellow
$mysqlRunning = Test-NetConnection -ComputerName localhost -Port 3306 -InformationLevel Quiet -WarningAction SilentlyContinue

if ($mysqlRunning) {
    Write-Host "✓ MySQL is running" -ForegroundColor Green
} else {
    Write-Host "⚠ MySQL is not running" -ForegroundColor Red
    Write-Host "  Please start MySQL in XAMPP Control Panel" -ForegroundColor Yellow
}
Write-Host ""

# Check if project exists
$projectPath = "C:\xampp\htdocs\honehube\install.php"
if (Test-Path $projectPath) {
    Write-Host "✓ Project found at correct location" -ForegroundColor Green
} else {
    Write-Host "⚠ Project not found at C:\xampp\htdocs\honehube\" -ForegroundColor Red
    Write-Host "  Current location: $PWD" -ForegroundColor Yellow
    Write-Host ""
    $move = Read-Host "Copy project to C:\xampp\htdocs\honehube\? (Y/N)"
    if ($move -eq "Y" -or $move -eq "y") {
        $destination = "C:\xampp\htdocs\honehube\"
        if (-not (Test-Path $destination)) {
            New-Item -ItemType Directory -Path $destination -Force | Out-Null
        }
        Copy-Item -Path ".\*" -Destination $destination -Recurse -Force
        Write-Host "✓ Project copied successfully" -ForegroundColor Green
    }
}
Write-Host ""

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "   Next Steps:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

if (-not $apacheRunning -or -not $mysqlRunning) {
    Write-Host "1. In XAMPP Control Panel:" -ForegroundColor Yellow
    if (-not $apacheRunning) {
        Write-Host "   - Click START next to Apache" -ForegroundColor Yellow
    }
    if (-not $mysqlRunning) {
        Write-Host "   - Click START next to MySQL" -ForegroundColor Yellow
    }
    Write-Host ""
    Write-Host "2. Wait for both to show 'Running'" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "3. Run this script again or visit:" -ForegroundColor Yellow
    Write-Host "   http://localhost/honehube/install.php" -ForegroundColor Cyan
} else {
    Write-Host "✓ All services are running!" -ForegroundColor Green
    Write-Host ""
    $openBrowser = Read-Host "Open installation page in browser? (Y/N)"
    if ($openBrowser -eq "Y" -or $openBrowser -eq "y") {
        Write-Host ""
        Write-Host "Opening browser..." -ForegroundColor Yellow
        Start-Process "http://localhost/honehube/install.php"
        Write-Host "✓ Browser opened" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "Visit this URL to install:" -ForegroundColor Yellow
        Write-Host "http://localhost/honehube/install.php" -ForegroundColor Cyan
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Read-Host "Press Enter to exit"
