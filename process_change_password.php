<?php
require_once __DIR__ . '/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['password_update_error'] = "You must be logged in to change your password.";
    header("Location: " . SITE_URL . "login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';
    $errors = [];

    if (empty($current_password)) $errors[] = "Current password is required.";
    if (empty($new_password)) {
        $errors[] = "New password is required.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "New password must be at least 8 characters long.";
    }
    if ($new_password !== $confirm_new_password) {
        $errors[] = "New passwords do not match.";
    }

    if (empty($errors)) {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
            $stmt->execute([':user_id' => $user_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Current password is correct, hash and update new password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash, updated_at = NOW() WHERE user_id = :user_id");
                $update_stmt->execute([
                    ':password_hash' => $new_password_hash,
                    ':user_id' => $user_id
                ]);
                $_SESSION['password_update_success'] = "Password changed successfully!";
            } else {
                $errors[] = "Incorrect current password.";
            }
        } catch (PDOException $e) {
            error_log("Change Password Error: " . $e->getMessage());
            $errors[] = "An error occurred while changing your password. Please try again.";
        }
    }
    
    if (!empty($errors)) {
        $_SESSION['password_update_error'] = implode("<br>", array_map('e', $errors));
    }

} else {
    $_SESSION['password_update_error'] = "Invalid request method.";
}

header("Location: " . SITE_URL . "edit_profile.php");
exit;
?>