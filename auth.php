<?php
/**
 * FindMyDojo - Authentication Page (FIXED)
 * Login and registration forms with proper role handling
 */

require_once 'includes/config.php';

// Page-specific variables
$page_title = 'Sign In';
$page_description = 'Sign in to your FindMyDojo account or create a new one.';
$page_scripts = ['forms.js'];

// Check mode (login or register)
$mode = isset($_GET['mode']) && $_GET['mode'] === 'register' ? 'register' : 'login';

// Handle form submissions
$login_error = '';
$register_error = '';
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    // ===== LOGIN HANDLER =====
    if ($action === 'login') {
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if ($email && $password) {
            try {
                $stmt = $pdo->prepare("
                    SELECT u.*, p.first_name, p.last_name 
                    FROM users u 
                    INNER JOIN profiles p ON u.user_id = p.user_id 
                    WHERE u.email = ? AND u.is_active = 1
                ");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['first_name'] = $user['first_name'];
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header('Location: dashboard.php');
                    } else {
                        header('Location: dashboard.php');
                    }
                    exit;
                } else {
                    $login_error = 'Invalid email or password';
                }
            } catch (PDOException $e) {
                $login_error = 'Login error. Please try again.';
                error_log("Login error: " . $e->getMessage());
            }
        } else {
            $login_error = 'Please enter valid email and password';
        }
    }
    
    // ===== REGISTER HANDLER =====
    elseif ($action === 'register') {
        $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $user_type = $_POST['user_type'] ?? 'student';
        
        // Validation
        if (!$name || !$email || !$password) {
            $register_error = 'All fields are required';
        } elseif ($password !== $confirm_password) {
            $register_error = 'Passwords do not match';
        } elseif (strlen($password) < 8) {
            $register_error = 'Password must be at least 8 characters';
        } else {
            try {
                // Check if email already exists
                $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
                $checkStmt->execute([$email]);
                
                if ($checkStmt->fetch()) {
                    $register_error = 'Email already registered';
                } else {
                    // Start transaction
                    $pdo->beginTransaction();
                    
                    // Determine role
                    $role = 'student'; // Default
                    if ($user_type === 'owner') {
                        $role = 'dojo_owner';
                    } elseif ($user_type === 'system') {
                        $role = 'admin';
                    }
                    
                    // Insert user
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);
                    $userStmt = $pdo->prepare("INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)");
                    $userStmt->execute([$email, $password_hash, $role]);
                    $user_id = $pdo->lastInsertId();
                    
                    // Insert profile
                    $nameParts = explode(' ', $name, 2);
                    $first_name = $nameParts[0];
                    $last_name = $nameParts[1] ?? '';
                    
                    $profileStmt = $pdo->prepare("INSERT INTO profiles (user_id, first_name, last_name) VALUES (?, ?, ?)");
                    $profileStmt->execute([$user_id, $first_name, $last_name]);
                    
                    // Commit transaction
                    $pdo->commit();
                    $register_success = true;
                    
                    // Auto-login after registration
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;
                    $_SESSION['first_name'] = $first_name;
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                }
            } catch (PDOException $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $register_error = 'Registration error. Please try again.';
                error_log("Registration error: " . $e->getMessage());
            }
        }
    }
}

// Include header
require_once 'includes/header.php';
?>

<div style="min-height: 100vh; display: flex;">
    <!-- Left Panel - Branding (Desktop Only) -->
    <div style="display: none; width: 50%; position: relative; overflow: hidden; background: linear-gradient(135deg, var(--color-secondary-dark) 0%, var(--color-secondary) 100%);" class="auth-left-panel">
        <!-- Background Image -->
        <div style="position: absolute; inset: 0;">
            <img src="https://images.unsplash.com/photo-1555597673-b21d5c935865?w=1200&h=1600&fit=crop" 
                 alt="Martial arts" 
                 style="width: 100%; height: 100%; object-fit: cover; opacity: 0.3;">
        </div>

        <!-- Content -->
        <div style="position: relative; z-index: 10; padding: 3rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; color: white;">
            <!-- Logo -->
            <a href="index.php" style="display: flex; align-items: center; gap: 0.75rem; color: white;">
                <span style="font-size: 2rem;">🐉</span>
                <span style="font-family: var(--font-display); font-size: 1.5rem; letter-spacing: 0.1em;">FINDMYDOJO</span>
            </a>

            <!-- Hero Text -->
            <div>
                <h1 style="font-family: var(--font-display); font-size: 3.5rem; line-height: 1.1; margin-bottom: 1.5rem;" data-aos="fade-up">
                    <span>BEGIN YOUR</span><br>
                    <span style="color: var(--color-primary);">JOURNEY</span>
                </h1>
                <p style="font-size: 1.125rem; opacity: 0.9; max-width: 400px;" data-aos="fade-up" data-aos-delay="100">
                    Join thousands of martial artists discovering their perfect training ground. 
                    Your path to mastery starts here.
                </p>
            </div>

            <!-- Social Proof -->
             <div style="display: flex; margin-left: -0.5rem;">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; border: 2px solid var(--color-secondary-dark); background-image: url('https://i.pravatar.cc/100?img=<?= $i + 10 ?>'); background-size: cover; margin-left: -0.5rem;"></div>
                <?php endfor; ?>
             </div>
            <div style="font-size: 0.875rem; opacity: 0.9;" data-aos="fade-up" data-aos-delay="200">
                <span style="font-size: 0.875rem;">Join 50,000+ martial artists</span>
            </div>
        </div>
    </div>

    <!-- Right Panel - Forms -->
    <div style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 2rem;">
        <div style="width: 100%; max-width: 450px;" data-aos="fade-up">
            <!-- Mobile Back Link -->
            <a href="index.php" class="mobile-back-link" style="display: none; align-items: center; gap: 0.5rem; color: var(--color-muted-foreground); margin-bottom: 2rem; font-size: 0.875rem;">
                <i class="fas fa-arrow-left"></i>
                <span>Back to home</span>
            </a>

            <!-- Mode Toggle -->
            <div class="glass-card" style="display: flex; gap: 0.5rem; padding: 0.25rem; margin-bottom: 2rem;">
                <button 
                    class="mode-toggle-btn <?php echo $mode === 'login' ? 'active' : ''; ?>" 
                    data-mode="login"
                    style="flex: 1; padding: 0.875rem; border-radius: var(--radius-md); font-weight: 500; transition: all var(--transition-base); cursor: pointer; border: none;">
                    Sign In
                </button>
                <button 
                    class="mode-toggle-btn <?php echo $mode === 'register' ? 'active' : ''; ?>" 
                    data-mode="register"
                    style="flex: 1; padding: 0.875rem; border-radius: var(--radius-md); font-weight: 500; transition: all var(--transition-base); cursor: pointer; border: none;">
                    Create Account
                </button>
            </div>

            <!-- Login Form -->
            <div id="loginForm" class="auth-form" style="<?php echo $mode === 'register' ? 'display: none;' : ''; ?>">
                <h2 style="font-family: var(--font-display); font-size: 2rem; color: var(--color-foreground); margin-bottom: 0.5rem;">
                    Welcome Back
                </h2>
                <p style="color: var(--color-muted-foreground); margin-bottom: 2rem;">
                    Enter your credentials to access your account
                </p>

                <?php if ($login_error && $mode === 'login'): ?>
                <div style="padding: 1rem; background: #ef444420; border-left: 4px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444; font-weight: 600;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($login_error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <input type="hidden" name="action" value="login">

                    <!-- Email -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="login_email" class="form-label">Email Address</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="email" 
                                id="login_email" 
                                name="email" 
                                class="form-input" 
                                style="padding-left: 3rem;"
                                placeholder="you@findmydojo.com"
                                required
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="login_password" class="form-label">Password</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="password" 
                                id="login_password" 
                                name="password" 
                                class="form-input" 
                                style="padding-left: 3rem; padding-right: 3rem;"
                                placeholder="••••••••"
                                required
                            >
                            <button type="button" data-password-toggle="login_password" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-muted-foreground); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember" style="width: 1rem; height: 1rem; accent-color: var(--color-primary);">
                            <span style="font-size: 0.875rem; color: var(--color-muted-foreground);">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>
                </form>
            </div>

            <!-- Register Form -->
            <div id="registerForm" class="auth-form" style="<?php echo $mode === 'login' ? 'display: none;' : ''; ?>">
                <h2 style="font-family: var(--font-display); font-size: 2rem; color: var(--color-foreground); margin-bottom: 0.5rem;">
                    Create Account
                </h2>
                <p style="color: var(--color-muted-foreground); margin-bottom: 2rem;">
                    Fill in your details to get started
                </p>

                <?php if ($register_error && $mode === 'register'): ?>
                <div style="padding: 1rem; background: #ef444420; border-left: 4px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #ef4444; font-weight: 600;">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($register_error); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    <input type="hidden" name="action" value="register">

                    <!-- Full Name -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="register_name" class="form-label">Full Name</label>
                        <div style="position: relative;">
                            <i class="fas fa-user" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="text" 
                                id="register_name" 
                                name="name" 
                                class="form-input" 
                                style="padding-left: 3rem;"
                                placeholder="Izuku Midoriya"
                                required
                            >
                        </div>
                    </div>

                    <!-- User Type -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="user_type" class="form-label">I am a...</label>
                        <select id="user_type" name="user_type" class="form-select" required>
                            <option value="student">Student / Practitioner</option>
                            <option value="owner">Dojo Owner / Instructor</option>
                        </select>
                    </div>

                    <!-- Email -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="register_email" class="form-label">Email Address</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="email" 
                                id="register_email" 
                                name="email" 
                                class="form-input" 
                                style="padding-left: 3rem;"
                                placeholder="izuku@findmydojo.com"
                                required
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="register_password" class="form-label">Password</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="password" 
                                id="register_password" 
                                name="password" 
                                class="form-input" 
                                style="padding-left: 3rem; padding-right: 3rem;"
                                placeholder="••••••••"
                                data-password
                                minlength="8"
                                required
                            >
                            <button type="button" data-password-toggle="register_password" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-muted-foreground); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group" style="margin-bottom: 1.25rem;">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-muted-foreground);"></i>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                class="form-input" 
                                style="padding-left: 3rem;"
                                placeholder="••••••••"
                                data-confirm="#register_password"
                                required
                            >
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
                        <i class="fas fa-sparkles"></i>
                        Create Account
                    </button>

                    <!-- Terms -->
                    <p style="font-size: 0.75rem; text-align: center; color: var(--color-muted-foreground);">
                        By creating an account, you agree to our Terms of Service and Privacy Policy
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for Auth Page -->
<style>
@media (min-width: 1024px) {
    .auth-left-panel {
        display: block !important;
    }
}

@media (max-width: 1023px) {
    .mobile-back-link {
        display: flex !important;
    }
}

.mode-toggle-btn {
    background: transparent;
    color: var(--color-muted-foreground);
}

.mode-toggle-btn.active {
    background: var(--gradient-button);
    color: white;
    box-shadow: var(--shadow-md);
}

.auth-form {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}
</style>

<!-- Custom JavaScript for Mode Toggle -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeButtons = document.querySelectorAll('.mode-toggle-btn');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    modeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const mode = this.getAttribute('data-mode');
            
            modeButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            if (mode === 'login') {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
                window.history.pushState({}, '', 'auth.php');
            } else {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
                window.history.pushState({}, '', 'auth.php?mode=register');
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>