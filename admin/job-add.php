<?php
/**
 * Add New Job
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    redirect('login.php');
    exit();
}

$errors = [];
$job = [
    'title' => '',
    'company_name' => '',
    'location' => '',
    'job_description' => '',
    'requirements' => '',
    'benefits' => '',
    'salary' => '',
    'job_type' => 'Full-time',
    'position' => '',
    'experience' => '',
    'company_description' => '',
    'company_website' => '',
    'company_size' => '',
    'status' => 'active'
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job = array_map('sanitize', $_POST);
    $job['status'] = isset($_POST['status']) ? 'active' : 'inactive';
    
    // Validate required fields
    if (empty($job['title'])) {
        $errors['title'] = 'Job title is required';
    }
    
    if (empty($job['company_name'])) {
        $errors['company_name'] = 'Company name is required';
    }
    
    if (empty($job['location'])) {
        $errors['location'] = 'Location is required';
    }
    
    if (empty($job['job_description'])) {
        $errors['job_description'] = 'Job description is required';
    }
    
    // Handle file upload
    if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
        $logo = uploadFile($_FILES['company_logo'], LOGO_UPLOAD_PATH);
        if ($logo) {
            $job['company_logo'] = $logo;
        } else {
            $errors['company_logo'] = 'Invalid logo file (only JPG/PNG allowed, max 1MB)';
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $job['slug'] = generateSlug($job['title']);
        $job['admin_id'] = $_SESSION['admin_id'];
        
        if (saveJob($job)) {
            setFlashMessage('success', 'Job added successfully');
            redirect('job-list.php');
            exit();
        } else {
            $errors['general'] = 'Failed to add job. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col">
            <h2>Add New Job</h2>
        </div>
    </div>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
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
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="job_type" class="form-label">Job Type</label>
                            <select class="form-select" id="job_type" name="job_type">
                                <option value="Full-time" <?php echo $job['job_type'] === 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                                <option value="Part-time" <?php echo $job['job_type'] === 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                                <option value="Contract" <?php echo $job['job_type'] === 'Contract' ? 'selected' : ''; ?>>Contract</option>
                                <option value="Freelance" <?php echo $job['job_type'] === 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                                <option value="Internship" <?php echo $job['job_type'] === 'Internship' ? 'selected' : ''; ?>>Internship</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="position" class="form-label">Position</label>
                            <input type="text" class="form-control" id="position" name="position" value="<?php echo htmlspecialchars($job['position']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="salary" class="form-label">Salary</label>
                            <input type="text" class="form-control" id="salary" name="salary" value="<?php echo htmlspecialchars($job['salary']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="experience" class="form-label">Experience</label>
                            <input type="text" class="form-control" id="experience" name="experience" value="<?php echo htmlspecialchars($job['experience']); ?>">
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description *</label>
                    <textarea class="form-control <?php echo isset($errors['job_description']) ? 'is-invalid' : ''; ?>" 
                              id="job_description" name="job_description" rows="5" required><?php echo htmlspecialchars($job['job_description']); ?></textarea>
                    <?php if (isset($errors['job_description'])): ?>
                        <div class="invalid-feedback"><?php echo $errors['job_description']; ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="requirements" class="form-label">Requirements</label>
                    <textarea class="form-control" id="requirements" name="requirements" rows="5"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="benefits" class="form-label">Benefits</label>
                    <textarea class="form-control" id="benefits" name="benefits" rows="5"><?php echo htmlspecialchars($job['benefits']); ?></textarea>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Company Information</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="company_description" class="form-label">Company Description</label>
                    <textarea class="form-control" id="company_description" name="company_description" rows="5"><?php echo htmlspecialchars($job['company_description']); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="company_website" name="company_website" value="<?php echo htmlspecialchars($job['company_website']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="company_size" class="form-label">Company Size</label>
                            <input type="text" class="form-control" id="company_size" name="company_size" value="<?php echo htmlspecialchars($job['company_size']); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                    <button type="submit" class="btn btn-primary">Publish Job</button>
                    <a href="job-list.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>