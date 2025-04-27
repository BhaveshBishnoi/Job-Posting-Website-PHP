<?php
/**
 * Contact Page
 */
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Page info
$pageTitle = 'Contact Us';
$pageDescription = 'Get in touch with our team for any questions or support';

// Form submission handling
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }

    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    if (empty($subject)) {
        $errors['subject'] = 'Subject is required';
    }

    if (empty($message)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message) < 20) {
        $errors['message'] = 'Message should be at least 20 characters';
    }

    // If no errors, process the form
    if (empty($errors)) {
        // Save contact message to database
        $db = Database::getInstance();
        $result = $db->insert('contact_messages', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            // Send email notification
            $to = CONTACT_EMAIL;
            $emailSubject = "New Contact Message: $subject";
            $emailBody = "You have received a new contact message:\n\n";
            $emailBody .= "Name: $name\n";
            $emailBody .= "Email: $email\n\n";
            $emailBody .= "Message:\n$message\n";
            
            $headers = "From: $email\r\n";
            $headers .= "Reply-To: $email\r\n";
            
            mail($to, $emailSubject, $emailBody, $headers);
            
            $success = true;
            setFlashMessage('success', 'Thank you for your message! We will get back to you soon.');
            redirect(SITE_URL . '/contact.php');
        } else {
            $errors['general'] = 'There was an error submitting your message. Please try again.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Contact Hero Section -->
<section class="hero-section bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-4">Have questions? We're here to help!</p>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="<?php echo SITE_URL; ?>/assets/images/contact-hero.svg" alt="Contact Us" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="mb-4">Send Us a Message</h2>
                <?php displayFlashMessage(); ?>
                
                <?php if (!empty($errors['general'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errors['general']); ?>
                    </div>
                <?php endif; ?>
                
                <form action="<?php echo SITE_URL; ?>/contact.php" method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name *</label>
                        <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : ''; ?>" 
                               id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" 
                               id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject *</label>
                        <input type="text" class="form-control <?php echo isset($errors['subject']) ? 'is-invalid' : ''; ?>" 
                               id="subject" name="subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                        <?php if (isset($errors['subject'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['subject']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea class="form-control <?php echo isset($errors['message']) ? 'is-invalid' : ''; ?>" 
                                  id="message" name="message" rows="5"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        <?php if (isset($errors['message'])): ?>
                            <div class="invalid-feedback">
                                <?php echo htmlspecialchars($errors['message']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="consent" name="consent" required>
                        <label class="form-check-label" for="consent">I consent to having this website store my submitted information so they can respond to my inquiry. *</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-4">Contact Information</h2>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-4">
                            <div class="me-3 text-primary">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Our Office</h5>
                                <p class="mb-0">123 Job Street, Suite 456<br>San Francisco, CA 94107</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mb-4">
                            <div class="me-3 text-primary">
                                <i class="fas fa-phone-alt fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Phone</h5>
                                <p class="mb-0">
                                    <a href="tel:+18005551234" class="text-decoration-none">+1 (800) 555-1234</a><br>
                                    Monday - Friday, 9am - 5pm PST
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="me-3 text-primary">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Email</h5>
                                <p class="mb-0">
                                    <a href="mailto:info@jobportal.com" class="text-decoration-none">info@jobportal.com</a><br>
                                    We typically respond within 24 hours
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="mb-3">Frequently Asked Questions</h3>
                <div class="accordion mb-4" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                How do I post a job on your platform?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Employers can create an account and post jobs through our employer dashboard. Visit our <a href="<?php echo SITE_URL; ?>/employers.php">Employers page</a> for more information.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Is there a fee to apply for jobs?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                No, job seekers can browse and apply for jobs completely free of charge. We never charge candidates for applying to jobs.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                How can I delete my account?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                You can delete your account from the account settings page. Please note that this action is irreversible and will permanently remove all your data from our system.
                            </div>
                        </div>
                    </div>
                </div>
                
                <h3 class="mb-3">Follow Us</h3>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="btn btn-outline-primary"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="mb-5">
    <div class="container-fluid px-0">
        <div class="ratio ratio-21x9">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.158572329599!2d-122.4199066846821!3d37.77492997975921!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80859a6d00690021%3A0x4a501367f076adff!2sSan%20Francisco%2C%20CA%2C%20USA!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" 
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>