<?php
/**
 * FindMyDojo - Add Event
 */

$page_title = 'Add Event';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dojo_owner') {
    header('Location: dashboard.php');
    exit;
}

// Fetch owner's dojos
try {
    $dojosStmt = $pdo->prepare("SELECT dojo_id, dojo_name FROM dojos WHERE owner_id = ? AND is_approved = 1 ORDER BY dojo_name");
    $dojosStmt->execute([$_SESSION['user_id']]);
    $dojos = $dojosStmt->fetchAll();

    if (empty($dojos)) {
        $error = "You must have an approved dojo to add events.";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$form_submitted = false;
$form_error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    try {
        $dojo_id = (int)($_POST['dojo_id'] ?? 0);
        $event_name = htmlspecialchars(trim($_POST['event_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $event_date = $_POST['event_date'] ?? '';
        $event_time = !empty($_POST['event_time']) ? $_POST['event_time'] : null;
        $location = htmlspecialchars(trim($_POST['event_location'] ?? ''), ENT_QUOTES, 'UTF-8');
        $max_participants = !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null;

        // Validation
        if (!$dojo_id || !$event_name || !$description || !$event_date || !$location) {
            throw new Exception('Please fill all required fields.');
        }

        // Check ownership
        $check = $pdo->prepare("SELECT dojo_id FROM dojos WHERE dojo_id = ? AND owner_id = ?");
        $check->execute([$dojo_id, $_SESSION['user_id']]);
        if (!$check->fetch()) {
            throw new Exception('Invalid dojo selected.');
        }

        // Insert event using ONLY columns that exist in your table
        $insert = "INSERT INTO events 
                   (dojo_id, event_name, description, event_date, event_time, location, max_participants) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insert);
        $stmt->execute([
            $dojo_id,
            $event_name,
            $description,
            $event_date,
            $event_time,
            $location,
            $max_participants
        ]);

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

<div style="padding-top: 8rem; padding-bottom: 6rem;">
    <div class="container" style="max-width: 800px;">
        <a href="dashboard.php" class="btn btn-outline" style="margin-bottom: 2rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <h1 class="section-title">Add New Event</h1>

        <?php if (isset($error)): ?>
        <div class="glass-card" style="padding: 2rem; background: #ef444415; border: 2px solid #ef4444; margin-bottom: 2rem;">
            <p><?php echo $error; ?></p>
        </div>
        <?php endif; ?>

        <?php if ($form_submitted): ?>
        <div class="glass-card" style="padding: 2rem; background: #22c55e15; border: 2px solid #22c55e; margin-bottom: 2rem;">
            <h3 style="color: #22c55e;">Event added successfully!</h3>
            <p>Your event has been created and will appear on your dojo page.</p>
        </div>
        <?php endif; ?>

        <?php if ($form_error): ?>
        <div class="glass-card" style="padding: 2rem; background: #ef444415; border: 2px solid #ef4444; margin-bottom: 2rem;">
            <p style="color: #ef4444;"><?php echo htmlspecialchars($error_message); ?></p>
        </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 2rem;">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Your Dojo *</label>
                    <select name="dojo_id" class="form-select" required>
                        <option value="">Select your dojo</option>
                        <?php foreach ($dojos as $d): ?>
                        <option value="<?php echo $d['dojo_id']; ?>" <?php echo isset($dojo_id) && $dojo_id == $d['dojo_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($d['dojo_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Event Name *</label>
                    <input type="text" name="event_name" class="form-input" 
                           value="<?php echo isset($event_name) ? htmlspecialchars($event_name) : ''; ?>"
                           placeholder="e.g., Summer Karate Tournament" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Description *</label>
                    <textarea name="description" class="form-textarea" rows="4" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                </div>

                <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Date *</label>
                        <input type="date" name="event_date" class="form-input" 
                               value="<?php echo isset($event_date) ? htmlspecialchars($event_date) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time (optional)</label>
                        <input type="time" name="event_time" class="form-input" 
                               value="<?php echo isset($event_time) ? htmlspecialchars($event_time) : ''; ?>">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Location *</label>
                    <input type="text" name="event_location" class="form-input" 
                           value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>"
                           placeholder="e.g., Main Hall or Online" required>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label">Max Participants (optional)</label>
                    <input type="number" min="1" name="max_participants" class="form-input" 
                           value="<?php echo isset($max_participants) ? $max_participants : ''; ?>"
                           placeholder="e.g., 50">
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i class="fas fa-calendar-plus"></i> Add Event
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>