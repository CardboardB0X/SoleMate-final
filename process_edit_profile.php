<?php
require_once __DIR__ . '/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $_SESSION['profile_update_error'] = "You must be logged in to update your profile.";
    header("Location: " . SITE_URL . "login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $phone = trim($_POST['phone'] ?? '');
    $errors = [];

    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Check if new email is already taken by another user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :user_id");
        $stmt->execute([':email' => $email, ':user_id' => $user_id]);
        if ($stmt->fetch()) {
            $errors[] = "This email address is already in use by another account.";
        }
    }
    if (!empty($phone) && !preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $phone)) {
        $errors[] = "Invalid phone number format.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone, updated_at = NOW() WHERE user_id = :user_id");
            $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => !empty($phone) ? $phone : null,
                ':user_id' => $user_id
            ]);

            // Update session variables if changed
            $_SESSION['user_first_name'] = $first_name;
            $_SESSION['user_email'] = $email;

            $_SESSION['profile_update_success'] = "Profile updated successfully!";
        } catch (PDOException $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            $_SESSION['profile_update_error'] = "An error occurred while updating your profile. Please try again.";
        }
    } else {
        $_SESSION['profile_update_error'] = implode("<br>", array_map('e', $errors));
    }
} else {
    $_SESSION['profile_update_error'] = "Invalid request method.";
}

header("Location: " . SITE_URL . "edit_profile.php");
exit;
?>