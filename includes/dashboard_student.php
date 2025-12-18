<?php
/**
 * Student Dashboard
 * Shows reviews, activity, and quick actions
 */

// Fetch student's reviews
$reviewsQuery = "
    SELECT 
        r.*,
        d.dojo_name,
        d.dojo_image,
        c.city_name,
        co.country_name
    FROM reviews r
    INNER JOIN dojos d ON r.dojo_id = d.dojo_id
    INNER JOIN cities c ON d.city_id = c.city_id
    INNER JOIN countries co ON c.country_id = co.country_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
";
$reviewsStmt = $pdo->prepare($reviewsQuery);
$reviewsStmt->execute([$user_id]);
$myReviews = $reviewsStmt->fetchAll();

// Get total review count
$reviewCountStmt = $pdo->prepare("SELECT COUNT(*) as count FROM reviews WHERE user_id = ?");
$reviewCountStmt->execute([$user_id]);
$reviewCount = $reviewCountStmt->fetch()['count'];
?>

<!-- 1. SHARED PROFILE GREETING (Main welcome at the top) -->
<?php include 'includes/dashboard_greeting.php'; ?>

<!-- 2. Stats Cards -->
<div class="grid grid-cols-3" style="gap: 1.5rem; margin-bottom: 3rem;">
    <!-- Total Reviews -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="100">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-star" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo $reviewCount; ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Reviews Written</p>
    </div>

    <!-- Profile Completion -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="200">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-check" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php 
            $completion = 60;
            if (!empty($user['phone'])) $completion += 20;
            if (!empty($user['bio'])) $completion += 20;
            echo $completion; 
            ?>%
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Profile Completion</p>
    </div>

    <!-- Member Since -->
    <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="300">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: var(--radius-lg); display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-calendar" style="font-size: 1.5rem; color: white;"></i>
            </div>
        </div>
        <h3 style="font-size: 2rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.25rem;">
            <?php echo date('Y', strtotime($user['created_at'])); ?>
        </h3>
        <p style="color: var(--color-muted-foreground); font-size: 0.875rem;">Member Since</p>
    </div>
</div>

<!-- Main Layout -->
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
    <!-- Main Content -->
    <div>
        <!-- Quick Actions -->
        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="400">
            <h2 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 1.5rem;">
                <i class="fas fa-bolt" style="color: var(--color-primary);"></i> Quick Actions
            </h2>
            <div class="grid grid-cols-2" style="gap: 1rem;">
                <a href="dojos.php" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-search"></i> Find Dojos
                </a>
                <a href="events.php" class="btn btn-outline" style="width: 100%;">
                    <i class="fas fa-calendar"></i> Browse Events
                </a>
            </div>
        </div>

        <!-- My Reviews -->
        <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-up" data-aos-delay="500">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                <h2 style="font-size: 1.5rem; color: var(--color-secondary);">
                    <i class="fas fa-star" style="color: var(--color-primary);"></i> My Reviews
                </h2>
                <span style="color: var(--color-muted-foreground); font-size: 0.875rem;">
                    <?php echo $reviewCount; ?> total
                </span>
            </div>

            <?php if (empty($myReviews)): ?>
                <div style="text-align: center; padding: 3rem 0;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">📝</div>
                    <p style="color: var(--color-muted-foreground); margin-bottom: 1.5rem;">
                        You haven't written any reviews yet
                    </p>
                    <a href="dojos.php" class="btn btn-primary">
                        Find a Dojo to Review
                    </a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($myReviews as $review): ?>
                    <div style="padding: 1rem; border: 1px solid var(--color-border); border-radius: var(--radius-lg); transition: all var(--transition-base);" class="hover-lift">
                        <div style="display: flex; gap: 1rem;">
                            <img src="<?php echo $review['dojo_image'] ?? 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=100&h=100&fit=crop'; ?>" 
                                 alt="<?php echo htmlspecialchars($review['dojo_name']); ?>"
                                 style="width: 4rem; height: 4rem; border-radius: var(--radius-md); object-fit: cover;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <h4 style="font-weight: 600; color: var(--color-foreground);">
                                        <?php echo htmlspecialchars($review['dojo_name']); ?>
                                    </h4>
                                    <div style="display: flex; gap: 0.25rem;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star" style="font-size: 0.875rem; color: <?php echo $i <= $review['rating'] ? 'var(--color-accent)' : 'var(--color-muted)'; ?>;"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?php echo htmlspecialchars($review['city_name'] . ', ' . $review['country_name']); ?>
                                </p>
                                <p style="color: var(--color-foreground); font-size: 0.875rem; line-height: 1.6;">
                                    <?php echo htmlspecialchars(substr($review['review_text'], 0, 100)) . (strlen($review['review_text']) > 100 ? '...' : ''); ?>
                                </p>
                                <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem; align-items: center;">
                                    <a href="dojo_detail.php?id=<?php echo $review['dojo_id']; ?>" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.375rem 0.75rem;">
                                        View Dojo
                                    </a>
                                    <span style="font-size: 0.75rem; color: var(--color-muted-foreground);">
                                        <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($reviewCount > 5): ?>
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="my-reviews.php" class="btn btn-outline">
                        View All Reviews
                    </a>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar: Account Menu Only -->
    <div>
        <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-left" data-aos-delay="100">
            <h3 style="font-size: 1.125rem; color: var(--color-secondary); margin-bottom: 1rem;">
                Account
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <a href="profile.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-user" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>My Profile</span>
                </a>
                <a href="my-reviews.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-star" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>My Reviews</span>
                </a>
                <a href="help.php" class="hover:bg-muted" style="padding: 0.75rem; display: flex; align-items: center; gap: 0.75rem; color: var(--color-foreground); border-radius: var(--radius-md); transition: all var(--transition-base);">
                    <i class="fas fa-question-circle" style="width: 1.25rem; color: var(--color-primary);"></i>
                    <span>Help Center</span>
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
</style>s