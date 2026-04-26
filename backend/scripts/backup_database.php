<?php
/**
 * HoneHube Database Backup Script
 * 
 * This script creates a backup of the database
 * Run manually or set up as a cron job
 * 
 * Usage: php backup_database.php
 */

// Configuration
$dbHost = 'localhost';
$dbName = 'honehube';
$dbUser = 'root';  // Change for production
$dbPass = '';      // Change for production

// Backup directory
$backupDir = __DIR__ . '/backups';
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Backup filename with timestamp
$timestamp = date('Y-m-d_H-i-s');
$backupFile = $backupDir . '/honehube_backup_' . $timestamp . '.sql';

// mysqldump command
$command = sprintf(
    'mysqldump --host=%s --user=%s --password=%s %s > %s 2>&1',
    escapeshellarg($dbHost),
    escapeshellarg($dbUser),
    escapeshellarg($dbPass),
    escapeshellarg($dbName),
    escapeshellarg($backupFile)
);

// Execute backup
echo "Starting database backup...\n";
echo "Database: {$dbName}\n";
echo "Backup file: {$backupFile}\n\n";

exec($command, $output, $returnCode);

if ($returnCode === 0) {
    // Compress the backup
    $gzipCommand = "gzip " . escapeshellarg($backupFile);
    exec($gzipCommand);
    $compressedFile = $backupFile . '.gz';
    
    $fileSize = filesize($compressedFile);
    $fileSizeMB = round($fileSize / 1024 / 1024, 2);
    
    echo "✅ Backup completed successfully!\n";
    echo "File: {$compressedFile}\n";
    echo "Size: {$fileSizeMB} MB\n";
    
    // Delete old backups (keep last 7 days)
    $files = glob($backupDir . '/honehube_backup_*.sql.gz');
    $now = time();
    $deleted = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) >= 7 * 24 * 3600) { // 7 days
                unlink($file);
                $deleted++;
            }
        }
    }
    
    if ($deleted > 0) {
        echo "\n🗑️  Deleted {$deleted} old backup(s)\n";
    }
    
    // Log backup
    $logFile = $backupDir . '/backup_log.txt';
    $logEntry = date('Y-m-d H:i:s') . " - Backup successful: {$compressedFile} ({$fileSizeMB} MB)\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
} else {
    echo "❌ Backup failed!\n";
    echo "Error output:\n";
    print_r($output);
    
    // Log error
    $logFile = $backupDir . '/backup_log.txt';
    $logEntry = date('Y-m-d H:i:s') . " - Backup FAILED: " . implode("\n", $output) . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    exit(1);
}

echo "\n✅ Backup process completed!\n";
?>
