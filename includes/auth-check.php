<?php
// Lightweight auth helpers used across pages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function ensure_logged_in() {
    if (empty($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}

function ensure_admin() {
    ensure_logged_in();
    if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: /login.php');
        exit;
    }
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_role(): ?string {
    return isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;
}

?>
