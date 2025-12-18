<?php
/**
 * FindMyDojo - Admin Contact Messages Viewer
 * View all messages from the contact form
 */

require_once ('../includes/config.php');  // Adjust path if needed

// Security: Only allow admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../dashboard.php');
    exit;
}

$page_title = 'Contact Messages';

// Fetch all messages ordered by newest first
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error loading messages: " . $e->getMessage();
    $messages = [];
}

require_once '../includes/header.php';
?>

<div style="padding-top: 8rem; padding-bottom: 6rem;">
    <div class="container">
        <!-- Page Header -->
        <div style="margin-bottom: 3rem;" data-aos="fade-up">
            <a href="../dashboard.php" class="btn btn-outline" style="margin-bottom: 1.5rem;">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1 class="section-title">Contact Messages</h1>
            <p class="section-description">
                All messages submitted through the contact form
            </p>
        </div>

        <?php if (isset($error)): ?>
        <div style="padding: 1.5rem; background: #ef444420; border-left: 4px solid #ef4444; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444; margin-right: 0.5rem;"></i>
            <span style="color: #ef4444; font-weight: 600;"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <?php if (empty($messages)): ?>
        <div class="glass-card" style="padding: 4rem; text-align: center;">
            <i class="fas fa-envelope-open-text" style="font-size: 4rem; color: var(--color-muted-foreground); margin-bottom: 1.5rem;"></i>
            <h3 style="color: var(--color-foreground); margin-bottom: 0.5rem;">No Messages Yet</h3>
            <p style="color: var(--color-muted-foreground);">When users submit the contact form, their messages will appear here.</p>
        </div>
        <?php else: ?>
        <div class="glass-card" style="padding: 0; overflow: hidden;">
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--color-muted);">
                            <th style="padding: 1.25rem; text-align: left; font-weight: 600; color: var(--color-foreground);">Date</th>
                            <th style="padding: 1.25rem; text-align: left; font-weight: 600; color: var(--color-foreground);">Name</th>
                            <th style="padding: 1.25rem; text-align: left; font-weight: 600; color: var(--color-foreground);">Email</th>
                            <th style="padding: 1.25rem; text-align: left; font-weight: 600; color: var(--color-foreground);">Subject</th>
                            <th style="padding: 1.25rem; text-align: left; font-weight: 600; color: var(--color-foreground);">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                        <tr style="border-bottom: 1px solid var(--color-border); transition: background 0.2s;">
                            <td style="padding: 1.25rem; color: var(--color-muted-foreground); font-size: 0.875rem;">
                                <?php echo date('M j, Y \<\b\r\> g:i A', strtotime($msg['created_at'])); ?>
                            </td>
                            <td style="padding: 1.25rem; color: var(--color-foreground);">
                                <?php echo htmlspecialchars($msg['name']); ?>
                            </td>
                            <td style="padding: 1.25rem;">
                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: var(--color-primary); text-decoration: underline;">
                                    <?php echo htmlspecialchars($msg['email']); ?>
                                </a>
                            </td>
                            <td style="padding: 1.25rem; color: var(--color-foreground);">
                                <?php echo htmlspecialchars($msg['subject']); ?>
                            </td>
                            <td style="padding: 1.25rem; color: var(--color-muted-foreground); max-width: 400px;">
                                <?php echo nl2br(htmlspecialchars(substr($msg['message'], 0, 200))) . (strlen($msg['message']) > 200 ? '...' : ''); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>