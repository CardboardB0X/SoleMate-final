<?php
// process_login.php
require_once __DIR__ . '/config.php'; // Defines SITE_URL
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'An unexpected error occurred.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT user_id, email, password_hash, first_name, is_admin, is_verified FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Optional: Check is_verified
                // if (!$user['is_verified']) {
                //    $response['message'] = "Your account is not yet verified. Please check your email.";
                // } else {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_first_name'] = $user['first_name'];
                    $_SESSION['is_admin'] = (bool)$user['is_admin'];

                    $update_stmt = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE user_id = :user_id");
                    $update_stmt->execute([':user_id' => $user['user_id']]);
                    
                    $response['success'] = true;
                    $response['message'] = 'Login successful! Refreshing page...';
                    
                    // Determine redirect URL:
                    // 1. Use intended_url if set (e.g., from trying to access a protected page)
                    // 2. Default to the main site URL (homepage)
                    $response['redirect_url'] = $_SESSION['intended_url'] ?? SITE_URL; // SITE_URL should be your homepage
                    unset($_SESSION['intended_url']); // Clear it after use
                // }
            } else {
                $response['message'] = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login Process Error: " . $e->getMessage());
            $response['message'] = "Database error during login. Please try again.";
        }
    } else {
        $response['message'] = implode(' ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
exit;
?>