<?php
// includes/dashboard_greeting.php
// Shared profile greeting for all dashboards
// Requires $user array to be already fetched

// Build profile image URL
$profile_img_url = !empty($user['profile_image'])
    ? 'assets/images/profiles/' . htmlspecialchars($user['profile_image'])
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['first_name'] . ' ' . $user['last_name']) 
      . '&size=120&background=d97706&color=fff&bold=true';
?>

<!-- Shared Profile Greeting Card -->
<div class="glass-card" style="padding: 2rem; text-align: center; margin-bottom: 3rem;" data-aos="fade-up">
    <img src="<?php echo $profile_img_url; ?>" 
         alt="Profile Picture"
         style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; margin: 0 auto 1.5rem; 
                border: 4px solid var(--color-primary); box-shadow: var(--shadow-md);">

    <h2 style="font-size: 2rem; color: var(--color-foreground); margin-bottom: 0.5rem;">
        Oss! Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!
    </h2>
    
    <p style="color: var(--color-muted-foreground); font-size: 1.125rem; margin-bottom: 1.5rem;">
        <?php echo ucfirst(str_replace('_', ' ', $user['role'])); ?> Dashboard
    </p>

    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="profile.php" class="btn btn-outline">
            <i class="fas fa-user-edit"></i> Edit Profile
        </a>
        <a href="logout.php" class="btn btn-outline" style="border-color: #ef4444; color: #ef4444;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>