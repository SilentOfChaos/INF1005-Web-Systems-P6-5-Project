<?php
session_start();

$errorMsg = '';
$success = true;

$loginId = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$_SESSION['old_login'] = ['email' => $loginId];

if ($loginId === '') {
    $errorMsg = 'Please enter your email address or username.';
    $success = false;
}

if ($success && $password === '') {
    $errorMsg = 'Password is required.';
    $success = false;
}

if ($success) {
    authenticateUser();
}

if ($success) {
    unset($_SESSION['old_login']);
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['role'] = $role;
    $_SESSION['success'] = 'You are now logged in.';

    header('Location: profiles.php');
    exit;
}

$_SESSION['error'] = $errorMsg;
header('Location: login.php');
exit;

function resolveDbConfigPath(): ?string
{
    $candidates = [
        __DIR__ . '/../config/db.php',
        dirname(__DIR__) . '/config/db.php',
        '/var/www/config/db.php',
    ];

    foreach ($candidates as $path) {
        if (is_file($path)) {
            return $path;
        }
    }

    return null;
}

function loadPdo(string &$errorMsg): ?PDO
{
    $dbPath = resolveDbConfigPath();
    if ($dbPath === null) {
        $errorMsg = 'Database configuration file not found on server.';
        return null;
    }

    // Use require (not require_once) to avoid stale include-state across entry points.
    require $dbPath;

    if (!isset($pdo) || !($pdo instanceof PDO)) {
        $errorMsg = 'Database connection not initialized.';
        return null;
    }

    return $pdo;
}

function authenticateUser(): void
{
    global $firstName, $lastName, $email, $password, $username, $role, $errorMsg, $success, $loginId, $userId;

    $pdo = loadPdo($errorMsg);
    if ($pdo === null) {
        $success = false;
        return;
    }

    try {
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, username, email, password_hash, role FROM users WHERE email = ? OR username = ? LIMIT 1');
        $stmt->execute([$loginId, $loginId]);
        $row = $stmt->fetch();

        if (!$row || !password_verify($password, $row['password_hash'])) {
            $errorMsg = 'Login credentials are invalid.';
            $success = false;
            return;
        }

        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $username = $row['username'];
        $email = $row['email'];
        $role = $row['role'];
        $userId = $row['id'];
    } catch (PDOException $e) {
        $errorMsg = 'Database error: ' . $e->getMessage();
        $success = false;
    }
}
?>