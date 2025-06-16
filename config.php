<?php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'solemate_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed. Please check server logs or contact support. Error details: " . $e->getMessage());
}

if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'SoleMate');
}

if (!defined('SITE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];

    $project_folder_name = 'SoleMate-final-master'; // <-- ADJUST THIS IF YOUR PROJECT FOLDER IS DIFFERENT, OR '' IF IN ROOT

    $base_path = '';
    if (!empty($project_folder_name)) {
        $script_name_parts = explode('/', dirname($_SERVER['SCRIPT_NAME']));
        if (isset($script_name_parts[1]) && $script_name_parts[1] === $project_folder_name) {
            $base_path = '/' . $project_folder_name;
        } else if (empty($script_name_parts[1]) && empty($project_folder_name)) {
             $base_path = '';
        } else {
            $base_path = '/' . $project_folder_name;
        }
    }
    if (empty($project_folder_name)) {
        $base_path = '';
    }

    define('SITE_URL', rtrim($protocol . $host . $base_path, '/') . '/');
}

function e($string) {
    if ($string === null) return '';
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>