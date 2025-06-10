<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($path_prefix)) {
    $doc_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
    $current_script_dir = rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '/');
    $relative_path_from_root = ltrim(str_replace($doc_root, '', $current_script_dir), '/');
    $depth = empty($relative_path_from_root) ? 0 : substr_count($relative_path_from_root, '/') + 1;
    $path_prefix = str_repeat('../', $depth);
    if ($current_script_dir === $doc_root) {
        $path_prefix = '';
    }
}

require_once __DIR__ . '/../config.php'; // Go up one level to find config.php from templates

$current_page_basename = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) : e(SITE_NAME); ?></title>
    <link rel="stylesheet" href="<?php echo e($path_prefix); ?>style.css?v=<?php echo time(); ?>">
    <link rel="icon" href="<?php echo e($path_prefix); ?>assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php require_once __DIR__ . '/promotion_bar.php'; ?>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <div class="main-content-wrapper">
        <div class="container">
            <div class="main-content">