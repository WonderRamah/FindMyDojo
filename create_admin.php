<?php
/**
 * Admin Account Creator
 * Creates a fresh admin account with proper password hash
 */

// Database configuration
$db_host = 'localhost';
$db_name = 'dojo';
$db_user = 'root';
$db_pass = '';

// Admin credentials to create
$admin_email = 'admin@findmydojo.com';
$admin_password = 'Admin@123';
$admin_first_name = 'System';
$admin_last_name = 'Administrator';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 3rem;
        }
        h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 2rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-radius: 8px;
        }
        .info-box h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-label {
            color: #666;
            font-weight: 600;
        }
        .info-value {
            color: #333;
            font-family: 'Courier New', monospace;
            background: #fff;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .btn:active {
            transform: translateY(0);
        }
        .result {
            margin-top: 2rem;
            padding: 1.5rem;
            border-radius: 10px;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .result h3 {
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }
        .result-details {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }
        .login-btn {
            display: inline-block;
            background: #28a745;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #218838;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
        }
        code {
            background: #f8f9fa;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Admin Account Creator</h1>
        <p class="subtitle">Create a fresh admin account for FindMyDojo</p>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Connect to database
                $pdo = new PDO(
                    "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
                    $db_user,
                    $db_pass,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );

                // Generate password hash
                $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);

                // Start transaction
                $pdo->beginTransaction();

                // Delete existing admin if exists
                $deleteStmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
                $deleteStmt->execute([$admin_email]);
                $deleted_count = $deleteStmt->rowCount();

                // Insert new admin user
                $insertUserStmt = $pdo->prepare("
                    INSERT INTO users (email, password_hash, role, is_active, created_at, updated_at)
                    VALUES (?, ?, 'admin', 1, NOW(), NOW())
                ");
                $insertUserStmt->execute([$admin_email, $password_hash]);
                $user_id = $pdo->lastInsertId();

                // Insert admin profile
                $insertProfileStmt = $pdo->prepare("
                    INSERT INTO profiles (user_id, first_name, last_name, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                $insertProfileStmt->execute([$user_id, $admin_first_name, $admin_last_name]);

                // Commit transaction
                $pdo->commit();

                // Verify the admin was created
                $verifyStmt = $pdo->prepare("
                    SELECT u.user_id, u.email, u.role, u.is_active, p.first_name, p.last_name
                    FROM users u
                    INNER JOIN profiles p ON u.user_id = p.user_id
                    WHERE u.email = ?
                ");
                $verifyStmt->execute([$admin_email]);
                $admin = $verifyStmt->fetch();

                // Test password verification
                $password_works = password_verify($admin_password, $password_hash);

                echo '<div class="result success">';
                echo '<h3>✅ Admin Account Created Successfully!</h3>';
                
                echo '<div class="result-details">';
                echo '<strong>Database Details:</strong><br>';
                echo 'User ID: ' . $admin['user_id'] . '<br>';
                echo 'Email: ' . $admin['email'] . '<br>';
                echo 'Role: ' . $admin['role'] . '<br>';
                echo 'Name: ' . $admin['first_name'] . ' ' . $admin['last_name'] . '<br>';
                echo 'Active: ' . ($admin['is_active'] ? 'Yes' : 'No') . '<br><br>';
                
                if ($deleted_count > 0) {
                    echo '<em>Note: Deleted ' . $deleted_count . ' old admin account(s)</em><br><br>';
                }
                
                echo '<strong>Password Verification:</strong><br>';
                echo 'Password Hash: ' . substr($password_hash, 0, 50) . '...<br>';
                echo 'Verification Test: ' . ($password_works ? '✅ PASSED' : '❌ FAILED') . '<br>';
                echo '</div>';

                echo '<a href="auth.php" class="login-btn">🚀 Go to Login Page</a>';
                echo '</div>';

            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                
                echo '<div class="result error">';
                echo '<h3>❌ Error Creating Admin</h3>';
                echo '<div class="result-details">';
                echo 'Error: ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                echo '</div>';
            }
        } else {
            // Show form
            ?>
            
            <div class="info-box">
                <h3>📋 Admin Credentials to Create</h3>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo $admin_email; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Password:</span>
                    <span class="info-value"><?php echo $admin_password; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">First Name:</span>
                    <span class="info-value"><?php echo $admin_first_name; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Name:</span>
                    <span class="info-value"><?php echo $admin_last_name; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role:</span>
                    <span class="info-value">admin</span>
                </div>
            </div>

            <div class="warning">
                ⚠️ <strong>Important:</strong> If an admin with email <code><?php echo $admin_email; ?></code> already exists, it will be deleted and replaced with a new one.
            </div>

            <form method="POST">
                <button type="submit" class="btn">
                    🔨 Create Admin Account
                </button>
            </form>

            <div class="info-box" style="margin-top: 2rem;">
                <h3>💡 What This Script Does</h3>
                <ol style="padding-left: 1.5rem; color: #555;">
                    <li>Connects to your database</li>
                    <li>Deletes old admin account (if exists)</li>
                    <li>Creates a fresh admin user</li>
                    <li>Creates admin profile</li>
                    <li>Verifies password hash works correctly</li>
                </ol>
            </div>

            <?php
        }
        ?>
    </div>
</body>
</html>