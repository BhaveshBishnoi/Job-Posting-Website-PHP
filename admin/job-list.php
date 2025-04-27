<?php
/**
 * Job Listings Management
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    redirect('login.php');
    exit();
}

// Get search parameters
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Get jobs with pagination
$jobs = getAdminJobs($search, $status, $page, $perPage);
$totalJobs = getAdminJobsCount($search, $status);
$totalPages = ceil($totalJobs / $perPage);

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Job Listings</h2>
        </div>
        <div class="col-auto">
            <a href="job-add.php" class="btn btn-primary">Add New Job</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <form method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search jobs..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4">
                    <form method="GET" class="status-filter">
                        <div class="input-group">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
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
                            <th>Title</th>
                            <th>Company</th>
                            <th>Location</th>
                            <th>Posted</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['title']); ?></td>
                            <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($job['location']); ?></td>
                            <td><?php echo formatDate($job['created_at']); ?></td>
                            <td><?php echo number_format($job['views']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : ($job['status'] === 'pending' ? 'warning' : 'secondary'); ?>">
                                    <?php echo ucfirst($job['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="job-edit.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="job-delete.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $page - 1]); ?>">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $i]); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo buildQueryString(['page' => $page + 1]); ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>