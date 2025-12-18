<?php
/**
 * Admin Dashboard
 * Approve dojos, manage users, view statistics
 */

// Fetch statistics
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT COUNT(*) FROM users WHERE role = 'student') as students,
        (SELECT COUNT(*) FROM users WHERE role = 'dojo_owner') as owners,
        (SELECT COUNT(*) FROM dojos) as total_dojos,
        (SELECT COUNT(*) FROM dojos WHERE is_approved = 1) as approved_dojos,
        (SELECT COUNT(*) FROM dojos WHERE is_approved = 0) as pending_dojos,
        (SELECT COUNT(*) FROM reviews) as total_reviews
";
$stats = $pdo->query($statsQuery)->fetch();

// Fetch pending dojos for approval
$pendingDojosQuery = "
    SELECT 
        d.*,
        c.city_name,
        co.country_name,
        p.first_name,
        p.last_name,
        u.email
    FROM dojos d
    INNER JOIN cities c ON d.city_id = c.city_id
    INNER JOIN countries co ON c.country_id = co.country_id
    INNER JOIN users u ON d.owner_id = u.user_id
    INNER JOIN profiles p ON u.user_id = p.user_id
    WHERE d.is_approved = 0
    ORDER BY d.created_at DESC
    LIMIT 10
";
$pendingDojos = $pdo->query($pendingDojosQuery)->fetchAll();

// Fetch recent users
$recentUsersQuery = "
    SELECT 
        u.*,
        p.first_name,
        p.last_name
    FROM users u
    INNER JOIN profiles p ON u.user_id = p.user_id
    ORDER BY u.created_at DESC
    LIMIT 5
";
$recentUsers = $pdo->query($recentUsersQuery)->fetchAll();

// Handle dojo approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_dojo'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $dojo_id = (int)$_POST['dojo_id'];
        $approveStmt = $pdo->prepare("UPDATE dojos SET is_approved = 1 WHERE dojo_id = ?");
        if ($approveStmt->execute([$dojo_id])) {
            header('Location: dashboard.php?approved=success');
            exit;
        }
    }
}

// Handle dojo rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_dojo'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $dojo_id = (int)$_POST['dojo_id'];
        $rejectStmt = $pdo->prepare("DELETE FROM dojos WHERE dojo_id = ? AND is_approved = 0");
        if ($rejectStmt->execute([$dojo_id])) {
            header('Location: dashboard.php?rejected=success');
            exit;
        }
    }
}
?>

<!-- Success Messages -->
<?php if (isset($_GET['approved'])): ?>
<div style="padding: 1rem; background: #22c55e20; border-left: 4px solid #22c55e; border-radius: var(--radius-md); margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; color: #22c55e; font-weight: 600;">
        <i class="fas fa-check-circle"></i>
        <span>Dojo approved successfully!</span>
    </div>
</div>
<?php endif; ?>

<?php if (isset($_GET['rejected'])): ?>
<div style="padding: 1rem; background: #ef444420; border-left: 4px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444; font-weight: 600;">
        <i class="fas fa-times-circle"></i>
        <span>Dojo rejected and removed.</span>
    </div>
</div>
<?php endif; ?>

<!-- 1. SHARED PROFILE GREETING (Main welcome at the top) -->
<?php include 'includes/dashboard_greeting.php'; ?>

<!-- 2. Admin Stats Cards -->
<div class="grid grid-cols-4" style="gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Total Users -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="100">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $stats['total_users']; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Total Users</p>
        <p style="font-size: 0.75rem; color: var(--color-muted-foreground); margin-top: 0.5rem;">
            <?php echo $stats['students']; ?> students • <?php echo $stats['owners']; ?> owners
        </p>
    </div>

    <!-- Total Dojos -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="200">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-building" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $stats['total_dojos']; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Total Dojos</p>
        <p style="font-size: 0.75rem; color: var(--color-muted-foreground); margin-top: 0.5rem;">
            <?php echo $stats['approved_dojos']; ?> approved
        </p>
    </div>

    <!-- Pending Approvals -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="300">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $stats['pending_dojos']; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Pending Approval</p>
        <?php if ($stats['pending_dojos'] > 0): ?>
        <p style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.5rem;">
            <i class="fas fa-exclamation-triangle"></i> Requires attention
        </p>
        <?php endif; ?>
    </div>

    <!-- Total Reviews -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="400">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-star" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $stats['total_reviews']; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Total Reviews</p>
    </div>
</div>

<!-- Main Layout -->
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Main Content: Pending Dojos -->
    <div>
        <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="500">
            <h2 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 1.5rem;">
                <i class="fas fa-clock" style="color: #f59e0b;"></i> Pending Dojo Approvals
            </h2>

            <?php if (empty($pendingDojos)): ?>
                <div style="text-align: center; padding: 3rem 0;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                    <p style="color: var(--color-muted-foreground);">No pending dojos to approve</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($pendingDojos as $dojo): ?>
                    <div style="padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg); transition: all var(--transition-base);" class="hover-lift">
                        <div style="display: flex; gap: 1rem;">
                            <img src="<?php echo $dojo['dojo_image'] ?? 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=100&h=100&fit=crop'; ?>" 
                                 alt="<?php echo htmlspecialchars($dojo['dojo_name']); ?>"
                                 style="width: 5rem; height: 5rem; border-radius: var(--radius-md); object-fit: cover;">
                            <div style="flex: 1;">
                                <h4 style="font-weight: 600; color: var(--color-foreground); margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($dojo['dojo_name']); ?>
                                </h4>
                                <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($dojo['city_name'] . ', ' . $dojo['country_name']); ?>
                                </p>
                                <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                    <i class="fas fa-user"></i>
                                    Owner: <?php echo htmlspecialchars($dojo['first_name'] . ' ' . $dojo['last_name']); ?>
                                    (<?php echo htmlspecialchars($dojo['email']); ?>)
                                </p>
                                <p style="font-size: 0.875rem; color: var(--color-foreground); line-height: 1.5;">
                                    <?php echo htmlspecialchars(substr($dojo['description'], 0, 150)) . (strlen($dojo['description']) > 150 ? '...' : ''); ?>
                                </p>
                                <div style="margin-top: 1rem; display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <a href="dojo_detail.php?id=<?php echo $dojo['dojo_id']; ?>" class="btn btn-outline" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                                        <input type="hidden" name="dojo_id" value="<?php echo $dojo['dojo_id']; ?>">
                                        <button type="submit" name="approve_dojo" class="btn btn-primary" style="font-size: 0.875rem; padding: 0.5rem 1rem;" onclick="return confirm('Approve this dojo?');">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                                        <input type="hidden" name="dojo_id" value="<?php echo $dojo['dojo_id']; ?>">
                                        <button type="submit" name="reject_dojo" class="btn btn-outline" style="font-size: 0.875rem; padding: 0.5rem 1rem; color: #ef4444; border-color: #ef4444;" onclick="return confirm('Reject and delete this dojo? This cannot be undone.');">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Recent Users -->
        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;" data-aos="fade-left" data-aos-delay="100">
            <h3 style="font-size: 1.125rem; color: var(--color-secondary); margin-bottom: 1rem;">
                Recent Users
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <?php foreach ($recentUsers as $recentUser): ?>
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($recentUser['first_name'] . ' ' . $recentUser['last_name']); ?>&size=40&background=d97706&color=fff&bold=true" 
                         alt="User"
                         style="width: 2.5rem; height: 2.5rem; border-radius: 50%;">
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-weight: 600; font-size: 0.875rem; color: var(--color-foreground); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($recentUser['first_name'] . ' ' . $recentUser['last_name']); ?>
                        </p>
                        <p style="font-size: 0.75rem; color: var(--color-muted-foreground);">
                            <?php echo ucfirst(str_replace('_', ' ', $recentUser['role'])); ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Admin Tools -->
        <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-left" data-aos-delay="200">
            <h3 style="font-size: 1.125rem; color: var(--color-secondary); margin-bottom: 1rem;">
                Admin Tools
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="dojos.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-building" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>All Dojos</span>
                </a>
                <a href="admin/messages.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-envelope" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>Contact Messages</span>
                </a>
                <a href="profile.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-user" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>My Profile</span>
                </a>
                <a href="logout.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: #ef4444; border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-sign-out-alt" style="width: 1.25rem;"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover\:bg-muted:hover {
    background: var(--color-muted);
}
</style>