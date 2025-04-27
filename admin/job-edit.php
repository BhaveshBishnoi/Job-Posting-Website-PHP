<?php
/**
 * Edit Job
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    redirect('login.php');
    exit();
}

// Check if job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('job-list.php');
    exit();
}

$jobId = (int)$_GET['id'];
$job = getJobById($jobId);

// If job not found, redirect
if (!$job) {
    setFlashMessage('error', 'Job not found');
    redirect('job-list.php');
    exit();
}

$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jobData = array_map('sanitize', $_POST);
    $jobData['status'] = isset($_POST['status']) ? 'active' : 'inactive';
    $jobData['id'] = $jobId;
    
    // Validate required fields
    if (empty($jobData['title'])) {
        $errors['title'] = 'Job title is required';
    }
    
    if (empty($jobData['company_name'])) {
        $errors['company_name'] = 'Company name is required';
    }
    
    if (empty($jobData['location'])) {
        $errors['location'] = 'Location is required';
    }
    
    if (empty($jobData['job_description'])) {
        $errors['job_description'] = 'Job description is required';
    }
    
    // Handle file upload
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $logo = uploadFile($_FILES['company_logo'], LOGO_UPLOAD_PATH);
        if ($logo) {
            // Delete old logo if exists
            if (!empty($job['company_logo'])) {
                @unlink(LOGO_UPLOAD_PATH . $job['company_logo']);
            }
            $jobData['company_logo'] = $logo;
        } else {
            $errors['company_logo'] = 'Invalid logo file (only JPG/PNG allowed, max 1MB)';
        }
    } else {
        // Keep existing logo if not uploading new one
        $jobData['company_logo'] = $job['company_logo'];
    }
    
    // If no errors, update in database
    if (empty($errors)) {
        if (updateJob($jobData)) {
            setFlashMessage('success', 'Job updated successfully');
            redirect('job-list.php');
            exit();
        } else {
            $errors['general'] = 'Failed to update job. Please try again.';
        }
    }
    
    // Merge with existing data for form
    $job = array_merge($job, $jobData);
}

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h2>Edit Job</h2>
        </div>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $jobId; ?>">
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Job Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title *</label>
                            <input type="text" class="form-control <?php echo isset($errors['title']) ? 'is-invalid' : ''; ?>" 
                                   id="title" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>
                            <?php if (isset($errors['title'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['title']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Company Name *</label>
                            <input type="text" class="form-control <?php echo isset($errors['company_name']) ? 'is-invalid' : ''; ?>" 
                                   id="company_name" name="company_name" value="<?php echo htmlspecialchars($job['company_name']); ?>" required>
                            <?php if (isset($errors['company_name'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['company_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control <?php echo isset($errors['location']) ? 'is-invalid' : ''; ?>" 
                                   id="location" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" required>
                            <?php if (isset($errors['location'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['location']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_logo" class="form-label">Company Logo</label>
                            <input type="file" class="form-control <?php echo isset($errors['company_logo']) ? 'is-invalid' : ''; ?>" 
                                   id="company_logo" name="company_logo" accept="image/jpeg,image/png">
                            <?php if (isset($errors['company_logo'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['company_logo']; ?></div>
                            <?php endif; ?>
                            <small class="text-muted">JPEG or PNG, max 1MB</small>
                            <?php if (!empty($job['company_logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" 
                                         alt="Current logo" class="img-thumbnail" style="max-height: 80px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo">
                                        <label class="form-check-label" for="remove_logo">Remove current logo</label>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Rest of the form is similar to job-add.php -->
                <!-- Include all the same fields as in job-add.php -->
                
            </div>
        </div>
        
        <!-- Include the other sections (Company Information, Publish) -->
        <!-- Same as in job-add.php -->
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Publish</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo $job['status'] === 'active' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="status">Active Job Listing</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">Update Job</button>
                    <a href="job-list.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>