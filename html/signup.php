<?php
session_start();

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$old = $_SESSION['old_signup'] ?? [
    'firstName' => '',
    'lastName' => '',
    'username' => '',
    'email' => '',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign up for Singapore Singles Society">
    <meta name="author" content="Singapore Singles Society— INF1005">
    <title>Sign Up - Singapore Singles Society: S³</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container" aria-label="Main navigation">
        <nav class="custom-navbar">
            <a href="index.php" class="brand-logo" aria-label="Singapore Singles Society home">S³</a>
            
            <div class="nav-center">
                <a href="index.php">Home</a>
                <a href="about.php">About</a>
                <a href="#">Pricing</a>
            </div>

            <div class="nav-right">
                <a href="login.php" class="btn-outline-custom">Log In</a>
                <a href="signup.php" class="btn-solid-custom">Join Now &rarr;</a>
            </div>
        </nav>
    </div>

    <main class="container my-4" style="flex: 1;">
        <div class="text-center mb-4">
            <h1 class="display-6 fw-bold text-dark">Join S³ ✨</h1>
            <p class="lead text-muted">Join a safe, secure, and fun environment designed to help you connect with people who share your vibe.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-lg border-0" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4 fw-bold" style="color: var(--text-dark);">Create Your Profile</h2>

                        <?php if ($error !== ''): ?>
                            <div class="alert alert-danger rounded-4" role="alert"><?= h($error) ?></div>
                        <?php endif; ?>

                        <form id="signupForm" action="process_register.php" method="POST" novalidate>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="firstName" class="form-label fw-bold text-muted">First Name</label>
                                    <input type="text" class="form-control form-control-lg rounded-3 bg-light border-0" id="firstName" name="firstName" value="<?= h((string) $old['firstName']) ?>" required>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="lastName" class="form-label fw-bold text-muted">Last Name</label>
                                    <input type="text" class="form-control form-control-lg rounded-3 bg-light border-0" id="lastName" name="lastName" value="<?= h((string) $old['lastName']) ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label fw-bold text-muted">Username</label>
                                <input type="text" class="form-control form-control-lg rounded-3 bg-light border-0" id="username" name="username" value="<?= h((string) $old['username']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold text-muted">Email address</label>
                                <input type="email" class="form-control form-control-lg rounded-3 bg-light border-0" id="email" name="email" value="<?= h((string) $old['email']) ?>" placeholder="you@email.com" required>
                            </div>
                            <div class="row">
                                <div class="mb-4 col-md-6">
                                    <label for="password" class="form-label fw-bold text-muted">Password</label>
                                    <input type="password" class="form-control form-control-lg rounded-3 bg-light border-0" id="password" name="password" minlength="8" required>
                                    <div class="form-text mt-2">Must be at least 8 characters long.</div>
                                </div>
                                <div class="mb-4 col-md-6">
                                    <label for="confirmPassword" class="form-label fw-bold text-muted">Confirm Password</label>
                                    <input type="password" class="form-control form-control-lg rounded-3 bg-light border-0" id="confirmPassword" name="confirmPassword" required>
                                    <div id="passwordError" class="text-danger mt-1 d-none" style="font-size: 0.85rem;">Passwords do not match.</div>
                                </div>
                            </div>
                            <button type="submit" class="btn-solid-custom w-100 d-block text-center mt-2" style="font-size: 1.1rem; padding: 0.8rem;">Create Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="custom-footer mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <a href="index.php" class="brand-logo d-inline-block mb-3">S³</a>
                    <p class="text-muted pe-md-5">A safe, fun space to meet people who share your vibe. Based in Singapore.</p>
                </div>
                
                <div class="col-md-2 col-6 mb-4 mb-md-0">
                    <h5 class="footer-heading">Explore</h5>
                    <a href="#" class="footer-link">Browse Profiles</a>
                    <a href="#" class="footer-link">Pricing</a>
                    <a href="login.php" class="footer-link">Sign In</a>
                </div>
                
                <div class="col-md-2 col-6 mb-4 mb-md-0">
                    <h5 class="footer-heading">Company</h5>
                    <a href="about.php" class="footer-link">About Us</a>
                    <a href="#" class="footer-link">Blog</a>
                    <a href="#" class="footer-link">Careers</a>
                </div>
                
                <div class="col-md-3 col-12">
                    <h5 class="footer-heading">Support</h5>
                    <a href="#" class="footer-link">Safety Centre</a>
                    <a href="#" class="footer-link">Privacy Policy</a>
                    <a href="#" class="footer-link">Terms of Service</a>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="text-center text-muted" style="font-size: 0.9rem;">
                <p class="mb-0">&copy; 2026 Singapore Singles Society S³. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>
</body>
</html>