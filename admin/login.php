<?php
/**
 * Admin Login
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
    exit();
}

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = sanitize($_POST['password'] ?? '');
    
    // Default credentials (for testing only - remove in production)
    $default_username = 'bhavesh';
    $default_password = 'bhavesh';
    
    // Check against default credentials first
    if ($username === $default_username && $password === $default_password) {
        $_SESSION['admin_id'] = 0; // Temporary ID for default user
        $_SESSION['admin_username'] = $default_username;
        $_SESSION['is_default_admin'] = true;
        redirect('index.php');
        exit();
    }
    
    // Check against database
    $admin = getAdminByUsername($username);
    
    if ($admin) {
        // Verify password against hashed password in database
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['is_default_admin'] = false;
            redirect('index.php');
            exit();
        }
    }
    
    // If we get here, credentials were invalid
    $error = 'Invalid username or password';
}

require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0 text-center">Admin Login</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>