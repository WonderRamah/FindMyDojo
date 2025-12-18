<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dojo_owner') {
    header('Location: dashboard.php');
    exit;
}

$dojo_id = (int)($_POST['dojo_id'] ?? 0);

if ($dojo_id > 0 && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    try {
        $pdo->beginTransaction();

        // Check ownership
        $check = $pdo->prepare("SELECT dojo_id, dojo_image FROM dojos WHERE dojo_id = ? AND owner_id = ?");
        $check->execute([$dojo_id, $_SESSION['user_id']]);
        $dojo = $check->fetch();

        if ($dojo) {
            // Delete image
            if ($dojo['dojo_image']) {
                @unlink('assets/images/dojos/' . $dojo['dojo_image']);
            }

            // Delete related records
            $pdo->prepare("DELETE FROM dojo_styles WHERE dojo_id = ?")->execute([$dojo_id]);
            $pdo->prepare("DELETE FROM reviews WHERE dojo_id = ?")->execute([$dojo_id]); // Optional: keep reviews?
            $pdo->prepare("DELETE FROM dojos WHERE dojo_id = ?")->execute([$dojo_id]);

            $pdo->commit();
            header('Location: dashboard.php?deleted=success');
        } else {
            header('Location: dashboard.php?error=not_owner');
        }
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        header('Location: dashboard.php?error=failed');
        exit;
    }
}

header('Location: dashboard.php');
exit;