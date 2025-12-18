<?php
/**
 * FindMyDojo - Dashboard
 * User dashboard (Student, Dojo Owner, or Admin)
 */

// Include header
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user information
try {
    $userQuery = "
        SELECT u.*, p.*
        FROM users u
        INNER JOIN profiles p ON u.user_id = p.user_id
        WHERE u.user_id = ?
    ";
    $userStmt = $pdo->prepare($userQuery);
    $userStmt->execute([$user_id]);
    $user = $userStmt->fetch();
    
    if (!$user) {
        session_destroy();
        header('Location: auth.php');
        exit;
    }
    
    // Debug: Check what role is being fetched
    // echo "User Role: " . $user['role']; die(); // Uncomment to debug
    
    $page_title = 'Dashboard';
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div style="padding-top: 6rem; padding-bottom: 4rem; background: var(--color-background);">
    <div class="container">
        <!-- Page Header -->
        <?php if ($user['role'] === 'student'): ?>
            <!-- STUDENT DASHBOARD -->
            <?php include 'includes/dashboard_student.php'; ?>
            
        <?php elseif ($user['role'] === 'dojo_owner'): ?>
            <!-- DOJO OWNER DASHBOARD -->
            <?php include 'includes/dashboard_owner.php'; ?>
            
        <?php elseif ($user['role'] === 'admin'): ?>
            <!-- ADMIN DASHBOARD -->
            <?php include 'includes/dashboard_admin.php'; ?>
            
        <?php else: ?>
            <!-- UNKNOWN ROLE -->
            <div class="glass-card" style="padding: 3rem; text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
                <h2 style="color: var(--color-foreground); margin-bottom: 1rem;">Invalid User Role</h2>
                <p style="color: var(--color-muted-foreground); margin-bottom: 1.5rem;">
                    Your account role is: <strong><?php echo htmlspecialchars($user['role']); ?></strong>
                </p>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Include footer
require_once 'includes/footer.php';
?>