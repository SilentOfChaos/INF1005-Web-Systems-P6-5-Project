<?php
session_start();

$errorMsg = '';
$success = true;

$email = trim((string) ($_POST['email'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

$_SESSION['old_login'] = ['email' => $email];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMsg = 'Please enter a valid email address.';
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
    $_SESSION['user_email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['role'] = $role;
    $_SESSION['success'] = 'You are now logged in.';

    header('Location: index.php');
    exit;
}

$_SESSION['error'] = $errorMsg;
header('Location: login.php');
exit;

function authenticateUser(): void
{
    global $firstName, $lastName, $email, $password, $username, $role, $errorMsg, $success;

    // Use Member 1's DB connection file
    require_once '/var/www/config/db.php';
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT first_name, last_name, username, email, password_hash, role FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if ($row) {
            $firstName = $row['first_name'];
            $lastName = $row['last_name'];
            $username = $row['username'];
            $role = $row['role'];

            // Verify the hashed password
            if (!password_verify($password, $row['password_hash'])) {
                $errorMsg = "Email not found or password doesn't match.";
                $success = false;
            }
        } else {
            $errorMsg = "Email not found or password doesn't match.";
            $success = false;
        }
    } catch (PDOException $e) {
        $errorMsg = 'Database error: ' . $e->getMessage();
        $success = false;
    }
}
?>