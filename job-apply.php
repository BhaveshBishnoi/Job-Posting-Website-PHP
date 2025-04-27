<?php
/**
 * Job Application Page
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if job ID is provided
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header('Location: ' . SITE_URL . '/jobs.php');
    exit();
}

$jobId = (int)$_GET['job_id'];
$job = getJobById($jobId);

// If job not found, redirect to jobs page
if (!$job) {
    header('Location: ' . SITE_URL . '/jobs.php');
    exit();
}

// Page info
$pageTitle = 'Apply for ' . $job['title'] . ' at ' . $job['company_name'];
$pageDescription = 'Apply for this job position';

// Form submission handling
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $coverLetter = trim($_POST['cover_letter'] ?? '');
    $resume = $_FILES['resume'] ?? null;

    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    }

    if (empty($coverLetter)) {
        $errors['cover_letter'] = 'Cover letter is required';
    } elseif (strlen($coverLetter) < 50) {
        $errors['cover_letter'] = 'Cover letter should be at least 50 characters';
    }

    if (!$resume || $resume['error'] !== UPLOAD_ERR_OK) {
        $errors['resume'] = 'Resume file is required';
    } elseif ($resume['size'] > MAX_RESUME_SIZE) {
        $errors['resume'] = 'Resume file size should be less than ' . (MAX_RESUME_SIZE / 1024) . 'KB';
    } else {
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($resume['type'], $allowedTypes)) {
            $errors['resume'] = 'Only PDF and Word documents are allowed';
        }
    }

    // If no errors, process the application
    if (empty($errors)) {
        // Upload resume
        $resumeFileName = uploadResume($resume);

        if ($resumeFileName) {
            // Save application to database
            $applicationData = [
                'job_id' => $jobId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'cover_letter' => $coverLetter,
                'resume' => $resumeFileName,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'status' => 'pending'
            ];

            if (saveJobApplication($applicationData)) {
                $success = true;
                
                // Send notification email to employer
                sendApplicationNotification($job, $applicationData);
                
                // Send confirmation email to applicant
                sendApplicationConfirmation($email, $job);
            } else {
                $errors['general'] = 'There was an error submitting your application. Please try again.';
            }
        } else {
            $errors['resume'] = 'There was an error uploading your resume. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Job Application Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">
                                <h4 class="alert-heading">Application Submitted Successfully!</h4>
                                <p>Thank you for applying for the <strong><?php echo htmlspecialchars($job['title']); ?></strong> position at <strong><?php echo htmlspecialchars($job['company_name']); ?></strong>.</p>
                                <p>We have sent a confirmation email to <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
                                <hr>
                                <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="btn btn-outline-success">
                                    Back to Job Details
                                </a>
                                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-success">
                                    Browse More Jobs
                                </a>
                            </div>
                        <?php else: ?>
                            <h2 class="text-center mb-4">Apply for <?php echo htmlspecialchars($job['title']); ?></h2>
                            <p class="text-center text-muted mb-4">
                                Position at <strong><?php echo htmlspecialchars($job['company_name']); ?></strong>
                            </p>

                            <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?php echo SITE_URL; ?>/job-apply.php?job_id=<?php echo $jobId; ?>" method="post" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name *</label>
                                            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                                                   id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                            <?php if (isset($errors['name'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['name']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                                                   id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                            <?php if (isset($errors['email'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['email']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phone" class="form-label">Phone Number *</label>
                                            <input type="tel" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>" 
                                                   id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                            <?php if (isset($errors['phone'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['phone']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="resume" class="form-label">Resume (PDF/DOC) *</label>
                                            <input type="file" class="form-control <?php echo isset($errors['resume']) ? 'is-invalid' : ''; ?>" 
                                                   id="resume" name="resume" accept=".pdf,.doc,.docx">
                                            <?php if (isset($errors['resume'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['resume']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Max file size: <?php echo (MAX_RESUME_SIZE / 1024); ?>KB</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="cover_letter" class="form-label">Cover Letter *</label>
                                            <textarea class="form-control <?php echo isset($errors['cover_letter']) ? 'is-invalid' : ''; ?>" 
                                                      id="cover_letter" name="cover_letter" rows="6"><?php echo htmlspecialchars($_POST['cover_letter'] ?? ''); ?></textarea>
                                            <?php if (isset($errors['cover_letter'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['cover_letter']); ?>
                                                </div>
                                            <?php endif; ?>
                                            <small class="text-muted">Tell us why you'd be a good fit for this position (minimum 50 characters)</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check mb-4">
                                            <input class="form-check-input <?php echo isset($errors['agree_terms']) ? 'is-invalid' : ''; ?>" 
                                                   type="checkbox" id="agree_terms" name="agree_terms">
                                            <label class="form-check-label" for="agree_terms">
                                                I agree to the <a href="<?php echo SITE_URL; ?>/terms.php" target="_blank">Terms of Service</a> and 
                                                <a href="<?php echo SITE_URL; ?>/privacy.php" target="_blank">Privacy Policy</a> *
                                            </label>
                                            <?php if (isset($errors['agree_terms'])): ?>
                                                <div class="invalid-feedback">
                                                    <?php echo htmlspecialchars($errors['agree_terms']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                            Submit Application
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>