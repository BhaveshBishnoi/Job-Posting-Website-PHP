<?php
/**
 * Homepage
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Page info
$pageTitle = 'Find Your Dream Job';
$pageDescription = 'Discover thousands of job opportunities across various industries';

// Get latest jobs
$latestJobs = getLatestJobs(6);

// Get featured jobs (based on views)
$featuredJobs = getFeaturedJobs(3);

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Find Your Dream Job Today</h1>
                <p class="lead mb-4">Browse thousands of job listings and find the perfect match for your skills and experience.</p>
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-light btn-lg">Browse Jobs</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="<?php echo SITE_URL; ?>/assets/images/hero-image.svg" alt="Job Search" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Featured Jobs Section -->
<section class="mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Featured Jobs</h2>
            <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-outline-primary">View All Jobs</a>
        </div>
        <div class="row">
            <?php foreach ($featuredJobs as $job): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($job['featured_image']): ?>
                            <img src="<?php echo SITE_URL . '/' . $job['featured_image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($job['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($job['company_logo']): ?>
                                    <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="company-logo me-2" width="40" height="40">
                                <?php else: ?>
                                    <div class="company-logo-placeholder me-2 bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($job['location']); ?></small>
                                </div>
                            </div>
                            <h5 class="card-title">
                                <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text"><?php echo truncateText(htmlspecialchars($job['job_description']), 100); ?></p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary"><?php echo htmlspecialchars($job['position']); ?></span>
                                <small class="text-muted">Posted <?php echo formatDate($job['created_at']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Jobs Section -->
<section class="mb-5">
    <div class="container">
        <h2 class="mb-4">Latest Job Openings</h2>
        <div class="row">
            <?php foreach ($latestJobs as $job): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <?php if ($job['company_logo']): ?>
                                    <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" alt="<?php echo htmlspecialchars($job['company_name']); ?>" class="company-logo me-2" width="40" height="40">
                                <?php else: ?>
                                    <div class="company-logo-placeholder me-2 bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="fas fa-building text-secondary"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($job['location']); ?></small>
                                </div>
                            </div>
                            <h5 class="card-title">
                                <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($job['title']); ?>
                                </a>
                            </h5>
                            <div class="mb-3">
                                <?php if ($job['salary']): ?>
                                    <span class="badge bg-success me-2"><?php echo htmlspecialchars($job['salary']); ?></span>
                                <?php endif; ?>
                                <?php if ($job['experience']): ?>
                                    <span class="badge bg-info me-2"><?php echo htmlspecialchars($job['experience']); ?> Experience</span>
                                <?php endif; ?>
                                <?php if ($job['working_days']): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($job['working_days']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text"><?php echo truncateText(htmlspecialchars($job['job_description']), 120); ?></p>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="btn btn-outline-primary btn-sm">View Details</a>
                                <small class="text-muted">Posted <?php echo formatDate($job['created_at']); ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary">Browse All Jobs</a>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="bg-light py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="mb-3">Ready to Find Your Dream Job?</h2>
                <p class="lead mb-4">Browse our latest job listings and find the perfect match for your skills and experience.</p>
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary btn-lg">Browse Jobs Now</a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>