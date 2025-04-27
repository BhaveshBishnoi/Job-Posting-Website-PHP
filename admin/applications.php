<?php
/**
 * Job Applications Management
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get search and filter parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Get applications with pagination
$applications = getApplications($search, $status, $job_id, $page, $perPage);
$totalApplications = getApplicationsCount($search, $status, $job_id);
$totalPages = ceil($totalApplications / $perPage);

// Get jobs for filter dropdown
$jobs = getJobsForFilter();

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Job Applications</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search applications..." 
                                   value="<?= htmlspecialchars($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <div class="input-group">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="reviewed" <?= $status === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                                <option value="approved" <?= $status === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="rejected" <?= $status === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET">
                        <div class="input-group">
                            <select name="job_id" class="form-select" onchange="this.form.submit()">
                                <option value="0">All Jobs</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id'] ?>" <?= $job_id === $job['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($job['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Job Title</th>
                            <th>Applied On</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($app['name']) ?></strong>
                                <div class="text-muted small"><?= htmlspecialchars($app['email']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($app['job_title']) ?></td>
                            <td><?= formatDate($app['applied_at']) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($app['status']) ?>">
                                    <?= ucfirst($app['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="application-view.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="application-delete.php?id=<?= $app['id'] ?>" class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Delete this application?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= buildQueryString(['page' => $page - 1]) ?>">
                                Previous
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= buildQueryString(['page' => $i]) ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= buildQueryString(['page' => $page + 1]) ?>">
                                Next
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>