<?php
require '../config.php'; // Go up one directory

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'register') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Basic Validation
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            $_SESSION['message'] = "All fields are required.";
            $_SESSION['message_type'] = "danger";
            redirect('register.php');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Invalid email format.";
            $_SESSION['message_type'] = "danger";
            redirect('register.php');
        }
        if (strlen($password) < 6) {
            $_SESSION['message'] = "Password must be at least 6 characters long.";
            $_SESSION['message_type'] = "danger";
            redirect('register.php');
        }
        if ($password !== $confirm_password) {
            $_SESSION['message'] = "Passwords do not match.";
            $_SESSION['message_type'] = "danger";
            redirect('register.php');
        }

        // Check if email already exists
        try {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            if ($stmt->fetch()) {
                $_SESSION['message'] = "Email already registered.";
                $_SESSION['message_type'] = "danger";
                redirect('register.php');
            }

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $insertStmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, is_verified) VALUES (:first_name, :last_name, :email, :password_hash, 0)"); // is_verified = 0 by default
            $insertStmt->bindParam(':first_name', $first_name);
            $insertStmt->bindParam(':last_name', $last_name);
            $insertStmt->bindParam(':email', $email);
            $insertStmt->bindParam(':password_hash', $password_hash);
            $insertStmt->execute();

            $_SESSION['message'] = "Registration successful! Please login.";
            $_SESSION['message_type'] = "success";
            redirect('login.php');

        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $_SESSION['message'] = "An error occurred during registration. Please try again.";
            $_SESSION['message_type'] = "danger";
            redirect('register.php');
        }

    } elseif ($action === 'login') {
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['message'] = "Email and password are required.";
            $_SESSION['message_type'] = "danger";
            redirect('login.php');
        }

        try {
            $stmt = $pdo->prepare("SELECT user_id, email, password_hash, first_name, last_name, is_admin FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct, set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_first_name'] = $user['first_name'];
                $_SESSION['user_last_name'] = $user['last_name'];
                $_SESSION['is_admin'] = (bool)$user['is_admin'];

                $_SESSION['message'] = "Login successful! Welcome back, " . htmlspecialchars($user['first_name']) . ".";
                $_SESSION['message_type'] = "success";
                redirect('../index.php'); // Redirect to homepage
            } else {
                $_SESSION['message'] = "Invalid email or password.";
                $_SESSION['message_type'] = "danger";
                redirect('login.php');
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $_SESSION['message'] = "An error occurred during login. Please try again.";
            $_SESSION['message_type'] = "danger";
            redirect('login.php');
        }
    } else {
        $_SESSION['message'] = "Invalid action.";
        $_SESSION['message_type'] = "danger";
        redirect('../index.php');
    }
} else {
    redirect('../index.php'); // No direct access
}
?>