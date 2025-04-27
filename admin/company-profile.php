<?php
/**
 * Company Profile Management
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get company profile data
$companyId = 1; // Default company ID (adjust as needed)
$company = getCompanyProfile($companyId);
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => sanitize($_POST['name']),
        'description' => sanitize($_POST['description']),
        'website' => sanitize($_POST['website']),
        'email' => sanitize($_POST['email']),
        'phone' => sanitize($_POST['phone']),
        'address' => sanitize($_POST['address']),
        'city' => sanitize($_POST['city']),
        'state' => sanitize($_POST['state']),
        'country' => sanitize($_POST['country']),
        'postal_code' => sanitize($_POST['postal_code']),
        'industry' => sanitize($_POST['industry']),
        'founded_year' => (int)$_POST['founded_year'],
        'company_size' => sanitize($_POST['company_size'])
    ];

    // Validate required fields
    if (empty($data['name'])) {
        $errors['name'] = 'Company name is required';
    }

    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo = uploadFile($_FILES['logo'], LOGO_UPLOAD_PATH);
        if ($logo) {
            // Delete old logo if exists
            if (!empty($company['logo'])) {
                @unlink(LOGO_UPLOAD_PATH . $company['logo']);
            }
            $data['logo'] = $logo;
        } else {
            $errors['logo'] = 'Invalid logo file (only JPG/PNG allowed, max 1MB)';
        }
    }

    if (empty($errors)) {
        if (updateCompanyProfile($companyId, $data)) {
            setFlashMessage('success', 'Company profile updated successfully');
            redirect('company-profile.php');
        } else {
            $errors['general'] = 'Failed to update company profile';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h2>Company Profile</h2>
        </div>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?= $errors['general'] ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                   value="<?= htmlspecialchars($company['name'] ?? '') ?>" required>
                            <?php if (isset($errors['name'])): ?>
                                <div class="invalid-feedback"><?= $errors['name'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($company['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Website</label>
                                    <input type="url" name="website" class="form-control" 
                                           value="<?= htmlspecialchars($company['website'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= htmlspecialchars($company['email'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="phone" class="form-control" 
                                           value="<?= htmlspecialchars($company['phone'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Size</label>
                                    <input type="text" name="company_size" class="form-control" 
                                           value="<?= htmlspecialchars($company['company_size'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Industry</label>
                                    <input type="text" name="industry" class="form-control" 
                                           value="<?= htmlspecialchars($company['industry'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Founded Year</label>
                                    <input type="number" name="founded_year" class="form-control" 
                                           value="<?= htmlspecialchars($company['founded_year'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Address Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" 
                                   value="<?= htmlspecialchars($company['address'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control" 
                                           value="<?= htmlspecialchars($company['city'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">State/Province</label>
                                    <input type="text" name="state" class="form-control" 
                                           value="<?= htmlspecialchars($company['state'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" class="form-control" 
                                           value="<?= htmlspecialchars($company['postal_code'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" 
                                   value="<?= htmlspecialchars($company['country'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Company Logo</h5>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($company['logo'])): ?>
                            <img src="<?= SITE_URL . '/' . LOGO_UPLOAD_PATH . $company['logo'] ?>" 
                                 class="img-fluid mb-3" style="max-height: 200px;" alt="Company Logo">
                        <?php else: ?>
                            <div class="bg-light p-5 mb-3 text-muted">
                                <i class="fas fa-building fa-4x"></i>
                                <p class="mt-2">No logo uploaded</p>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Upload New Logo</label>
                            <input type="file" name="logo" class="form-control <?= isset($errors['logo']) ? 'is-invalid' : '' ?>">
                            <?php if (isset($errors['logo'])): ?>
                                <div class="invalid-feedback"><?= $errors['logo'] ?></div>
                            <?php endif; ?>
                            <small class="text-muted">JPEG or PNG, max 1MB</small>
                        </div>

                        <?php if (!empty($company['logo'])): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo">
                                <label class="form-check-label" for="remove_logo">Remove current logo</label>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Social Media</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-facebook"></i></span>
                                <input type="url" name="facebook" class="form-control" 
                                       value="<?= htmlspecialchars($company['facebook'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Twitter</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                <input type="url" name="twitter" class="form-control" 
                                       value="<?= htmlspecialchars($company['twitter'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">LinkedIn</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                <input type="url" name="linkedin" class="form-control" 
                                       value="<?= htmlspecialchars($company['linkedin'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                                <input type="url" name="instagram" class="form-control" 
                                       value="<?= htmlspecialchars($company['instagram'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn btn-primary px-4">Save Changes</button>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>