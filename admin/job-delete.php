<?php
/**
 * Delete Job
 */
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isLoggedIn()) {
    redirect('login.php');
    exit();
}

// Check if job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('job-list.php');
    exit();
}

$jobId = (int)$_GET['id'];
$job = getJobById($jobId);

// If job not found, redirect
if (!$job) {
    setFlashMessage('error', 'Job not found');
    redirect('job-list.php');
    exit();
}

// Handle deletion
if (deleteJob($jobId)) {
    // Delete associated logo if exists
    if (!empty($job['company_logo'])) {
        @unlink(LOGO_UPLOAD_PATH . $job['company_logo']);
    }
    setFlashMessage('success', 'Job deleted successfully');
} else {
    setFlashMessage('error', 'Failed to delete job');
}

redirect('job-list.php');
exit();