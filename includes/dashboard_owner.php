<?php
/**
 * Dojo Owner Dashboard
 * Manage dojos, view stats, and handle reviews
 */

// Fetch owner's dojos
$dojosQuery = "
    SELECT 
        d.*,
        c.city_name,
        co.country_name,
        COALESCE(AVG(r.rating), 0) as avg_rating,
        COUNT(DISTINCT r.review_id) as review_count
    FROM dojos d
    INNER JOIN cities c ON d.city_id = c.city_id
    INNER JOIN countries co ON c.country_id = co.country_id
    LEFT JOIN reviews r ON d.dojo_id = r.dojo_id
    WHERE d.owner_id = ?
    GROUP BY d.dojo_id
    ORDER BY d.created_at DESC
";
$dojosStmt = $pdo->prepare($dojosQuery);
$dojosStmt->execute([$user_id]);
$myDojos = $dojosStmt->fetchAll();

// Count approved and pending dojos
$approvedCount = 0;
$pendingCount = 0;
foreach ($myDojos as $dojo) {
    $dojo['is_approved'] ? $approvedCount++ : $pendingCount++;
}

// Total reviews across all dojos
$totalReviewsStmt = $pdo->prepare("
    SELECT COUNT(*) as count 
    FROM reviews r
    INNER JOIN dojos d ON r.dojo_id = d.dojo_id
    WHERE d.owner_id = ?
");
$totalReviewsStmt->execute([$user_id]);
$totalReviews = $totalReviewsStmt->fetch()['count'];
?>

<!-- 1. SHARED PROFILE GREETING (Now at the top - no duplication!) -->
<?php include 'includes/dashboard_greeting.php'; ?>

<!-- 2. Stats Cards -->
<div class="grid grid-cols-4" style="gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Total Dojos -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="100">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-building" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo count($myDojos); ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Total Dojos</p>
    </div>

    <!-- Approved Dojos -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="200">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $approvedCount; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Approved</p>
    </div>

    <!-- Pending Dojos -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="300">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $pendingCount; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Pending Approval</p>
    </div>

    <!-- Total Reviews -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="400">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-star" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $totalReviews; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Total Reviews</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="glass-card" style="padding: 1.5rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="500">
    <h2 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 1.5rem;">
        <i class="fas fa-bolt" style="color: var(--color-primary);"></i>
        Quick Actions
    </h2>
    <div class="grid grid-cols-3" style="gap: 1rem;">
        <a href="add_dojo.php" class="btn btn-primary" style="width: 100%;">
            <i class="fas fa-plus"></i> Add New Dojo
        </a>
        <a href="profile.php" class="btn btn-outline" style="width: 100%;">
            <i class="fas fa-user-edit"></i> Edit Profile
        </a>
        <a href="help.php" class="btn btn-outline" style="width: 100%;">
            <i class="fas fa-question-circle"></i> Help Center
        </a>
    </div>
</div>

<!-- My Dojos -->
<div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="600">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.5rem; color: var(--color-secondary);">
            <i class="fas fa-building" style="color: var(--color-primary);"></i>
            My Dojos
        </h2>
        <a href="add_dojo.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Dojo
        </a>
    </div>

    <?php if (empty($myDojos)): ?>
        <div style="text-align: center; padding: 3rem 0;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🥋</div>
            <h3 style="font-size: 1.25rem; color: var(--color-foreground); margin-bottom: 0.5rem;">
                No Dojos Yet
            </h3>
            <p style="color: var(--color-muted-foreground); margin-bottom: 1.5rem;">
                Start by adding your first dojo to reach students worldwide
            </p>
            <a href="add_dojo.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Your First Dojo
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2" style="gap: 1.5rem;">
            <?php foreach ($myDojos as $dojo): ?>
            <div class="dojo-card hover-lift" style="overflow: hidden;">
                <div style="position: relative; height: 150px;">
                    <img src="<?php echo $dojo['dojo_image'] ?? 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=600&h=300&fit=crop'; ?>" 
                         alt="<?php echo htmlspecialchars($dojo['dojo_name']); ?>"
                         style="width: 100%; height: 100%; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);"></div>
                    
                    <?php if ($dojo['is_approved']): ?>
                    <div style="position: absolute; top: 1rem; right: 1rem; padding: 0.375rem 0.875rem; background: #059669; color: white; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600;">
                        <i class="fas fa-check"></i> Approved
                    </div>
                    <?php else: ?>
                    <div style="position: absolute; top: 1rem; right: 1rem; padding: 0.375rem 0.875rem; background: #f59e0b; color: white; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 600;">
                        <i class="fas fa-clock"></i> Pending
                    </div>
                    <?php endif; ?>

                    <div style="position: absolute; bottom: 1rem; left: 1rem; color: white;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fas fa-star" style="color: #fbbf24;"></i>
                            <span style="font-weight: 600;"><?php echo number_format($dojo['avg_rating'], 1); ?></span>
                            <span style="opacity: 0.8; font-size: 0.875rem;">(<?php echo $dojo['review_count']; ?> reviews)</span>
                        </div>
                    </div>
                </div>

                <div style="padding: 1.25rem;">
                    <h3 style="font-size: 1.125rem; color: var(--color-foreground); margin-bottom: 0.5rem; font-weight: 600;">
                        <?php echo htmlspecialchars($dojo['dojo_name']); ?>
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 1rem;">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($dojo['city_name'] . ', ' . $dojo['country_name']); ?>
                    </p>

                    <div style="display: flex; gap: 0.5rem;">
                        <a href="dojo_detail.php?id=<?php echo $dojo['dojo_id']; ?>" class="btn btn-outline" style="flex: 1;">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="edit_dojo.php?id=<?php echo $dojo['dojo_id']; ?>" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form method="POST" action="delete_dojo.php" style="display: inline;" onsubmit="return confirm('Delete this dojo permanently? This cannot be undone.');">
                            <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                            <input type="hidden" name="dojo_id" value="<?php echo $dojo['dojo_id']; ?>">
                            <button type="submit" class="btn btn-outline" style="flex: 1; color: #ef4444; border-color: #ef4444;">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Account Menu Card -->
<div class="glass-card" style="padding: 1.5rem; margin-top: 2rem;" data-aos="fade-up" data-aos-delay="700">
    <h3 style="font-size: 1.125rem; color: var(--color-secondary); margin-bottom: 1rem;">
        Account Management
    </h3>
    <div class="grid grid-cols-4" style="gap: 0.5rem;">
        <a href="profile.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base); border: 1px solid var(--color-border);">
            <i class="fas fa-user" style="color: var(--color-primary);"></i>
            <span>Profile</span>
        </a>
        <a href="help.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base); border: 1px solid var(--color-border);">
            <i class="fas fa-question-circle" style="color: var(--color-primary);"></i>
            <span>Help</span>
        </a>
        <a href="contact.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base); border: 1px solid var(--color-border);">
            <i class="fas fa-envelope" style="color: var(--color-primary);"></i>
            <span>Contact</span>
        </a>
        <a href="logout.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: #ef4444; border-radius: var(--radius-md); transition: all var(--transition-base); border: 1px solid var(--color-border);">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>