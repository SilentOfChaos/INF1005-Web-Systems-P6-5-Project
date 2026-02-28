<?php
session_start();

$errorMsg = '';
$success = true;

$firstName = trim((string) ($_POST['firstName'] ?? ''));
$lastName = trim((string) ($_POST['lastName'] ?? ''));
$username = trim((string) ($_POST['username'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$confirmPassword = (string) ($_POST['confirmPassword'] ?? '');

$_SESSION['old_signup'] = [
    'firstName' => $firstName,
    'lastName' => $lastName,
    'username' => $username,
    'email' => $email,
];

if ($firstName === '' || !preg_match('/^[A-Za-z\-\s]{1,100}$/', $firstName)) {
    $errorMsg = 'First name must be 1-100 characters and only contain letters, spaces, or hyphens.';
    $success = false;
}

if ($success && ($lastName === '' || !preg_match('/^[A-Za-z\-\s]{1,100}$/', $lastName))) {
    $errorMsg = 'Last name must be 1-100 characters and only contain letters, spaces, or hyphens.';
    $success = false;
}

if ($success && !preg_match('/^[A-Za-z0-9_]{3,30}$/', $username)) {
    $errorMsg = 'Username must be 3-30 characters and only contain letters, numbers, and underscores.';
    $success = false;
}

if ($success && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMsg = 'Please provide a valid email address.';
    $success = false;
}

if ($success && strlen($password) < 8) {
    $errorMsg = 'Password must be at least 8 characters long.';
    $success = false;
}

if ($success && $password !== $confirmPassword) {
    $errorMsg = 'Passwords do not match.';
    $success = false;
}

if ($success) {
    saveMemberToDB();
}

if ($success) {
    unset($_SESSION['old_signup']);
    $_SESSION['success'] = 'Account created successfully. Please log in.';
    header('Location: login.php');
    exit;
}

$_SESSION['error'] = $errorMsg;
header('Location: signup.php');
exit;

function saveMemberToDB(): void
{
    global $firstName, $lastName, $username, $email, $password, $errorMsg, $success;

    // Use Member 1's DB connection file
    require_once '/var/www/config/db.php';
    global $pdo;

    try {
        // Check if username or email already exists
        $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1');
        $checkStmt->execute([$email, $username]);

        if ($checkStmt->fetch()) {
            $errorMsg = 'An account with this username or email already exists.';
            $success = false;
            return;
        }

        // Hash the password securely
        $pwdHashed = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';

        // Insert new user into the database
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, username, email, password_hash, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([$firstName, $lastName, $username, $email, $pwdHashed, $role]);

    } catch (PDOException $e) {
        $errorMsg = 'Database error: ' . $e->getMessage();
        $success = false;
    }
}
?>