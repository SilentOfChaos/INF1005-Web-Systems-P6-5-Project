<?php
// ── header.inc.php ──
// Usage: include this at the top of every page.
//
// To highlight the correct nav link, define $activePage before including:
//   $activePage = 'discover';   // 'home' | 'discover' | 'matches' | 'messages' | 'profile' | 'about' | 'pricing'
//
// Example:
//   <?php $activePage = 'discover'; require_once 'includes/header.inc.php'; 

if (!isset($activePage)) $activePage = '';

// Helper — echo HTML-escaped string
if (!function_exists('h')) {
    function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
}

$loggedIn = isset($_SESSION['user_id']);

// Helper — returns 'active' + aria-current if page matches
function navClass(string $page, string $active): string {
    if ($page === $active) {
        return ' class="active" aria-current="page"';
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? h($pageTitle) . ' – Singapore Singles Society' : 'Singapore Singles Society' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/css/styles.css">
    <?= isset($extraHead) ? $extraHead : '' ?>
</head>
<body>

<div class="navbar-wrapper">
    <div class="container" aria-label="Main navigation">
        <nav class="custom-navbar">

            <!-- Brand -->
            <a href="index.php" class="brand-logo" aria-label="Singapore Singles Society home">S3</a>

            <!-- Centre nav links -->
            <div class="nav-center">
                <a href="index.php"<?= navClass('home', $activePage) ?>>Home</a>

                <?php if ($loggedIn): ?>
                    <!-- Logged-in only links -->
                    <a href="profiles.php"<?= navClass('discover', $activePage) ?>>Discover</a>
                    <a href="matches.php"<?= navClass('matches',  $activePage) ?>>Matches</a>
                    <a href="messages.php"<?= navClass('messages', $activePage) ?>>Messages</a>
                <?php endif; ?>

                <a href="about.php"<?= navClass('about', $activePage) ?>>About</a>
                <a href="pricing.php"<?= navClass('pricing', $activePage) ?>>Pricing</a>
            </div>

            <!-- Right side -->
            <div class="nav-right">
                <?php if ($loggedIn): ?>
                    <!-- Logged-in: profile button + dropdown -->
                    <a href="edit-profile.php"
                       class="btn-outline-custom<?= $activePage === 'profile' ? ' active' : '' ?>"
                       aria-label="My profile">
                        My Profile
                    </a>

                    <div class="dropdown">
                        <a class="btn-solid-custom dropdown-toggle"
                           href="#"
                           role="button"
                           data-bs-toggle="dropdown"
                           aria-expanded="false"
                           aria-label="Account menu for <?= h($_SESSION['username'] ?? 'User') ?>">
                            <?= h($_SESSION['username'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="edit-profile.php">
                                    <span aria-hidden="true">👤</span> My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="matches.php">
                                    <span aria-hidden="true">💛</span> My Matches
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="messages.php">
                                    <span aria-hidden="true">💬</span> Messages
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="logout.php">
                                    <span aria-hidden="true">🚪</span> Logout
                                </a>
                            </li>
                        </ul>
                    </div>

                <?php else: ?>
                    <!-- Logged-out: login + join -->
                    <a href="login.php"  class="btn-outline-custom">Log In</a>
                    <a href="signup.php" class="btn-solid-custom">Join Now &rarr;</a>
                <?php endif; ?>
            </div>

        </nav>
    </div>
</div>