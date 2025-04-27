<?php
/**
 * Admin Settings
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$adminId = $_SESSION['admin_id'];
$admin = getAdminById($adminId);
$errors = [];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $data = [
        'full_name' => sanitize($_POST['full_name']),
        'email' => sanitize($_POST['email']),
        'username' => sanitize($_POST['username'])
    ];

    // Validate
    if (empty($data['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }

    if (empty($errors)) {
        if (updateAdminProfile($adminId, $data)) {
            $_SESSION['admin_username'] = $data['username'];
            setFlashMessage('success', 'Profile updated successfully');
            redirect('settings.php');
        } else {
            $errors['profile'] = 'Failed to update profile';
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate
    if (!password_verify($current_password, $admin['password'])) {
        $errors['current_password'] = 'Current password is incorrect';
    }

    if (strlen($new_password) < 8) {
        $errors['new_password'] = 'Password must be at least 8 characters';
    }

    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if (updateAdminPassword($adminId, $hashed_password)) {
            setFlashMessage('success', 'Password changed successfully');
            redirect('settings.php');
        } else {
            $errors['password'] = 'Failed to change password';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h2>Admin Settings</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors['profile'])): ?>
                        <div class="alert alert-danger"><?= $errors['profile'] ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="update_profile" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="full_name" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($admin['full_name'] ?? '') ?>" required>
                            <?php if (isset($errors['full_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username *</label>
                            <input type="text" name="username" class="form-control" 
                                   value="<?= htmlspecialchars($admin['username'] ?? '') ?>" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors['password'])): ?>
                        <div class="alert alert-danger"><?= $errors['password'] ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password *</label>
                            <input type="password" name="current_password" class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['current_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['current_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password *</label>
                            <input type="password" name="new_password" class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['new_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['new_password'] ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Minimum 8 characters</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password *</label>
                            <input type="password" name="confirm_password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" required>
                            <?php if (isset($errors['confirm_password'])): ?>
                                <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">System Settings</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="save-settings.php">
                        <div class="mb-3">
                            <label class="form-label">Site Title</label>
                            <input type="text" name="site_title" class="form-control" 
                                   value="<?= htmlspecialchars(getSetting('site_title')) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Admin Email</label>
                            <input type="email" name="admin_email" class="form-control" 
                                   value="<?= htmlspecialchars(getSetting('admin_email')) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jobs Per Page</label>
                            <input type="number" name="jobs_per_page" class="form-control" 
                                   value="<?= htmlspecialchars(getSetting('jobs_per_page', 10)) ?>">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="registration_enabled" 
                                   id="registration_enabled" <?= getSetting('registration_enabled', 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="registration_enabled">Enable User Registration</label>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>