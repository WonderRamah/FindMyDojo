<?php
/**
 * FindMyDojo - Profile Page
 * User profile viewing, editing, and profile picture upload
 */

$page_title = 'My Profile';
require_once 'includes/header.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$update_success = false;
$update_error = '';
$upload_error = '';

// Upload directory
$upload_dir = 'assets/images/profiles/';
$upload_url = 'assets/images/profiles/';

// Ensure directory exists and is writable
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Fetch user data
try {
    $query = "
        SELECT u.*, p.*
        FROM users u
        INNER JOIN profiles p ON u.user_id = p.user_id
        WHERE u.user_id = ?
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header('Location: auth.php');
        exit;
    }
} catch (PDOException $e) {
    die("Error loading profile: " . $e->getMessage());
}

// Handle profile update and image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    try {
        $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $bio = htmlspecialchars(trim($_POST['bio'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        if (!$first_name || !$last_name) {
            throw new Exception('First name and last name are required.');
        }

        $new_image = $user['profile_image']; // Keep old if no new upload

        // Handle image upload
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['profile_pic'];
            $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $max_size = 2 * 1024 * 1024; // 2MB

            if (!array_key_exists($ext, $allowed) || !in_array($file['type'], $allowed)) {
                throw new Exception('Invalid file type. Only JPG, PNG, and GIF allowed.');
            }
            if ($file['size'] > $max_size) {
                throw new Exception('File too large. Max 2MB.');
            }

            $filename = 'profile_' . $user_id . '_' . time() . '.' . $ext;
            $target = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                // Delete old image if exists and not default
                if ($user['profile_image'] && file_exists($upload_dir . $user['profile_image'])) {
                    @unlink($upload_dir . $user['profile_image']);
                }
                $new_image = $filename;
            } else {
                throw new Exception('Failed to upload image. Check folder permissions.');
            }
        }

        $pdo->beginTransaction();
        
        // Update profile
        $updateProfile = "UPDATE profiles SET first_name = ?, last_name = ?, phone = ?, bio = ?, profile_image = ? WHERE user_id = ?";
        $stmt = $pdo->prepare($updateProfile);
        $stmt->execute([$first_name, $last_name, $phone, $bio, $new_image, $user_id]);
        
        $pdo->commit();
        
        // Update session
        $_SESSION['first_name'] = $first_name;
        
        $update_success = true;
        
        // Refresh user data
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $update_error = $e->getMessage();
    }
}

// Get profile image URL
$profile_img_url = $user['profile_image'] 
    ? $upload_url . htmlspecialchars($user['profile_image'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) . '&size=150&background=d97706&color=fff';
?>

<div style="padding-top: 6rem; padding-bottom: 4rem;">
    <div class="container" style="max-width: 900px;">
        <!-- Header -->
        <div style="margin-bottom: 3rem;" data-aos="fade-up">
            <a href="dashboard.php" class="btn btn-outline" style="margin-bottom: 1rem;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="section-title">My Profile</h1>
            <p class="section-description">Manage your account information and photo</p>
        </div>

        <?php if ($update_success): ?>
        <div style="padding: 1rem; background: #22c55e20; border-left: 4px solid #22c55e; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #22c55e; font-weight: 600;">
                <i class="fas fa-check-circle"></i>
                <span>Profile updated successfully!</span>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($update_error): ?>
        <div style="padding: 1rem; background: #ef444420; border-left: 4px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444; font-weight: 600;">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($update_error); ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Profile Card -->
            <div>
                <div class="glass-card" style="padding: 2rem; text-align: center;" data-aos="fade-right">
                    <div style="position: relative; display: inline-block;">
                        <img id="profilePreview" src="<?php echo $profile_img_url; ?>" 
                             alt="Profile"
                             style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin: 0 auto 1.5rem; border: 4px solid var(--color-primary);">
                        <label for="profile_pic" style="position: absolute; bottom: 10px; right: 10px; background: var(--color-primary); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: var(--shadow-md);">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    
                    <h2 style="font-size: 1.5rem; color: var(--color-foreground); margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                    </h2>
                    
                    <p style="color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </p>
                    
                    <span style="display: inline-block; padding: 0.375rem 0.875rem; background: hsla(24, 72%, 50%, 0.1); color: var(--color-primary); border-radius: var(--radius-full); font-size: 0.875rem; font-weight: 600; text-transform: uppercase;">
                        <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?>
                    </span>
                    
                    <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border); text-align: left;">
                        <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">
                            <i class="fas fa-calendar" style="width: 1.25rem; color: var(--color-primary);"></i>
                            Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div>
                <div class="glass-card" style="padding: 2rem;" data-aos="fade-left">
                    <h3 style="font-size: 1.5rem; color: var(--color-secondary); margin-bottom: 1.5rem;">
                        <i class="fas fa-user-edit" style="color: var(--color-primary);"></i>
                        Edit Profile
                    </h3>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">

                        <!-- Hidden file input -->
                        <input type="file" id="profile_pic" name="profile_pic" accept="image/jpeg,image/png,image/gif" style="display: none;">

                        <!-- Name Fields -->
                        <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" id="first_name" name="first_name" class="form-input"
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" id="last_name" name="last_name" class="form-input"
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                        </div>

                        <!-- Email (Read-only) -->
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled
                                   style="background: var(--color-muted); cursor: not-allowed;">
                            <p style="font-size: 0.75rem; color: var(--color-muted-foreground); margin-top: 0.25rem;">
                                Email cannot be changed. Contact support if needed.
                            </p>
                        </div>

                        <!-- Phone -->
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567"
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <!-- Bio -->
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea id="bio" name="bio" class="form-textarea" rows="4" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <!-- Upload Info -->
                        <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 1rem;">
                            Click the camera icon on your photo to upload a new profile picture (JPG, PNG, GIF • Max 2MB)
                        </p>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                            <i class="fas fa-save"></i>
                            Save Changes
                        </button>
                    </form>
                </div>

                <!-- Account Actions -->
                <div class="glass-card" style="padding: 2rem; margin-top: 2rem;" data-aos="fade-left" data-aos-delay="100">
                    <h3 style="font-size: 1.25rem; color: var(--color-secondary); margin-bottom: 1rem;">
                        Account Actions
                    </h3>
                    <a href="logout.php" class="btn btn-outline" style="justify-content: center; width: 100%;">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Live Preview -->
<script>
document.getElementById('profile_pic').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('profilePreview').src = ev.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>