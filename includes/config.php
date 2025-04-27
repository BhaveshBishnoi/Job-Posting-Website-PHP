<?php
/**
 * Configuration settings for the job portal
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'openhiring');

// Site configuration
define('SITE_URL', 'http://localhost/openhiring');
define('SITE_NAME', 'OpenHiring');
define('SITE_DESCRIPTION', 'Find your dream job');

// File upload paths
define('LOGO_UPLOAD_PATH', 'assets/uploads/logos/');
define('RESUME_UPLOAD_PATH', 'assets/uploads/resumes/');

// Pagination
define('JOBS_PER_PAGE', 10);

// Admin configuration
define('ADMIN_URL', SITE_URL . '/admin');

// Security
define('SALT', 'your_random_salt_string_here');


// Maximum file sizes (in bytes)
define('MAX_LOGO_SIZE', 1024 * 1024); // 1MB
define('MAX_RESUME_SIZE', 2 * 1024 * 1024); // 2MB

// Email addresses
define('ADMIN_EMAIL', 'admin@yourdomain.com');
define('NOREPLY_EMAIL', 'noreply@yourdomain.com');
define('CONTACT_EMAIL', 'contact@yourdomain.com');