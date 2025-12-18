<?php
/**
 * FindMyDojo - Edit Dojo
 * Update dojo with all fields including pricing, schedule, image
 */

$page_title = 'Edit Your Dojo';
$page_description = 'Update your martial arts dojo details.';

require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dojo_owner') {
    header('Location: dashboard.php');
    exit;
}

$dojo_id = (int)($_GET['id'] ?? 0);
if ($dojo_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

try {
    $dojoStmt = $pdo->prepare("
        SELECT d.*, c.city_name, co.country_id
        FROM dojos d
        INNER JOIN cities c ON d.city_id = c.city_id
        INNER JOIN countries co ON c.country_id = co.country_id
        WHERE d.dojo_id = ? AND d.owner_id = ?
    ");
    $dojoStmt->execute([$dojo_id, $_SESSION['user_id']]);
    $dojo = $dojoStmt->fetch();

    if (!$dojo) {
        header('Location: dashboard.php');
        exit;
    }

    // Current styles
    $stylesStmt = $pdo->prepare("SELECT style_id FROM dojo_styles WHERE dojo_id = ?");
    $stylesStmt->execute([$dojo_id]);
    $current_styles = array_column($stylesStmt->fetchAll(), 'style_id');

    // Current schedule
    $scheduleStmt = $pdo->prepare("SELECT * FROM dojo_schedule WHERE dojo_id = ? ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
    $scheduleStmt->execute([$dojo_id]);
    $schedule = $scheduleStmt->fetchAll();

    // All countries and styles
    $countries = $pdo->query("SELECT country_id, country_name FROM countries ORDER BY country_name")->fetchAll();
    $styles = $pdo->query("SELECT style_id, style_name FROM styles ORDER BY style_name")->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$form_submitted = false;
$form_error = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    try {
        $dojo_name = htmlspecialchars(trim($_POST['dojo_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars(trim($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $city_name = htmlspecialchars(trim($_POST['city_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $country_id = (int)($_POST['country_id'] ?? 0);
        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $website = filter_var($_POST['website'] ?? '', FILTER_VALIDATE_URL);
        $selected_styles = $_POST['styles'] ?? [];

        $monthly_price = $_POST['monthly_price'] !== '' ? (float)$_POST['monthly_price'] : null;
        $has_free_trial = isset($_POST['has_free_trial']) ? 1 : 0;
        $trial_details = $has_free_trial ? htmlspecialchars(trim($_POST['trial_details'] ?? ''), ENT_QUOTES, 'UTF-8') : null;

        $schedule_days = $_POST['schedule_day'] ?? [];
        $schedule_start = $_POST['schedule_start'] ?? [];
        $schedule_end = $_POST['schedule_end'] ?? [];
        $schedule_type = $_POST['schedule_type'] ?? [];

        if (!$dojo_name || !$description || !$address || !$city_name || !$country_id || !$phone || !$email || empty($selected_styles)) {
            throw new Exception('Please fill all required fields.');
        }

        $pdo->beginTransaction();

        // City handling
        $cityStmt = $pdo->prepare("SELECT city_id FROM cities WHERE city_name = ? AND country_id = ?");
        $cityStmt->execute([$city_name, $country_id]);
        $city = $cityStmt->fetch();

        if ($city) {
            $city_id = $city['city_id'];
        } else {
            $insertCity = $pdo->prepare("INSERT INTO cities (city_name, country_id) VALUES (?, ?)");
            $insertCity->execute([$city_name, $country_id]);
            $city_id = $pdo->lastInsertId();
        }

        // Image update
        $dojo_image = $dojo['dojo_image'];
        if (isset($_FILES['dojo_image']) && $_FILES['dojo_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['dojo_image'];
            $allowed_types = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $max_size = 5 * 1024 * 1024;

            if (!array_key_exists($ext, $allowed_types)) {
                throw new Exception('Invalid image type.');
            }
            if ($file['size'] > $max_size) {
                throw new Exception('Image too large.');
            }

            $upload_dir = 'assets/images/dojos/';
            $filename = 'dojo_' . $_SESSION['user_id'] . '_' . $dojo_id . '_' . time() . '.' . $ext;
            $target_path = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                if ($dojo_image && file_exists($upload_dir . $dojo_image)) {
                    unlink($upload_dir . $dojo_image);
                }
                $dojo_image = $filename;
            } else {
                throw new Exception('Failed to upload image.');
            }
        }

        // Update dojo
        $updateDojo = "UPDATE dojos SET 
            dojo_name = ?, description = ?, address = ?, city_id = ?, phone = ?, email = ?, website = ?, dojo_image = ?, 
            monthly_price = ?, has_free_trial = ?, trial_details = ?
            WHERE dojo_id = ? AND owner_id = ?";
        $updateStmt = $pdo->prepare($updateDojo);
        $updateStmt->execute([$dojo_name, $description, $address, $city_id, $phone, $email, $website, $dojo_image, 
            $monthly_price, $has_free_trial, $trial_details, $dojo_id, $_SESSION['user_id']]);

        // Update styles
        $pdo->prepare("DELETE FROM dojo_styles WHERE dojo_id = ?")->execute([$dojo_id]);
        foreach ($selected_styles as $style_id) {
            $pdo->prepare("INSERT INTO dojo_styles (dojo_id, style_id) VALUES (?, ?)")
                ->execute([$dojo_id, (int)$style_id]);
        }

        // Update schedule
        $pdo->prepare("DELETE FROM dojo_schedule WHERE dojo_id = ?")->execute([$dojo_id]);
        for ($i = 0; $i < count($schedule_days); $i++) {
            if (!empty($schedule_days[$i]) && !empty($schedule_start[$i]) && !empty($schedule_end[$i]) && !empty($schedule_type[$i])) {
                $insertSchedule = "INSERT INTO dojo_schedule (dojo_id, day_of_week, start_time, end_time, class_type) VALUES (?, ?, ?, ?, ?)";
                $pdo->prepare($insertSchedule)->execute([$dojo_id, $schedule_days[$i], $schedule_start[$i], $schedule_end[$i], htmlspecialchars($schedule_type[$i])]);
            }
        }

        $pdo->commit();
        $form_submitted = true;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $form_error = true;
        $error_message = $e->getMessage();
    }
}
?>

<div style="padding-top: 8rem; padding-bottom: 6rem;">
    <div class="container">
        <a href="dashboard.php" class="btn btn-outline" style="margin-bottom: 2rem;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <h1 class="section-title">Edit Dojo</h1>

        <?php if ($form_submitted): ?>
        <div class="glass-card" style="padding: 2rem; background: #22c55e15; border: 2px solid #22c55e; margin-bottom: 2rem;">
            <i class="fas fa-check-circle" style="font-size: 2.5rem; color: #22c55e;"></i>
            <h3 style="color: #22c55e;">Dojo updated successfully!</h3>
        </div>
        <?php endif; ?>

        <?php if ($form_error): ?>
        <div class="glass-card" style="padding: 2rem; background: #ef444415; border: 2px solid #ef4444; margin-bottom: 2rem;">
            <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
            <span style="color: #ef4444; font-weight: 600;"><?php echo htmlspecialchars($error_message); ?></span>
        </div>
        <?php endif; ?>

        <div class="glass-card" style="padding: 2rem;">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                <!-- Dojo Name -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="dojo_name" class="form-label">Dojo Name *</label>
                    <input type="text" id="dojo_name" name="dojo_name" class="form-input" value="<?php echo htmlspecialchars($dojo['dojo_name']); ?>" required>
                </div>

                <!-- Description -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="description" class="form-label">Description *</label>
                    <textarea id="description" name="description" class="form-textarea" rows="4" required><?php echo htmlspecialchars($dojo['description']); ?></textarea>
                </div>

                <!-- Address -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="address" class="form-label">Address *</label>
                    <input type="text" id="address" name="address" class="form-input" value="<?php echo htmlspecialchars($dojo['address']); ?>" required>
                </div>

                <!-- City & Country -->
                <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="city_name" class="form-label">City *</label>
                        <input type="text" id="city_name" name="city_name" class="form-input" value="<?php echo htmlspecialchars($dojo['city_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country_id" class="form-label">Country *</label>
                        <select id="country_id" name="country_id" class="form-select" required>
                            <option value="">Select Country</option>
                            <?php foreach ($countries as $country): ?>
                                <option value="<?php echo $country['country_id']; ?>" <?php echo $country['country_id'] == $dojo['country_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($country['country_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Phone & Email -->
                <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone *</label>
                        <input type="tel" id="phone" name="phone" class="form-input" value="<?php echo htmlspecialchars($dojo['phone']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($dojo['email']); ?>" required>
                    </div>
                </div>

                <!-- Website -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="website" class="form-label">Website</label>
                    <input type="url" id="website" name="website" class="form-input" value="<?php echo htmlspecialchars($dojo['website']); ?>">
                </div>

                <!-- Image -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="dojo_image" class="form-label">Update Dojo Image</label>
                    <input type="file" id="dojo_image" name="dojo_image" accept="image/jpeg,image/png,image/gif" class="form-input">
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-top: 0.5rem;">
                        Current image: <?php echo $dojo['dojo_image'] ? htmlspecialchars($dojo['dojo_image']) : 'None'; ?>
                    </p>
                </div>

                <!-- Pricing -->
                <div style="margin: 2rem 0; padding: 1.5rem; background: hsla(24, 72%, 50%, 0.05); border-radius: var(--radius-lg); border: 1px solid hsla(24, 72%, 50%, 0.2);">
                    <h3 style="font-size: 1.25rem; color: var(--color-primary); margin-bottom: 1rem;">
                        <i class="fas fa-dollar-sign"></i> Pricing Information (in USD)
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 1.5rem;">
                        All prices are displayed in US Dollars for international consistency.
                    </p>

                    <div class="grid grid-cols-2" style="gap: 1.5rem;">
                        <div class="form-group">
                            <label for="monthly_price" class="form-label">Monthly Fee (USD)</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground); font-weight: 600;">$</span>
                                <input type="number" step="0.01" min="0" id="monthly_price" name="monthly_price" class="form-input" placeholder="99.99" value="<?php echo $dojo['monthly_price'] ? number_format($dojo['monthly_price'], 2) : ''; ?>" style="padding-left: 2.5rem;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Free Trial Available?</label>
                            <label style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.75rem; cursor: pointer;">
                                <input type="checkbox" name="has_free_trial" id="has_free_trial" style="width: 1.5rem; height: 1.5rem; accent-color: var(--color-primary);" <?php echo $dojo['has_free_trial'] ? 'checked' : ''; ?>>
                                <span>Yes, we offer a free trial</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top: 1rem; display: <?php echo $dojo['has_free_trial'] ? 'block' : 'none'; ?>;" id="trial_details_group">
                        <label for="trial_details" class="form-label">Trial Details</label>
                        <input type="text" id="trial_details" name="trial_details" class="form-input" placeholder="e.g., 1 free class, 7-day unlimited" value="<?php echo htmlspecialchars($dojo['trial_details']); ?>">
                    </div>
                </div>

                <!-- Class Schedule Section -->
                <div style="margin: 2rem 0; padding: 1.5rem; background: hsla(77, 44%, 25%, 0.05); border-radius: var(--radius-lg); border: 1px solid hsla(77, 44%, 25%, 0.2);">
                    <h3 style="font-size: 1.25rem; color: var(--color-primary); margin-bottom: 1rem;">
                        <i class="fas fa-clock"></i> Class Schedule
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 1.5rem;">
                        Update your training days and times.
                    </p>

                    <div id="schedule-rows">
                        <?php if (empty($schedule)): ?>
                            <div class="grid grid-cols-4" style="gap: 1rem; margin-bottom: 1rem;">
                                <select name="schedule_day[]" class="form-select" required>
                                    <option value="">Day</option>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                                <input type="time" name="schedule_start[]" class="form-input" required>
                                <input type="time" name="schedule_end[]" class="form-input" required>
                                <input type="text" name="schedule_type[]" class="form-input" placeholder="e.g., Adults BJJ" required>
                            </div>
                        <?php else: ?>
                            <?php foreach ($schedule as $i => $slot): ?>
                            <div class="grid grid-cols-4" style="gap: 1rem; margin-bottom: 1rem;">
                                <select name="schedule_day[]" class="form-select" required>
                                    <option value="">Day</option>
                                    <option value="Monday" <?php echo $slot['day_of_week'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                                    <option value="Tuesday" <?php echo $slot['day_of_week'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                                    <option value="Wednesday" <?php echo $slot['day_of_week'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                                    <option value="Thursday" <?php echo $slot['day_of_week'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                                    <option value="Friday" <?php echo $slot['day_of_week'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                                    <option value="Saturday" <?php echo $slot['day_of_week'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                                    <option value="Sunday" <?php echo $slot['day_of_week'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>
                                </select>
                                <input type="time" name="schedule_start[]" class="form-input" value="<?php echo $slot['start_time']; ?>" required>
                                <input type="time" name="schedule_end[]" class="form-input" value="<?php echo $slot['end_time']; ?>" required>
                                <input type="text" name="schedule_type[]" class="form-input" value="<?php echo htmlspecialchars($slot['class_type']); ?>" placeholder="e.g., Adults BJJ" required>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="button" id="add-schedule-row" class="btn btn-outline" style="margin-top: 0.5rem;">
                        <i class="fas fa-plus"></i> Add Another Day
                    </button>
                </div>

                <!-- Styles Selection -->
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label">Martial Arts Styles Taught *</label>
                    <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.75rem;">
                        Select all that apply
                    </p>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                        <?php foreach ($styles as $style): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.75rem; border: 1px solid var(--color-border); border-radius: var(--radius-md); transition: all var(--transition-base);" class="style-checkbox">
                            <input type="checkbox" name="styles[]" value="<?php echo $style['style_id']; ?>" style="width: 1.25rem; height: 1.25rem; accent-color: var(--color-primary);" <?php echo in_array($style['style_id'], $current_styles) ? 'checked' : ''; ?>>
                            <span><?php echo htmlspecialchars($style['style_name']); ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for Trial and Schedule -->
<script>
document.getElementById('has_free_trial').addEventListener('change', function() {
    document.getElementById('trial_details_group').style.display = this.checked ? 'block' : 'none';
});

document.getElementById('add-schedule-row').addEventListener('click', function() {
    const container = document.getElementById('schedule-rows');
    const row = document.createElement('div');
    row.className = 'grid grid-cols-4';
    row.style.gap = '1rem';
    row.style.marginBottom = '1rem';

    row.innerHTML = `
        <select name="schedule_day[]" class="form-select" required>
            <option value="">Day</option>
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
            <option value="Sunday">Sunday</option>
        </select>
        <input type="time" name="schedule_start[]" class="form-input" required>
        <input type="time" name="schedule_end[]" class="form-input" required>
        <input type="text" name="schedule_type[]" class="form-input" placeholder="e.g., Adults BJJ" required>
        <button type="button" class="btn btn-outline" style="background: #ef444415; color: #ef4444;" onclick="this.parentElement.remove()">
            <i class="fas fa-trash"></i>
        </button>
    `;

    container.appendChild(row);
});
</script>

<style>
.style-checkbox:hover {
    background: var(--color-muted);
    border-color: var(--color-primary);
}
.style-checkbox:has(input:checked) {
    background: hsla(24, 72%, 50%, 0.1);
    border-color: var(--color-primary);
}
</style>

<?php require_once 'includes/footer.php'; ?>