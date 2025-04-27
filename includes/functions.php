<?php
/**
 * Helper functions for the job portal
 */

/**
 * Generate a slug from a string
 */
function generateSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = preg_replace('/\s/', '-', $string);
    return $string;
}

/**
 * Format date
 */
function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Upload file and return the filename
 */
function uploadFile($file, $destination, $allowedTypes = ['image/jpeg', 'image/png']) {
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    // Check file type
    $fileType = $file['type'];
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target = $destination . $filename;
    
    // Create directory if it doesn't exist
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Move the file
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return $filename;
    }
    
    return false;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    return $text . '...';
}

/**
 * Get job by slug
 */
function getJobBySlug($slug) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM jobs WHERE slug = :slug AND status = 'active'", ['slug' => $slug]);
}

/**
 * Get latest jobs
 */
function getLatestJobs($limit = 6) {
    $db = Database::getInstance();
    return $db->select("SELECT * FROM jobs WHERE status = 'active' ORDER BY created_at DESC LIMIT :limit", ['limit' => $limit]);
}

/**
 * Get featured jobs
 */
function getFeaturedJobs($limit = 3) {
    $db = Database::getInstance();
    return $db->select("SELECT * FROM jobs WHERE status = 'active' ORDER BY views DESC LIMIT :limit", ['limit' => $limit]);
}

/**
 * Update job views
 */
function updateJobViews($jobId) {
    $db = Database::getInstance();
    $db->query("UPDATE jobs SET views = views + 1 WHERE id = :id", ['id' => $jobId]);
}

/**
 * Get pagination
 */
function getPagination($total, $page, $perPage) {
    $totalPages = ceil($total / $perPage);
    
    return [
        'total' => $total,
        'perPage' => $perPage,
        'currentPage' => $page,
        'totalPages' => $totalPages,
        'hasPrev' => $page > 1,
        'hasNext' => $page < $totalPages
    ];
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password . SALT, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password . SALT, $hash);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $type = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">';
        echo $flash['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

/**
 * Get total number of jobs in database
 */
function getTotalJobsCount() {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'");
    return $result['count'] ?? 0;
}

/**
 * Get total number of companies registered
 */
function getTotalCompaniesCount() {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(DISTINCT company_id) as count FROM jobs");
    return $result['count'] ?? 0;
}

/**
 * Get total number of applications
 */
function getTotalApplicationsCount() {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(*) as count FROM job_applications");
    return $result['count'] ?? 0;
}

/**
 * Get estimated number of happy candidates (placeholder implementation)
 */
function getHappyCandidatesCount() {
    // This could be replaced with actual data from your database
    return getTotalApplicationsCount() * 0.85; // Assuming 85% satisfaction rate
}

/**
 * Get similar jobs based on category
 */
function getSimilarJobs($currentJobId, $categoryId, $limit = 3) {
    $db = Database::getInstance();
    return $db->select(
        "SELECT * FROM jobs 
         WHERE category_id = :category_id 
         AND id != :current_job_id 
         AND status = 'active' 
         ORDER BY created_at DESC 
         LIMIT :limit",
        [
            'category_id' => $categoryId,
            'current_job_id' => $currentJobId,
            'limit' => $limit
        ]
    );
}

/**
 * Get all job categories for filtering
 */
function getJobCategories() {
    $db = Database::getInstance();
    return $db->select(
        "SELECT c.id, c.name, COUNT(j.id) as job_count 
         FROM categories c
         LEFT JOIN jobs j ON j.category_id = c.id AND j.status = 'active'
         GROUP BY c.id, c.name
         ORDER BY c.name"
    );
}

/**
 * Get jobs with filters and pagination
 */
function getJobs($search = '', $location = '', $category = 0, $page = 1, $perPage = 10) {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE status = 'active'";
    
    if (!empty($search)) {
        $where .= " AND (title LIKE :search OR company_name LIKE :search OR job_description LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($location)) {
        $where .= " AND location LIKE :location";
        $params['location'] = "%$location%";
    }
    
    if (!empty($category) && is_numeric($category)) {
        $where .= " AND category_id = :category";
        $params['category'] = $category;
    }
    
    $offset = ($page - 1) * $perPage;
    
    return $db->select(
        "SELECT * FROM jobs 
         $where
         ORDER BY created_at DESC
         LIMIT :offset, :per_page",
        array_merge($params, [
            'offset' => $offset,
            'per_page' => $perPage
        ])
    );
}

/**
 * Get total jobs count for pagination with filters
 */
function getTotalJobs($search = '', $location = '', $category = 0) {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE status = 'active'";
    
    if (!empty($search)) {
        $where .= " AND (title LIKE :search OR company_name LIKE :search OR job_description LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($location)) {
        $where .= " AND location LIKE :location";
        $params['location'] = "%$location%";
    }
    
    if (!empty($category) && is_numeric($category)) {
        $where .= " AND category_id = :category";
        $params['category'] = $category;
    }
    
    $result = $db->selectOne("SELECT COUNT(*) as count FROM jobs $where", $params);
    return $result['count'] ?? 0;
}

/**
 * Get job by ID
 */
function getJobById($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM jobs WHERE id = :id AND status = 'active'", ['id' => $id]);
}

/**
 * Upload resume file
 */
function uploadResume($file) {
    $allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];
    
    return uploadFile($file, RESUME_UPLOAD_PATH, $allowedTypes);
}

/**
 * Send application notification to employer
 */
function sendApplicationNotification($job, $application) {
    $to = $job['contact_email'] ?? ADMIN_EMAIL;
    $subject = "New Application for {$job['title']}";
    
    $message = "You have received a new application:\n\n";
    $message .= "Job Title: {$job['title']}\n";
    $message .= "Applicant Name: {$application['name']}\n";
    $message .= "Applicant Email: {$application['email']}\n";
    $message .= "Applicant Phone: {$application['phone']}\n\n";
    $message .= "Cover Letter:\n{$application['cover_letter']}\n\n";
    $message .= "Resume: " . SITE_URL . '/' . RESUME_UPLOAD_PATH . $application['resume'] . "\n";
    
    $headers = "From: " . NOREPLY_EMAIL . "\r\n";
    $headers .= "Reply-To: {$application['email']}\r\n";
    
    mail($to, $subject, $message, $headers);
}

/**
 * Send application confirmation to candidate
 */
function sendApplicationConfirmation($email, $job) {
    $subject = "Application Confirmation for {$job['title']}";
    
    $message = "Thank you for applying to {$job['title']} at {$job['company_name']}.\n\n";
    $message .= "We have received your application and will review it shortly.\n";
    $message .= "If your qualifications match our requirements, we will contact you for next steps.\n\n";
    $message .= "Job Details:\n";
    $message .= "Title: {$job['title']}\n";
    $message .= "Company: {$job['company_name']}\n";
    $message .= "Location: {$job['location']}\n\n";
    $message .= "You can view the job posting here: " . SITE_URL . "/job-detail.php?slug={$job['slug']}\n\n";
    $message .= "Best regards,\n";
    $message .= "The {$job['company_name']} Team";
    
    $headers = "From: " . NOREPLY_EMAIL . "\r\n";
    
    mail($email, $subject, $message, $headers);
}

/**
 * Build pagination URL with filters
 */
function buildPaginationUrl($page, $search, $location, $category) {
    $params = [];
    if (!empty($search)) $params['search'] = urlencode($search);
    if (!empty($location)) $params['location'] = urlencode($location);
    if (!empty($category)) $params['category'] = (int)$category;
    $params['page'] = $page;
    
    return SITE_URL . '/jobs.php?' . http_build_query($params);
}

/**
 * Save job application to database
 */
function saveJobApplication($data) {
    $db = Database::getInstance();
    return $db->insert('job_applications', [
        'job_id' => $data['job_id'],
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'cover_letter' => $data['cover_letter'],
        'resume' => $data['resume'],
        'ip_address' => $data['ip_address'],
        'user_agent' => $data['user_agent'],
        'status' => $data['status'],
        'applied_at' => date('Y-m-d H:i:s')
    ]);
}

// In your jobs.php, after getting categories:
$categories = getJobCategories();
if (empty($categories)) {
    // Provide some default categories if none exist
    $categories = [
        ['id' => 1, 'name' => 'Technology', 'job_count' => 0],
        ['id' => 2, 'name' => 'Healthcare', 'job_count' => 0],
        ['id' => 3, 'name' => 'Finance', 'job_count' => 0]
    ];
}

/**
 * Get admin jobs with filters
 */
function getAdminJobs($search = '', $status = '', $page = 1, $perPage = 10) {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE 1=1";
    
    if (!empty($search)) {
        $where .= " AND (title LIKE :search OR company_name LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status)) {
        $where .= " AND status = :status";
        $params['status'] = $status;
    }
    
    $offset = ($page - 1) * $perPage;
    
    return $db->select(
        "SELECT * FROM jobs 
         $where
         ORDER BY created_at DESC
         LIMIT :offset, :per_page",
        array_merge($params, [
            'offset' => $offset,
            'per_page' => $perPage
        ])
    );
}

/**
 * Get count of admin jobs with filters
 */
function getAdminJobsCount($search = '', $status = '') {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE 1=1";
    
    if (!empty($search)) {
        $where .= " AND (title LIKE :search OR company_name LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status)) {
        $where .= " AND status = :status";
        $params['status'] = $status;
    }
    
    $result = $db->selectOne("SELECT COUNT(*) as count FROM jobs $where", $params);
    return $result['count'] ?? 0;
}

/**
 * Save job to database
 */
function saveJob($data) {
    $db = Database::getInstance();
    return $db->insert('jobs', $data);
}

/**
 * Update job in database
 */
function updateJob($data) {
    $db = Database::getInstance();
    return $db->update('jobs', $data, 'id = :id', ['id' => $data['id']]);
}

/**
 * Delete job from database
 */
function deleteJob($id) {
    $db = Database::getInstance();
    return $db->query("DELETE FROM jobs WHERE id = :id", ['id' => $id]);
}

/**
 * Get pending applications count
 */
function getPendingApplicationsCount() {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT COUNT(*) as count FROM job_applications WHERE status = 'pending'");
    return $result['count'] ?? 0;
}

/**
 * Get recent applications
 */
function getRecentApplications($limit = 5) {
    $db = Database::getInstance();
    return $db->select(
        "SELECT a.*, j.title as job_title 
         FROM job_applications a
         JOIN jobs j ON j.id = a.job_id
         ORDER BY a.applied_at DESC
         LIMIT :limit",
        ['limit' => $limit]
    );
}

/**
 * Build query string for pagination
 */
function buildQueryString($params = []) {
    $currentParams = $_GET;
    unset($currentParams['page']);
    $mergedParams = array_merge($currentParams, $params);
    return http_build_query($mergedParams);
}

/**
 * Get job applications with filters
 */
function getApplications($search = '', $status = '', $job_id = 0, $page = 1, $perPage = 10) {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE 1=1";
    
    if (!empty($search)) {
        $where .= " AND (a.name LIKE :search OR a.email LIKE :search OR j.title LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status)) {
        $where .= " AND a.status = :status";
        $params['status'] = $status;
    }
    
    if (!empty($job_id)) {
        $where .= " AND a.job_id = :job_id";
        $params['job_id'] = $job_id;
    }
    
    $offset = ($page - 1) * $perPage;
    
    return $db->select(
        "SELECT a.*, j.title as job_title 
         FROM job_applications a
         JOIN jobs j ON j.id = a.job_id
         $where
         ORDER BY a.applied_at DESC
         LIMIT :offset, :per_page",
        array_merge($params, [
            'offset' => $offset,
            'per_page' => $perPage
        ])
    );
}

/**
 * Get applications count with filters
 */
function getApplicationsCount($search = '', $status = '', $job_id = 0) {
    $db = Database::getInstance();
    
    $params = [];
    $where = "WHERE 1=1";
    
    if (!empty($search)) {
        $where .= " AND (a.name LIKE :search OR a.email LIKE :search OR j.title LIKE :search)";
        $params['search'] = "%$search%";
    }
    
    if (!empty($status)) {
        $where .= " AND a.status = :status";
        $params['status'] = $status;
    }
    
    if (!empty($job_id)) {
        $where .= " AND a.job_id = :job_id";
        $params['job_id'] = $job_id;
    }
    
    $result = $db->selectOne(
        "SELECT COUNT(*) as count 
         FROM job_applications a
         JOIN jobs j ON j.id = a.job_id
         $where",
        $params
    );
    
    return $result['count'] ?? 0;
}

/**
 * Get jobs for filter dropdown
 */
function getJobsForFilter() {
    $db = Database::getInstance();
    return $db->select("SELECT id, title FROM jobs ORDER BY title");
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending': return 'warning';
        case 'reviewed': return 'info';
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Get company profile
 */
function getCompanyProfile($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM companies WHERE id = :id", ['id' => $id]);
}

/**
 * Update company profile
 */
function updateCompanyProfile($id, $data) {
    $db = Database::getInstance();
    return $db->update('companies', $data, 'id = :id', ['id' => $id]);
}

/**
 * Get admin by ID
 */
function getAdminById($id) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM admins WHERE id = :id", ['id' => $id]);
}

/**
 * Update admin profile
 */
function updateAdminProfile($id, $data) {
    $db = Database::getInstance();
    return $db->update('admins', $data, 'id = :id', ['id' => $id]);
}

/**
 * Update admin password
 */
function updateAdminPassword($id, $password) {
    $db = Database::getInstance();
    return $db->update('admins', ['password' => $password], 'id = :id', ['id' => $id]);
}

/**
 * Get system setting
 */
function getSetting($key, $default = '') {
    $db = Database::getInstance();
    $result = $db->selectOne("SELECT value FROM settings WHERE setting_key = :key", ['key' => $key]);
    return $result['value'] ?? $default;
}

function getAdminByUsername($username) {
    $db = Database::getInstance();
    return $db->selectOne("SELECT * FROM admins WHERE username = :username", ['username' => $username]);
}