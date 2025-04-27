<?php
/**
 * Jobs Listing Page
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Page info
$pageTitle = 'Browse Jobs';
$pageDescription = 'Browse all available job opportunities';

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Get jobs with pagination
$jobs = getJobs($search, $location, $category, $page, $perPage);
$totalJobs = getTotalJobs($search, $location, $category);
$totalPages = ceil($totalJobs / $perPage);

// Get job categories for filter
$categories = getJobCategories();

require_once 'includes/header.php';
?>

<!-- Job Search Section -->
<section class="bg-light py-4 mb-4">
    <div class="container">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/jobs.php" method="get">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Job title, keywords" value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="location" class="form-control" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Search Jobs</button>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <select name="category" class="form-select">
                                <option value="0">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Jobs Listing Section -->
<section class="mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <?php if ($search || $location || $category): ?>
                    <?php echo number_format($totalJobs); ?> Jobs Found
                <?php else: ?>
                    All Jobs
                <?php endif; ?>
            </h2>
            <div>
                <span class="text-muted me-2">Sort by:</span>
                <div class="btn-group">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Most Recent
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Most Recent</a></li>
                        <li><a class="dropdown-item" href="#">Most Relevant</a></li>
                        <li><a class="dropdown-item" href="#">Highest Salary</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <?php if (count($jobs) > 0): ?>
            <div class="row">
                <div class="col-lg-8">
                    <?php foreach ($jobs as $job): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex">
                                    <?php if ($job['company_logo']): ?>
                                        <img src="<?php echo SITE_URL . '/' . LOGO_UPLOAD_PATH . $job['company_logo']; ?>" 
                                             alt="<?php echo htmlspecialchars($job['company_name']); ?>" 
                                             class="company-logo me-3" width="80" height="80">
                                    <?php else: ?>
                                        <div class="company-logo-placeholder me-3 bg-light rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-building text-secondary fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h5>
                                            <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="text-decoration-none">
                                                <?php echo htmlspecialchars($job['title']); ?>
                                            </a>
                                        </h5>
                                        <div class="mb-2">
                                            <span class="text-muted">
                                                <i class="fas fa-building me-1"></i>
                                                <?php echo htmlspecialchars($job['company_name']); ?>
                                            </span>
                                            <span class="text-muted ms-3">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($job['location']); ?>
                                            </span>
                                        </div>
                                        <div class="mb-2">
                                            <?php if ($job['salary']): ?>
                                                <span class="badge bg-success me-2">
                                                    <i class="fas fa-money-bill-wave me-1"></i>
                                                    <?php echo htmlspecialchars($job['salary']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($job['position']): ?>
                                                <span class="badge bg-primary me-2">
                                                    <?php echo htmlspecialchars($job['position']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($job['job_type']): ?>
                                                <span class="badge bg-info">
                                                    <?php echo htmlspecialchars($job['job_type']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-0"><?php echo truncateText(htmlspecialchars($job['job_description']), 150); ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Posted <?php echo formatDate($job['created_at']); ?>
                                    </small>
                                    <a href="<?php echo SITE_URL; ?>/job-detail.php?slug=<?php echo htmlspecialchars($job['slug']); ?>" class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Jobs pagination">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($page - 1, $search, $location, $category); ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($i, $search, $location, $category); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo buildPaginationUrl($page + 1, $search, $location, $category); ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Job Alerts</h5>
                        </div>
                        <div class="card-body">
                            <p>Get notified when new jobs match your search criteria.</p>
                            <form>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Your Email">
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Create Job Alert</button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Popular Categories</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($categories as $cat): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="<?php echo SITE_URL; ?>/jobs.php?category=<?php echo $cat['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                        <span class="badge bg-primary rounded-pill"><?php echo $cat['job_count']; ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-4"></i>
                <h3>No Jobs Found</h3>
                <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
                <a href="<?php echo SITE_URL; ?>/jobs.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>