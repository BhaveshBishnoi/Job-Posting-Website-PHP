<?php
/**
 * Admin Dashboard
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    redirect('login.php');
    exit();
}

// Get stats for dashboard
$totalJobs = getTotalJobsCount();
$totalCompanies = getTotalCompaniesCount();
$totalApplications = getTotalApplicationsCount();
$pendingApplications = getPendingApplicationsCount();

require_once 'includes/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Jobs</h5>
                    <h2 class="mb-0"><?php echo number_format($totalJobs); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Companies</h5>
                    <h2 class="mb-0"><?php echo number_format($totalCompanies); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Applications</h5>
                    <h2 class="mb-0"><?php echo number_format($totalApplications); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Pending Apps</h5>
                    <h2 class="mb-0"><?php echo number_format($pendingApplications); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Jobs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Company</th>
                                    <th>Posted</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (getLatestJobs(5) as $job): ?>
                                <tr>
                                    <td><a href="job-edit.php?id=<?php echo $job['id']; ?>"><?php echo htmlspecialchars($job['title']); ?></a></td>
                                    <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                    <td><?php echo formatDate($job['created_at']); ?></td>
                                    <td><?php echo number_format($job['views']); ?></td>
                                    <td><span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo ucfirst($job['status']); ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Applications</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach (getRecentApplications(5) as $app): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($app['name']); ?></strong>
                                <small class="d-block text-muted">Applied for <?php echo htmlspecialchars($app['job_title']); ?></small>
                            </div>
                            <span class="badge bg-<?php echo $app['status'] === 'pending' ? 'warning' : ($app['status'] === 'approved' ? 'success' : 'danger'); ?>">
                                <?php echo ucfirst($app['status']); ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>