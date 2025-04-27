<?php
/**
 * Job Detail Page
 * Handles job articles by slug and displays them in a new page
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if slug is provided
if (!isset($_GET['slug'])) {
    setFlashMessage('error', 'Invalid job listing');
    redirect(SITE_URL . '/jobs.php');
    exit();
}

$slug = sanitize($_GET['slug']);
$job = getJobBySlug($slug);

// If job not found, redirect to jobs page with error
if (!$job) {
    setFlashMessage('error', 'Job listing not found');
    redirect(SITE_URL . '/jobs.php');
    exit();
}

// Increment job views
updateJobViews($job['id']);

// Set default values for missing fields
$job = array_merge([
    'job_type' => 'Full-time',
    'position' => 'Not specified',
    'salary' => 'Not specified',
    'experience' => 'Not specified',
    'requirements' => '',
    'benefits' => '',
    'company_description' => '',
    'company_website' => '',
    'company_size' => ''
], $job);

// Page info
$pageTitle = $job['title'] . ' at ' . $job['company_name'];
$pageDescription = truncateText(strip_tags($job['job_description']), 160);

// Get similar jobs
$similarJobs = getSimilarJobs($job['id'], $job['category_id'] ?? 0, 3);

require_once 'includes/header.php';
?>

<!-- Job Detail Section -->
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/jobs.php">Jobs</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($job['title']); ?></li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-lg-8">
                <!-- Job Header -->
                <div class="d-flex align-items-center mb-4">
                    <?php if (!empty($job['company_logo'])): ?>
                        <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" 
                             alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                             class="company-logo me-3" width="100" height="100">
                    <?php else: ?>
                        <div class="company-logo-placeholder me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="fas fa-building text-secondary fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h1 class="mb-2"><?php echo htmlspecialchars($job['title']); ?></h1>
                        <h3 class="text-muted mb-3"><?php echo htmlspecialchars($job['company_name']); ?></h3>
                        <div class="d-flex flex-wrap">
                            <span class="text-muted me-3">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                <?php echo htmlspecialchars($job['location']); ?>
                            </span>
                            <span class="text-muted me-3">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo htmlspecialchars($job['job_type']); ?>
                            </span>
                            <span class="text-muted">
                                <i class="fas fa-eye me-1"></i>
                                <?php echo number_format($job['views']); ?> views
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Application Buttons -->
                <div class="mb-5">
                    <a href="<?php echo SITE_URL; ?>/job-apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary btn-lg me-2">
                        <i class="fas fa-paper-plane me-1"></i> Apply Now
                    </a>
                    <button class="btn btn-outline-secondary me-2">
                        <i class="far fa-heart me-1"></i> Save Job
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share-alt me-1"></i> Share
                    </button>
                </div>

                <!-- Job Details -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="mb-0">Job Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="d-flex mb-2">
                                    <span class="text-muted me-2" style="width: 120px;">Salary:</span>
                                    <span><?php echo htmlspecialchars($job['salary']); ?></span>
                                </div>
                                <div class="d-flex mb-2">
                                    <span class="text-muted me-2" style="width: 120px;">Experience:</span>
                                    <span><?php echo htmlspecialchars($job['experience']); ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex mb-2">
                                    <span class="text-muted me-2" style="width: 120px;">Position:</span>
                                    <span><?php echo htmlspecialchars($job['position']); ?></span>
                                </div>
                                <div class="d-flex mb-2">
                                    <span class="text-muted me-2" style="width: 120px;">Posted:</span>
                                    <span><?php echo formatDate($job['created_at']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Job Description -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h3 class="mb-0">Job Description</h3>
                    </div>
                    <div class="card-body">
                        <div class="job-description">
                            <?php echo nl2br(htmlspecialchars($job['job_description'])); ?>
                        </div>
                    </div>
                </div>

                <!-- Requirements -->
                <?php if (!empty($job['requirements'])): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h3 class="mb-0">Requirements</h3>
                        </div>
                        <div class="card-body">
                            <div class="job-requirements">
                                <?php echo nl2br(htmlspecialchars($job['requirements'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Benefits -->
                <?php if (!empty($job['benefits'])): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h3 class="mb-0">Benefits</h3>
                        </div>
                        <div class="card-body">
                            <div class="job-benefits">
                                <?php echo nl2br(htmlspecialchars($job['benefits'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Apply Button -->
                <div class="text-center mt-5">
                    <a href="<?php echo SITE_URL; ?>/job-apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-paper-plane me-2"></i> Apply For This Position
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Job Summary</h3>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Published:</span>
                                <span><?php echo formatDate($job['created_at']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Position:</span>
                                <span><?php echo htmlspecialchars($job['position']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Job Type:</span>
                                <span><?php echo htmlspecialchars($job['job_type']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Salary:</span>
                                <span><?php echo htmlspecialchars($job['salary']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Experience:</span>
                                <span><?php echo htmlspecialchars($job['experience']); ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="text-muted">Location:</span>
                                <span><?php echo htmlspecialchars($job['location']); ?></span>
                            </li>
                        </ul>
                        <div class="d-grid mt-3">
                            <a href="<?php echo SITE_URL; ?>/job-apply.php?job_id=<?php echo $job['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Apply Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Company Info -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">About <?php echo htmlspecialchars($job['company_name']); ?></h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($job['company_logo'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" 
                                     alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                     class="img-fluid" style="max-height: 100px;">
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($job['company_description'])): ?>
                            <div class="mb-3">
                                <?php echo nl2br(htmlspecialchars($job['company_description'])); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($job['company_website'])): ?>
                            <div class="mb-2">
                                <i class="fas fa-globe me-2"></i>
                                <a href="<?php echo htmlspecialchars($job['company_website']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($job['company_website']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($job['company_size'])): ?>
                            <div class="mb-2">
                                <i class="fas fa-users me-2"></i>
                                <?php echo htmlspecialchars($job['company_size']); ?> employees
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Similar Jobs -->
                <?php if (!empty($similarJobs)): ?>
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h3 class="mb-0">Similar Jobs</h3>
                        </div>
                        <div class="card-body">
                            <?php foreach ($similarJobs as $similarJob): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <h5>
                                        <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($similarJob['slug']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($similarJob['title']); ?>
                                        </a>
                                    </h5>
                                    <div class="d-flex align-items-center mb-1">
                                        <small class="text-muted me-2">
                                            <i class="fas fa-building me-1"></i>
                                            <?php echo htmlspecialchars($similarJob['company_name']); ?>
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted me-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($similarJob['location']); ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?php echo htmlspecialchars($similarJob['job_type'] ?? 'Full-time'); ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="text-center mt-2">
                                <a href="<?php echo SITE_URL; ?>/jobs.php?category=<?php echo $job['category_id'] ?? 0; ?>" class="btn btn-outline-primary btn-sm">
                                    View More Similar Jobs
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>