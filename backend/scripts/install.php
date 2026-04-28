<?php
/**
 * Honehube Database Installation Script
 * Run this file once to set up the database
 */

// Configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'honehube';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Honehube Database Installation</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .step h3 {
            color: #667eea;
            margin-bottom: 8px;
            font-size: 16px;
        }
        .step p {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
        }
        .success {
            background: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .success h3 {
            color: #28a745;
        }
        .error {
            background: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .error h3 {
            color: #dc3545;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }
        .warning h3 {
            color: #ffc107;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: background 0.3s;
        }
        button:hover {
            background: #5568d3;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .code {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .admin-info {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .admin-info strong {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Honehube Database Installation</h1>
        <p class="subtitle">Evelyn Hone College E-commerce Platform</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Installation process
            try {
                // Connect to MySQL (without database)
                $conn = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                echo '<div class="step success">';
                echo '<h3>✓ Step 1: MySQL Connection Successful</h3>';
                echo '<p>Successfully connected to MySQL server.</p>';
                echo '</div>';
                
                // Create database
                $conn->exec("CREATE DATABASE IF NOT EXISTS $dbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                echo '<div class="step success">';
                echo '<h3>✓ Step 2: Database Created</h3>';
                echo '<p>Database "' . $dbName . '" has been created successfully.</p>';
                echo '</div>';
                
                // Connect to the new database
                $conn->exec("USE $dbName");
                
                // Read and execute schema file
                $schemaFile = __DIR__ . '/database/schema.sql';
                if (file_exists($schemaFile)) {
                    $sql = file_get_contents($schemaFile);
                    
                    // Split by semicolon and execute each statement
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $statement) {
                        if (!empty($statement)) {
                            $conn->exec($statement);
                        }
                    }
                    
                    echo '<div class="step success">';
                    echo '<h3>✓ Step 3: Tables Created</h3>';
                    echo '<p>All database tables have been created successfully.</p>';
                    echo '</div>';
                    
                    echo '<div class="step success">';
                    echo '<h3>✓ Step 4: Sample Data Inserted</h3>';
                    echo '<p>Default admin user and sample listings have been added.</p>';
                    echo '</div>';
                    
                    echo '<div class="admin-info">';
                    echo '<h3>🔐 Default Admin Account</h3>';
                    echo '<p><strong>Email:</strong> admin@honehube.com</p>';
                    echo '<p><strong>Password:</strong> Admin@123</p>';
                    echo '<p style="margin-top:10px; color:#dc3545;"><strong>⚠️ IMPORTANT:</strong> Change this password immediately after first login!</p>';
                    echo '</div>';
                    
                    echo '<div class="step success">';
                    echo '<h3>🎉 Installation Complete!</h3>';
                    echo '<p>Your database is ready to use. You can now:</p>';
                    echo '<ul style="margin-left:20px; margin-top:10px;">';
                    echo '<li>Delete this install.php file for security</li>';
                    echo '<li>Visit your login page and test the system</li>';
                    echo '<li>Read DATABASE_SETUP.md for more information</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                } else {
                    throw new Exception("Schema file not found: $schemaFile");
                }
                
            } catch(PDOException $e) {
                echo '<div class="step error">';
                echo '<h3>✗ Installation Failed</h3>';
                echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p style="margin-top:10px;">Please check:</p>';
                echo '<ul style="margin-left:20px;">';
                echo '<li>MySQL is running in XAMPP</li>';
                echo '<li>Database credentials are correct</li>';
                echo '<li>You have permission to create databases</li>';
                echo '</ul>';
                echo '</div>';
            } catch(Exception $e) {
                echo '<div class="step error">';
                echo '<h3>✗ Error</h3>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        } else {
            // Show installation form
            ?>
            <div class="step">
                <h3>📋 Pre-Installation Checklist</h3>
                <p>Before proceeding, ensure:</p>
                <ul style="margin-left:20px; margin-top:10px; line-height:1.8;">
                    <li>✓ XAMPP is installed</li>
                    <li>✓ Apache and MySQL are running</li>
                    <li>✓ You have MySQL root access</li>
                    <li>✓ Port 3306 is not blocked</li>
                </ul>
            </div>

            <div class="step warning">
                <h3>⚠️ Important Notes</h3>
                <p>This script will:</p>
                <ul style="margin-left:20px; margin-top:10px; line-height:1.8;">
                    <li>Create a new database named "honehube"</li>
                    <li>Create all required tables</li>
                    <li>Insert default admin user and sample data</li>
                    <li>If database exists, it will be updated (not deleted)</li>
                </ul>
            </div>

            <div class="step">
                <h3>🔧 Current Configuration</h3>
                <div class="code">
Host: <?php echo $dbHost; ?><br>
User: <?php echo $dbUser; ?><br>
Password: <?php echo empty($dbPass) ? '(empty)' : '****'; ?><br>
Database: <?php echo $dbName; ?>
                </div>
                <p style="margin-top:10px; font-size:12px; color:#666;">
                    To change these settings, edit the variables at the top of install.php
                </p>
            </div>

            <form method="POST">
                <button type="submit">🚀 Install Database</button>
            </form>

            <p style="text-align:center; margin-top:20px; color:#666; font-size:12px;">
                After installation, delete this file for security
            </p>
            <?php
        }
        ?>
    </div>
</body>
</html>
