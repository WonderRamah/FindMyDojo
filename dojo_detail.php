<?php
/**
 * FindMyDojo - Dojo Detail Page
 * View individual dojo information, reviews, pricing, schedule, and contact details
 */

require_once 'includes/config.php';  // DB and session only

$dojo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($dojo_id <= 0) {
    header('Location: dojos.php');
    exit;
}

try {
    $query = "
        SELECT 
            d.*,
            c.city_name,
            co.country_name,
            co.country_code,
            u.email as owner_email,
            p.first_name,
            p.last_name,
            GROUP_CONCAT(DISTINCT s.style_name SEPARATOR ', ') as styles,
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(DISTINCT r.review_id) as review_count
        FROM dojos d
        INNER JOIN cities c ON d.city_id = c.city_id
        INNER JOIN countries co ON c.country_id = co.country_id
        INNER JOIN users u ON d.owner_id = u.user_id
        INNER JOIN profiles p ON u.user_id = p.user_id
        LEFT JOIN dojo_styles ds ON d.dojo_id = ds.dojo_id
        LEFT JOIN styles s ON ds.style_id = s.style_id
        LEFT JOIN reviews r ON d.dojo_id = r.dojo_id
        WHERE d.dojo_id = ? AND d.is_approved = 1
        GROUP BY d.dojo_id
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$dojo_id]);
    $dojo = $stmt->fetch();
    
    if (!$dojo) {
        header('Location: dojos.php');
        exit;
    }
    
    // Fetch reviews
    $reviewsQuery = "
        SELECT 
            r.*,
            p.first_name,
            p.last_name,
            p.profile_image
        FROM reviews r
        INNER JOIN users u ON r.user_id = u.user_id
        INNER JOIN profiles p ON u.user_id = p.user_id
        WHERE r.dojo_id = ?
        ORDER BY r.created_at DESC
        LIMIT 10
    ";
    
    $reviewsStmt = $pdo->prepare($reviewsQuery);
    $reviewsStmt->execute([$dojo_id]);
    $reviews = $reviewsStmt->fetchAll();
    
    // Fetch schedule
    $scheduleQuery = "
        SELECT *
        FROM dojo_schedule
        WHERE dojo_id = ?
        ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ";
    
    $scheduleStmt = $pdo->prepare($scheduleQuery);
    $scheduleStmt->execute([$dojo_id]);
    $schedule = $scheduleStmt->fetchAll();
    
    // Fetch events for this dojo
    $eventsQuery = "
        SELECT *
        FROM events
        WHERE dojo_id = ? AND event_date >= CURDATE()
        ORDER BY event_date ASC, event_time ASC
        LIMIT 5
    ";
    
    $eventsStmt = $pdo->prepare($eventsQuery);
    $eventsStmt->execute([$dojo_id]);
    $events = $eventsStmt->fetchAll();
    
    $page_title = $dojo['dojo_name'];
    $page_description = substr($dojo['description'], 0, 150);
    
    require_once 'includes/header.php';  // Now include header after all redirects
    
} catch (PDOException $e) {
    header('Location: dojos.php');
    exit;
}
?>

<div style="padding-top: 6rem; padding-bottom: 4rem;">
    <div class="container">
        <!-- Back Button -->
        <div style="margin-bottom: 2rem;" data-aos="fade-right">
            <a href="dojos.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to Dojos
            </a>
        </div>

        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 2rem;">
            <!-- Main Content -->
            <div>
                <!-- Dojo Header -->
                <div class="glass-card" style="padding: 0; overflow: hidden; margin-bottom: 2rem;" data-aos="fade-up">
                    <div style="height: 400px; position: relative;">
                        <?php 
                        $dojoImg = !empty($dojo['dojo_image']) 
                            ? 'assets/images/dojos/' . htmlspecialchars($dojo['dojo_image'])
                            : 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=1200&h=600&fit=crop&quality=80';
                        ?>
                        <img src="<?php echo $dojoImg; ?>" 
                             alt="<?php echo htmlspecialchars($dojo['dojo_name']); ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);"></div>
                        
                        <div style="position: absolute; bottom: 2rem; left: 2rem; right: 2rem; color: white;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star" style="color: <?php echo $i <= round($dojo['avg_rating']) ? 'var(--color-primary)' : '#ffffff66'; ?>;"></i>
                                <?php endfor; ?>
                                <span style="font-size: 1.25rem; font-weight: 600;"><?php echo number_format($dojo['avg_rating'], 1); ?></span>
                                <span style="opacity: 0.8;">(<?php echo $dojo['review_count']; ?> reviews)</span>
                            </div>
                            <h1 style="font-size: 3rem; margin-bottom: 0.5rem;">
                                <?php echo htmlspecialchars($dojo['dojo_name']); ?>
                            </h1>
                            <p style="font-size: 1.125rem; opacity: 0.9;">
                                <i class="fas fa-map-marker-alt"></i>
                                <?php echo htmlspecialchars($dojo['city_name'] . ', ' . $dojo['country_name']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Styles Tags -->
                    <div style="padding: 1.5rem; background: white; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        <?php 
                        $stylesArray = $dojo['styles'] ? explode(', ', $dojo['styles']) : [];
                        foreach ($stylesArray as $style): ?>
                            <span class="style-tag"><?php echo htmlspecialchars($style); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- About Section -->
                <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="100">
                    <h2 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-info-circle" style="color: var(--color-primary);"></i> About This Dojo
                    </h2>
                    <p style="color: var(--color-foreground); line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($dojo['description'])); ?>
                    </p>
                </div>

                <!-- Class Schedule Section -->
                <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="150">
                    <h2 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-clock" style="color: var(--color-primary);"></i> Class Schedule
                    </h2>
                    <?php if (empty($schedule)): ?>
                        <p style="color: var(--color-muted-foreground); text-align: center; padding: 2rem 0;">
                            No schedule information available
                        </p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <?php foreach ($schedule as $slot): ?>
                            <div style="padding: 1rem; background: var(--color-muted); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <strong style="min-width: 100px;"><?php echo htmlspecialchars($slot['day_of_week']); ?></strong>
                                <span><?php echo date('g:i A', strtotime($slot['start_time'])); ?> - <?php echo date('g:i A', strtotime($slot['end_time'])); ?></span>
                                <em style="color: var(--color-primary);"><?php echo htmlspecialchars($slot['class_type']); ?></em>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Upcoming Events Section -->
                <div class="glass-card" style="padding: 2rem; margin-bottom: 2rem;" data-aos="fade-up" data-aos-delay="200">
                    <h2 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-calendar-alt" style="color: var(--color-primary);"></i> Upcoming Events
                    </h2>
                    <?php if (empty($events)): ?>
                        <p style="color: var(--color-muted-foreground); text-align: center; padding: 2rem 0;">
                            No upcoming events
                        </p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <?php foreach ($events as $event): ?>
                            <div style="padding: 1.5rem; background: var(--color-muted); border-radius: var(--radius-lg);">
                                <h3 style="font-size: 1.25rem; color: var(--color-primary); margin-bottom: 0.5rem;">
                                    <?php echo htmlspecialchars($event['event_name']); ?>
                                </h3>
                                <p style="color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                    <i class="fas fa-calendar"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                    <?php if ($event['event_time']): ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?><?php endif; ?>
                                </p>
                                <p style="color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['event_location']); ?>
                                </p>
                                <?php if ($event['event_price'] > 0): ?>
                                <p style="color: var(--color-primary); font-weight: 600;">
                                    <i class="fas fa-dollar-sign"></i> $<?php echo number_format($event['event_price'], 2); ?>
                                </p>
                                <?php endif; ?>
                                <p style="color: var(--color-foreground); line-height: 1.6; margin-top: 0.5rem;">
                                    <?php echo nl2br(htmlspecialchars($event['event_description'])); ?>
                                </p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reviews Section -->
                <div class="glass-card" style="padding: 2rem;" data-aos="fade-up" data-aos-delay="250">
                    <h2 style="font-size: 1.75rem; margin-bottom: 1.5rem; color: var(--color-secondary);">
                        <i class="fas fa-comments" style="color: var(--color-primary);"></i>
                        Reviews (<?php echo $dojo['review_count']; ?>)
                    </h2>

                    <?php if (empty($reviews)): ?>
                        <p style="text-align: center; color: var(--color-muted-foreground); padding: 2rem 0;">
                            No reviews yet. Be the first to review!
                        </p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <?php foreach ($reviews as $review): ?>
                            <div style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.5rem;">
                                <div style="display: flex; align-items: start; gap: 1rem; margin-bottom: 0.75rem;">
                                    <?php 
                                    $profileImg = !empty($review['profile_image']) 
                                        ? 'assets/images/profiles/' . htmlspecialchars($review['profile_image'])
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($review['first_name'] . ' ' . $review['last_name']) . '&background=d97706&color=fff&bold=true&size=120';
                                    ?>
                                    <img src="<?php echo $profileImg; ?>" 
                                         alt="<?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?>"
                                         style="width: 3rem; height: 3rem; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary); box-shadow: var(--shadow-sm);">
                                    <div style="flex: 1;">
                                        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.25rem;">
                                            <h4 style="font-weight: 600; color: var(--color-foreground);">
                                                <?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?>
                                            </h4>
                                            <div style="display: flex; gap: 0.25rem;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star" style="color: <?php echo $i <= $review['rating'] ? 'var(--color-accent)' : 'var(--color-muted)'; ?>; font-size: 0.875rem;"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                                            <?php echo date('F j, Y', strtotime($review['created_at'])); ?>
                                        </p>
                                        <p style="color: var(--color-foreground); line-height: 1.6;">
                                            <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Write Review Button -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="my_review.php?dojo_id=<?php echo $dojo_id; ?>" class="btn btn-primary">
                            Write a Review
                        </a>
                    </div>
                    <?php else: ?>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <p style="color: var(--color-muted-foreground); margin-bottom: 1rem;">
                            Log in to write a review
                        </p>
                        <a href="auth.php" class="btn btn-outline">
                            Log In
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Pricing Card -->
                <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;" data-aos="fade-left">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-dollar-sign" style="color: var(--color-primary);"></i> Pricing
                    </h3>
                    <?php if ($dojo['monthly_price']): ?>
                    <p style="font-size: 1.5rem; font-weight: 700; color: var(--color-foreground); margin-bottom: 0.5rem;">
                        $<?php echo number_format($dojo['monthly_price'], 2); ?> / month
                    </p>
                    <?php endif; ?>
                    <?php if ($dojo['has_free_trial']): ?>
                    <p style="color: var(--color-primary);">
                        Free Trial: <?php echo htmlspecialchars($dojo['trial_details']); ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Contact Card -->
                <div class="glass-card" style="padding: 1.5rem; margin-bottom: 1.5rem;" data-aos="fade-left">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-address-card" style="color: var(--color-primary);"></i> Contact Information
                    </h3>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-phone" style="color: var(--color-primary);"></i>
                            <a href="tel:<?php echo htmlspecialchars($dojo['phone']); ?>" style="color: var(--color-foreground);">
                                <?php echo htmlspecialchars($dojo['phone']); ?>
                            </a>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-envelope" style="color: var(--color-primary);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($dojo['email']); ?>" style="color: var(--color-foreground);">
                                <?php echo htmlspecialchars($dojo['email']); ?>
                            </a>
                        </div>
                        <?php if ($dojo['website']): ?>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fas fa-globe" style="color: var(--color-primary);"></i>
                            <a href="<?php echo htmlspecialchars($dojo['website']); ?>" target="_blank" style="color: var(--color-foreground);">
                                Visit Website
                            </a>
                        </div>
                        <?php endif; ?>
                        <div style="display: flex; align-items: start; gap: 0.75rem;">
                            <i class="fas fa-map-marker-alt" style="color: var(--color-primary); margin-top: 0.25rem;"></i>
                            <span style="color: var(--color-foreground);">
                                <?php echo nl2br(htmlspecialchars($dojo['address'] . "\n" . $dojo['city_name'] . ', ' . $dojo['country_name'])); ?>
                            </span>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem;">
                        <a href="contact.php?dojo_id=<?php echo $dojo_id; ?>" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Contact Dojo
                        </a>
                    </div>
                </div>

                <!-- Owner Info Card -->
                <div class="glass-card" style="padding: 1.5rem;" data-aos="fade-left" data-aos-delay="100">
                    <h3 style="font-size: 1.25rem; margin-bottom: 1rem; color: var(--color-secondary);">
                        <i class="fas fa-user-tie" style="color: var(--color-primary);"></i> Dojo Owner
                    </h3>
                    <p style="font-weight: 600; color: var(--color-foreground);">
                        <?php echo htmlspecialchars($dojo['first_name'] . ' ' . $dojo['last_name']); ?>
                    </p>
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground);">
                        Member since <?php echo date('Y', strtotime($dojo['created_at'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>