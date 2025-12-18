<?php
/**
 * FindMyDojo - Contact Page
 * Contact form with database storage + email notification to admin
 */

$page_title = 'Contact Us';
$page_description = 'Get in touch with FindMyDojo. We\'re here to help with any questions or feedback.';
$page_scripts = ['forms.js'];

require_once 'includes/header.php';

$form_submitted = false;
$form_error = false;
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $subject_option = $_POST['subject'] ?? '';
        $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        if (!$name || !$email || !$subject_option || !$message) {
            $form_error = true;
            $error_message = 'Please fill in all required fields.';
        } else {
            try {
                // Map subject
                $subjects = [
                    'general' => 'General Inquiry',
                    'support' => 'Technical Support',
                    'partnership' => 'Partnership Opportunity',
                    'dojo' => 'Dojo Listing Help',
                    'feedback' => 'Feedback',
                    'other' => 'Other'
                ];
                $subject = $subjects[$subject_option] ?? 'Contact Form Message';

                // 1. Save to database
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $subject, $message]);

                // 2. Send email to admin
                $admin_email = 'admin@findmydojo.com'; // CHANGE TO YOUR REAL EMAIL
                $email_subject = "[FindMyDojo] New Message: $subject";
                $email_body = "
                <html>
                <body>
                    <h2>New Contact Form Submission</h2>
                    <p><strong>From:</strong> $name &lt;$email&gt;</p>
                    <p><strong>Subject:</strong> $subject</p>
                    <p><strong>Message:</strong></p>
                    <p>" . nl2br(htmlspecialchars($message)) . "</p>
                    <hr>
                    <small>Sent on " . date('F j, Y \a\t g:i A') . "</small>
                </body>
                </html>
                ";

                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=utf-8\r\n";
                $headers .= "From: no-reply@findmydojo.com\r\n";
                $headers .= "Reply-To: $email\r\n";

                // Try to send email
                $email_sent = mail($admin_email, $email_subject, $email_body, $headers);

                $form_submitted = true;
                $success_message = 'Thank you! Your message has been sent and saved. We\'ll reply soon.';

                // Optional: Log if email failed (but message is still saved)
                if (!$email_sent) {
                    error_log("Contact form email failed to send to $admin_email");
                }

            } catch (Exception $e) {
                $form_error = true;
                $error_message = 'Something went wrong. Please try again later.';
                error_log("Contact form error: " . $e->getMessage());
            }
        }
    } else {
        $form_error = true;
        $error_message = 'Invalid request. Please try again.';
    }
}
?>

<div style="padding-top: 8rem; padding-bottom: 6rem;">
    <div class="container">
        <!-- Page Header -->
        <div class="text-center" style="margin-bottom: 4rem;" data-aos="fade-up">
            <h1 class="section-title">
                <span style="color: var(--color-foreground);">GET IN</span>
                <span class="gradient-text">TOUCH</span>
            </h1>
            <p class="section-description">
                Have questions or feedback? We'd love to hear from you. 
                Our team is always ready to help.
            </p>
        </div>

        <!-- Success / Error Messages -->
        <?php if ($form_submitted): ?>
        <div class="text-center" style="max-width: 700px; margin: 0 auto 3rem;">
            <div class="glass-card" style="padding: 2.5rem; background: #22c55e15; border: 2px solid #22c55e;">
                <i class="fas fa-check-circle" style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;"></i>
                <h3 style="color: #22c55e; font-size: 1.75rem; margin-bottom: 0.5rem;">Message Sent!</h3>
                <p style="color: var(--color-foreground); font-size: 1.125rem;"><?php echo $success_message; ?></p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($form_error): ?>
        <div class="text-center" style="max-width: 700px; margin: 0 auto 2rem;">
            <div class="glass-card" style="padding: 1.5rem; background: #ef444415; border-left: 4px solid #ef4444;">
                <i class="fas fa-exclamation-triangle" style="color: #ef4444; margin-right: 0.5rem;"></i>
                <span style="color: #ef4444; font-weight: 600;"><?php echo $error_message; ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 2rem; max-width: 1200px; margin: 0 auto;">
            <!-- Contact Info Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;" data-aos="fade-right" data-aos-delay="100">
                <!-- Email Card -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; border-radius: var(--radius-md); background: var(--gradient-button); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-envelope" style="font-size: 1.25rem; color: white;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.125rem; color: var(--color-foreground); margin-bottom: 0.25rem;">Email Us</h3>
                            <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">For general inquiries</p>
                            <a href="mailto:hello@findmydojo.com" style="color: var(--color-primary); font-weight: 500;">
                                hello@findmydojo.com
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Phone Card -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; border-radius: var(--radius-md); background: var(--gradient-button); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-phone" style="font-size: 1.25rem; color: white;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.125rem; color: var(--color-foreground); margin-bottom: 0.25rem;">Call Us</h3>
                            <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">Mon-Fri, 9am-6pm PST</p>
                            <a href="tel:+22780858585" style="color: var(--color-primary); font-weight: 500;">
                                +227 80 85 85 85
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; border-radius: var(--radius-md); background: var(--gradient-button); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt" style="font-size: 1.25rem; color: white;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.125rem; color: var(--color-foreground); margin-bottom: 0.25rem;">Visit Us</h3>
                            <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">Our headquarters</p>
                            <p style="color: var(--color-foreground); font-size: 0.875rem;">
                                123 Martial Arts Way<br>
                                Niamey, CA 94102
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Response Time Card -->
                <div class="glass-card" style="padding: 1.5rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <div style="width: 3rem; height: 3rem; border-radius: var(--radius-md); background: var(--gradient-button); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-clock" style="font-size: 1.25rem; color: white;"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="font-size: 1.125rem; color: var(--color-foreground); margin-bottom: 0.25rem;">Response Time</h3>
                            <p style="font-size: 0.875rem; color: var(--color-muted-foreground); margin-bottom: 0.5rem;">Average reply time</p>
                            <p style="color: var(--color-primary); font-weight: 600;">Within 24 hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="glass-card" style="padding: 2rem;" data-aos="fade-left" data-aos-delay="200">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                    <i class="fas fa-comment-dots" style="font-size: 1.5rem; color: var(--color-primary);"></i>
                    <h2 style="font-size: 1.5rem; color: var(--color-foreground);">Send a Message</h2>
                </div>

                <form method="POST" action="" data-validate>
                    <input type="hidden" name="csrf_token" value="<?php echo get_csrf_token(); ?>">
                    
                    <div class="grid grid-cols-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div class="form-group">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" id="name" name="name" class="form-input" placeholder="Izuku Midoriya" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="you@gmail.com" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="subject" class="form-label">Subject</label>
                        <select id="subject" name="subject" class="form-select" required>
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="partnership">Partnership Opportunity</option>
                            <option value="dojo">Dojo Listing Help</option>
                            <option value="feedback">Feedback</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label for="message" class="form-label">Message</label>
                        <textarea id="message" name="message" class="form-textarea" rows="6" placeholder="How can we help you?" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>