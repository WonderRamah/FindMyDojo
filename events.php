<?php
/**
 * FindMyDojo - Events Page
 */

$page_title = 'Events';
$page_description = 'Discover tournaments, seminars, and training events from around the world.';
require_once 'includes/header.php';

// Fetch events
try {
    $query = "
        SELECT 
            e.*,
            d.dojo_name,
            d.dojo_image,
            c.city_name,
            co.country_name
        FROM events e
        INNER JOIN dojos d ON e.dojo_id = d.dojo_id
        INNER JOIN cities c ON d.city_id = c.city_id
        INNER JOIN countries co ON c.country_id = co.country_id
        WHERE e.is_active = 1 
        AND e.event_date >= CURDATE()
        ORDER BY e.event_date ASC, e.event_time ASC
    ";
    
    $stmt = $pdo->query($query);
    $events = $stmt->fetchAll();

    $featuredEvent = !empty($events) ? $events[0] : null;
    $regularEvents = array_slice($events, 1);

} catch (PDOException $e) {
    die("Error fetching events: " . $e->getMessage());
}
?>

<div style="padding-top: 6rem; padding-bottom: 4rem;">
    <div class="container">
        <!-- Header -->
        <div class="text-center" style="margin-bottom: 2rem;" data-aos="fade-up">
            <h1 class="section-title">
                <span style="color: var(--color-foreground);">UPCOMING</span>
                <span class="gradient-text">EVENTS</span>
            </h1>
            <p class="section-description">
                Discover tournaments, seminars, and training events near you
            </p>
        </div>

        <?php if (empty($events)): ?>
        <div class="glass-card" style="padding: 3rem; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 0.8rem;">📅</div>
            <h2 style="font-size: 1.4rem; margin-bottom: 0.5rem;">No Upcoming Events</h2>
            <p style="color: var(--color-muted-foreground); margin-bottom: 1.2rem;">
                Check back later for new events
            </p>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'dojo_owner'): ?>
            <a href="add_event.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Event
            </a>
            <?php endif; ?>
        </div>

        <?php else: ?>

        <!-- Featured Event - Small like dojo cards -->
        <?php if ($featuredEvent): ?>
        <div class="dojo-card hover-lift" style="margin-bottom: 1.5rem;" data-aos="fade-up">
            <a href="dojo_detail.php?id=<?php echo $featuredEvent['dojo_id']; ?>#events" style="display: block; text-decoration: none; color: inherit;">
                <div class="dojo-image">
                    <?php 
                    $featuredImg = !empty($featuredEvent['event_image']) 
                        ? 'assets/images/events/' . htmlspecialchars($featuredEvent['event_image'])
                        : (!empty($featuredEvent['dojo_image']) 
                            ? 'assets/images/dojos/' . htmlspecialchars($featuredEvent['dojo_image'])
                            : 'https://thumbs.dreamstime.com/z/professional-martial-arts-dojo-interior-stunning-training-space-bright-sharp-lighting-pristine-tatami-mats-step-373023527.jpg');
                    ?>
                    <img src="<?php echo $featuredImg; ?>" 
                         alt="<?php echo htmlspecialchars($featuredEvent['event_name']); ?>"
                         style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="dojo-image-overlay"></div>
                    <div style="position: absolute; top: 0.8rem; left: 0.8rem;">
                        <span style="background: var(--color-accent); color: white; padding: 0.3rem 0.8rem; border-radius: 50px; font-size: 0.75rem;">
                            Featured
                        </span>
                    </div>
                </div>

                <div class="dojo-content">
                    <h3 class="dojo-name" style="font-size: 1.1rem; margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($featuredEvent['event_name']); ?>
                    </h3>
                    <div class="dojo-location" style="font-size: 0.85rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($featuredEvent['event_date'])); ?>
                        <?php if ($featuredEvent['event_time']): ?> · <?php echo date('g:i A', strtotime($featuredEvent['event_time'])); ?><?php endif; ?>
                    </div>
                    <div class="dojo-location" style="font-size: 0.85rem;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($featuredEvent['location']); ?>
                    </div>
                    <div class="dojo-footer" style="margin-top: 0.8rem;">
                        <span><?php echo $featuredEvent['current_participants']; ?> / <?php echo $featuredEvent['max_participants'] ?? '∞'; ?> participants</span>
                    </div>
                </div>
            </a>
        </div>
        <?php endif; ?>

        <!-- Regular Events Grid - Same size as dojo cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3" style="gap: 1.5rem;" id="eventsGrid">
            <?php foreach ($regularEvents as $index => $event): ?>
            <div class="dojo-card hover-lift" 
                 data-aos="fade-up" 
                 data-aos-delay="<?php echo $index * 100; ?>">
                
                <a href="dojo_detail.php?id=<?php echo $event['dojo_id']; ?>#events" style="display: block; text-decoration: none; color: inherit;">
                    <div class="dojo-image">
                        <?php 
                        $cardImg = !empty($event['event_image']) 
                            ? 'assets/images/events/' . htmlspecialchars($event['event_image'])
                            : (!empty($event['dojo_image']) 
                                ? 'assets/images/dojos/' . htmlspecialchars($event['dojo_image'])
                                : 'https://thumbs.dreamstime.com/z/professional-martial-arts-dojo-interior-stunning-training-space-bright-sharp-lighting-pristine-tatami-mats-step-373023527.jpg');
                        ?>
                        <img src="<?php echo $cardImg; ?>" 
                             alt="<?php echo htmlspecialchars($event['event_name']); ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                        <div class="dojo-image-overlay"></div>
                    </div>

                    <div class="dojo-content">
                        <h3 class="dojo-name" style="font-size: 1.05rem;">
                            <?php echo htmlspecialchars($event['event_name']); ?>
                        </h3>
                        <div class="dojo-location" style="font-size: 0.85rem;">
                            <i class="fas fa-calendar"></i> <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
                            <?php if ($event['event_time']): ?> · <?php echo date('g:i A', strtotime($event['event_time'])); ?><?php endif; ?>
                        </div>
                        <div class="dojo-location" style="font-size: 0.85rem;">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?>
                        </div>
                        <div class="dojo-footer" style="margin-top: 0.8rem;">
                            <span><?php echo $event['current_participants']; ?> / <?php echo $event['max_participants'] ?? '∞'; ?> participants</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- CTA -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'dojo_owner'): ?>
        <div class="text-center" style="margin-top: 2.5rem;">
            <a href="add_event.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Event
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>