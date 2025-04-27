<?php
/**
 * About Page
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Page info
$pageTitle = 'About Us';
$pageDescription = 'Learn more about our job portal and mission';

// Get stats for about page
$stats = [
    'jobs_posted' => getTotalJobsCount(),
    'companies_registered' => getTotalCompaniesCount(),
    'successful_applications' => getTotalApplicationsCount(),
    'happy_candidates' => getHappyCandidatesCount()
];

require_once 'includes/header.php';
?>

<!-- About Hero Section -->
<section class="hero-section bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">About Our Job Portal</h1>
                <p class="lead mb-4">Connecting talented professionals with top employers since 2010.</p>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="<?php echo SITE_URL; ?>/assets/images/about-hero.svg" alt="About Us" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light mb-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">
                    <?php echo number_format($stats['jobs_posted']); ?>+
                </div>
                <p class="mb-0">Jobs Posted</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">
                    <?php echo number_format($stats['companies_registered']); ?>+
                </div>
                <p class="mb-0">Companies</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="display-4 fw-bold text-primary">
                    <?php echo number_format($stats['successful_applications']); ?>+
                </div>
                <p class="mb-0">Applications</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-primary">
                    <?php echo number_format($stats['happy_candidates']); ?>+
                </div>
                <p class="mb-0">Happy Candidates</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2 class="mb-4">Our Story</h2>
                <p>Founded in 2010, our job portal began with a simple mission: to make the job search process easier and more efficient for both job seekers and employers. What started as a small platform with a handful of listings has grown into one of the most trusted job search platforms in the industry.</p>
                <p>Over the years, we've helped thousands of candidates find their dream jobs and assisted countless companies in discovering top talent. Our commitment to innovation and user satisfaction has been the driving force behind our success.</p>
            </div>
            <div class="col-lg-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/our-story.jpg" alt="Our Story" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Our Mission Section -->
<section class="py-5 bg-light mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <h2 class="mb-4">Our Mission</h2>
                <p>Our mission is to bridge the gap between talent and opportunity. We strive to create a platform that is:</p>
                <ul>
                    <li><strong>Easy to use:</strong> Intuitive interface for both job seekers and employers</li>
                    <li><strong>Comprehensive:</strong> Wide range of jobs across all industries</li>
                    <li><strong>Efficient:</strong> Advanced search and matching algorithms</li>
                    <li><strong>Transparent:</strong> Clear information for all parties</li>
                </ul>
                <p>We believe that finding the right job or candidate shouldn't be a frustrating experience, and we're committed to making the process as smooth as possible.</p>
            </div>
            <div class="col-lg-6 order-lg-1">
                <img src="<?php echo SITE_URL; ?>/assets/images/our-mission.jpg" alt="Our Mission" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5 mb-5">
    <div class="container">
        <h2 class="text-center mb-5">Meet Our Team</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <img src="<?php echo SITE_URL; ?>/assets/images/team1.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body">
                        <h5 class="card-title">John Smith</h5>
                        <p class="text-muted">CEO & Founder</p>
                        <p class="card-text">With over 15 years in the recruitment industry, John leads our vision and strategy.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <img src="<?php echo SITE_URL; ?>/assets/images/team2.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body">
                        <h5 class="card-title">Sarah Johnson</h5>
                        <p class="text-muted">CTO</p>
                        <p class="card-text">Sarah oversees our technology platform and ensures seamless user experiences.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card text-center border-0 shadow-sm h-100">
                    <img src="<?php echo SITE_URL; ?>/assets/images/team3.jpg" class="card-img-top" alt="Team Member">
                    <div class="card-body">
                        <h5 class="card-title">Michael Chen</h5>
                        <p class="text-muted">Head of Customer Success</p>
                        <p class="card-text">Michael ensures our users get the support they need throughout their journey.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-primary text-white mb-5">
    <div class="container">
        <h2 class="text-center mb-5">What People Say About Us</h2>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-primary border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x opacity-25"></i>
                        </div>
                        <p class="card-text">This platform helped me find my dream job in just two weeks! The application process was so smooth.</p>
                        <div class="d-flex align-items-center mt-4">
                            <img src="<?php echo SITE_URL; ?>/assets/images/testimonial1.jpg" class="rounded-circle me-3" width="50" height="50" alt="Testimonial">
                            <div>
                                <h6 class="mb-0">Emily Rodriguez</h6>
                                <small class="text-white-50">Marketing Manager</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-primary border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x opacity-25"></i>
                        </div>
                        <p class="card-text">As a hiring manager, I've found some of our best candidates through this platform. Highly recommended!</p>
                        <div class="d-flex align-items-center mt-4">
                            <img src="<?php echo SITE_URL; ?>/assets/images/testimonial2.jpg" class="rounded-circle me-3" width="50" height="50" alt="Testimonial">
                            <div>
                                <h6 class="mb-0">David Thompson</h6>
                                <small class="text-white-50">HR Director</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card h-100 bg-primary border-0">
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-quote-left fa-2x opacity-25"></i>
                        </div>
                        <p class="card-text">The job matching algorithm is incredible. It suggested positions I wouldn't have found on my own.</p>
                        <div class="d-flex align-items-center mt-4">
                            <img src="<?php echo SITE_URL; ?>/assets/images/testimonial3.jpg" class="rounded-circle me-3" width="50" height="50" alt="Testimonial">
                            <div>
                                <h6 class="mb-0">James Wilson</h6>
                                <small class="text-white-50">Software Engineer</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>