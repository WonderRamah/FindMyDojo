<?php
/**
 * FindMyDojo - Add Review (FULLY FUNCTIONAL)
 * Allow users to submit reviews for dojos
 */

$page_title = 'Write a Review';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php?redirect=add-review.php');
    exit;
}

// Get dojo ID
$dojo_id = isset($_GET['dojo_id']) ? (int)$_GET['dojo_id'] : 0;

if ($dojo_id <= 0) {
    header('Location: dojos.php');
    exit;
}

$form_submitted = false;
$form_error = false;
$error_message = '';

// Fetch dojo details
try {
    $dojoStmt = $pdo->prepare("
        SELECT d.dojo_name, d.dojo_image, c.city_name, co.country_name
        FROM dojos d
        INNER JOIN cities c ON d.city_id = c.city_id
        INNER JOIN countries co ON c.country_id = co.country_id
        WHERE d.dojo_id = ? AND d.is_approved = 1
    ");
    $dojoStmt->execute([$dojo_id]);
    $dojo = $dojoStmt->fetch();

    if (!$dojo) {
        header('Location: dojos.php');
        exit;
    }

    // Check if user already reviewed this dojo
    $checkReview = $pdo->prepare("SELECT review_id FROM reviews WHERE dojo_id = ? AND user_id = ?");
    $checkReview->execute([$dojo_id, $_SESSION['user_id']]);
    $existingReview = $checkReview->fetch();

    if ($existingReview) {
        $form_error = true;
        $error_message = 'You have already reviewed this dojo.';
    }

} catch (PDOException $e) {
    die("Error loading dojo: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    try {
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $review_text = htmlspecialchars(trim($_POST['review_text'] ?? ''), ENT_QUOTES, 'UTF-8');

        // Validation
        if ($rating < 1 || $rating > 5) {
            throw new Exception('Please select a rating between 1 and 5 stars.');
        }

        if (empty($review_text) || strlen($review_text) < 10) {
            throw new Exception('Review must be at least 10 characters long.');
        }

        // Check again for existing review
        $checkReview = $pdo->prepare("SELECT review_id FROM reviews WHERE dojo_id = ? AND user_id = ?");
        $checkReview->execute([$dojo_id, $_SESSION['user_id']]);
        if ($checkReview->fetch()) {
            throw new Exception('You have already reviewed this dojo.');
        }

        // Insert review
        $insertReview = $pdo->prepare("
            INSERT INTO reviews (dojo_id, user_id, rating, review_text, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $insertReview->execute([$dojo_id, $_SESSION['user_id'], $rating, $review_text]);

        $form_submitted = true;

    } catch (Exception $e) {
        $form_error = true;
        $error_message = $e->getMessage();
    } catch (PDOException $e) {
        $form_error = true;
        $error_message = 'Database error: ' . $e->getMessage();
    }
}
?>

<div style="padding-top: 6rem; padding-bottom: 4rem;">
    <div class="container" style="max-width: 800px;">
        
        <a href="dojo_detail.php?id=<?php echo $dojo_id; ?>" class="btn btn-outline" style="margin-bottom: 1.5rem;">
            <i class="fas fa-arrow-left"></i> Back to Dojo
        </a>

        <?php if ($form_submitted): ?>
        <!-- Success Message -->
        <div class="glass-card" style="padding: 3rem; text-align: center;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;"></i>
            <h2 style="font-size: 1.75rem; color: var(--color-secondary); margin-bottom: 0.5rem;">
                Review Submitted!
            </h2>
            <p style="color: var(--color-muted-foreground); margin-bottom: 2rem;">
                Thank you for sharing your experience.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <a href="dojo-detail.php?id=<?php echo $dojo_id; ?>" class="btn btn-primary">
                    View Dojo
                </a>
                <a href="my-reviews.php" class="btn btn-outline">
                    My Reviews
                </a>
            </div>
        </div>

        <?php else: ?>
        
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Write a Review</h1>
        <p style="color: var(--color-muted-foreground); margin-bottom: 2rem;">Share your experience at this dojo</p>

        <!-- Dojo Info Card -->
        <div class="glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; gap: 1.5rem; align-items: center;">
                <img src="<?php echo $dojo['dojo_image'] ?? 'https://images.unsplash.com/photo-1555597673-b21d5c935865?w=200&h=150&fit=crop'; ?>" 
                     alt="<?php echo htmlspecialchars($dojo['dojo_name']); ?>"
                     style="width: 120px; height: 90px; object-fit: cover; border-radius: var(--radius-md);">
                <div>
                    <h3 style="font-size: 1.25rem; color: var(--color-secondary); margin-bottom: 0.25rem;">
                        <?php echo htmlspecialchars($dojo['dojo_name']); ?>
                    </h3>
                    <p style="color: var(--color-muted-foreground);">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php echo htmlspecialchars($dojo['city_name'] . ', ' . $dojo['country_name']); ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if ($form_error): ?>
        <div style="padding: 1rem; background: #ef444415; border: 2px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
            <span style="color: #ef4444; margin-left: 0.5rem;"><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>

        <!-- Review Form -->
        <div class="glass-card" style="padding: 2rem;">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <!-- Rating Section -->
                <div style="margin-bottom: 2rem;">
                    <label class="form-label" style="margin-bottom: 1rem;">Your Rating *</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;" id="star-rating">
                        <i class="far fa-star rating-star" data-rating="1" style="font-size: 2.5rem; cursor: pointer; color: var(--color-muted); transition: all 0.2s;"></i>
                        <i class="far fa-star rating-star" data-rating="2" style="font-size: 2.5rem; cursor: pointer; color: var(--color-muted); transition: all 0.2s;"></i>
                        <i class="far fa-star rating-star" data-rating="3" style="font-size: 2.5rem; cursor: pointer; color: var(--color-muted); transition: all 0.2s;"></i>
                        <i class="far fa-star rating-star" data-rating="4" style="font-size: 2.5rem; cursor: pointer; color: var(--color-muted); transition: all 0.2s;"></i>
                        <i class="far fa-star rating-star" data-rating="5" style="font-size: 2.5rem; cursor: pointer; color: var(--color-muted); transition: all 0.2s;"></i>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" required>
                    <p id="rating-label" style="color: var(--color-muted-foreground); font-size: 0.875rem;">Click to select your rating</p>
                </div>

                <!-- Review Text -->
                <div style="margin-bottom: 2rem;">
                    <label for="review_text" class="form-label">Your Review *</label>
                    <textarea 
                        id="review_text" 
                        name="review_text" 
                        class="form-textarea" 
                        rows="6" 
                        placeholder="Share your experience... What did you like? What could be improved?"
                        required
                        minlength="10"></textarea>
                    <p style="color: var(--color-muted-foreground); font-size: 0.875rem; margin-top: 0.5rem;">
                        <span id="char-count">0</span> characters (minimum 10)
                    </p>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-paper-plane"></i>
                        Submit Review
                    </button>
                    <a href="dojo_detail.php?id=<?php echo $dojo_id; ?>" class="btn btn-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <?php endif; ?>
    </div>
</div>

<script>
// Star Rating System
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('rating-input');
    const ratingLabel = document.getElementById('rating-label');
    
    const labels = [
        '',
        'Poor - Not recommended',
        'Fair - Needs improvement',
        'Good - Worth trying',
        'Very Good - Recommended',
        'Excellent - Highly recommended'
    ];

    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            highlightStars(rating);
        });

        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            ratingInput.value = rating;
            ratingLabel.textContent = labels[rating];
            ratingLabel.style.color = 'var(--color-primary)';
            ratingLabel.style.fontWeight = '600';
            highlightStars(rating, true);
        });
    });

    document.getElementById('star-rating').addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        highlightStars(currentRating, true);
    });

    function highlightStars(rating, permanent = false) {
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('far');
                star.classList.add('fas');
                star.style.color = 'var(--color-accent)';
            } else {
                if (!permanent || index >= parseInt(ratingInput.value)) {
                    star.classList.remove('fas');
                    star.classList.add('far');
                    star.style.color = 'var(--color-muted)';
                }
            }
        });
    }

    // Character counter
    const textarea = document.getElementById('review_text');
    const charCount = document.getElementById('char-count');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
        if (this.value.length >= 10) {
            charCount.style.color = 'var(--color-primary)';
        } else {
            charCount.style.color = 'var(--color-muted-foreground)';
        }
    });
});


<?php if ($form_error && isset($_POST['rating'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const postedRating = <?php echo (int)$_POST['rating']; ?>;
        if (postedRating > 0) {
            ratingInput.value = postedRating;
            ratingLabel.textContent = labels[postedRating];
            ratingLabel.style.color = 'var(--color-primary)';
            ratingLabel.style.fontWeight = '600';
            highlightStars(postedRating, true);
        }
    });
    
</script>
<?php endif; ?>
</script>

<?php require_once 'includes/footer.php'; ?>