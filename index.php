<?php

$projectName = "CareerConnect";
$tagline = "Your Career. Your Future. Your Opportunity.";
$pageTitle = $projectName . " | AI-Based Placement Portal";

require_once __DIR__ . "/includes/header.php";

?>

<!-- HERO SECTION -->
<section class="hero-section" id="home">

    <div class="container">
        <div class="row align-items-center">

            <div class="col-lg-7">

                <span class="hero-label">
                    YOUR CAREER JOURNEY STARTS HERE
                </span>

                <h1>
                    Discover Opportunities.
                    <span>Build Your Future.</span>
                </h1>

                <p class="hero-description">
                    <?php echo htmlspecialchars($tagline); ?>
                    Connect with companies, explore job opportunities
                    and discover career options that match your skills.
                </p>

                <div class="d-flex flex-wrap gap-3 mt-4">

                    <a href="#jobs" class="btn btn-primary btn-lg">
                        Explore Opportunities
                    </a>

                    <a href="<?php echo BASE_URL; ?>/auth/register.php"
                       class="btn btn-outline-primary btn-lg">
                        Create Account
                    </a>

                </div>

                <div class="hero-highlights mt-4">
                    <span>✓ Smart Job Matching</span>
                    <span>✓ Easy Applications</span>
                    <span>✓ Career Opportunities</span>
                </div>

            </div>

            <div class="col-lg-5 mt-5 mt-lg-0">

                <div class="hero-card text-center">

                    <div class="hero-icon">🎓</div>

                    <span class="hero-card-label">
                        YOUR NEXT OPPORTUNITY
                    </span>

                    <h3>Make Your Career Move</h3>

                    <p>
                        Build your professional profile,
                        explore opportunities and take
                        the next step towards your goals.
                    </p>

                    <div class="career-stat">

                        <div>
                            <h4>Smart</h4>
                            <small>Job Recommendations</small>
                        </div>

                        <div>
                            <h4>Simple</h4>
                            <small>Application Tracking</small>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

</section>


<!-- PORTAL ACCESS SECTION -->
<section class="section-padding portal-section" id="portals">

    <div class="container">

        <div class="section-heading text-center">

            <span>CHOOSE YOUR PORTAL</span>

            <h2>Access CareerConnect</h2>

            <p>
                Select your role to continue to the appropriate
                login page and access your dashboard.
            </p>

        </div>

        <div class="row g-4 mt-4">

            <!-- STUDENT PORTAL -->
            <div class="col-md-4">

                <div class="portal-card">

                    <div class="portal-icon">🎓</div>

                    <h4>Student Portal</h4>

                    <p>
                        Create your profile, upload your resume,
                        explore job openings, apply for jobs
                        and receive AI-based recommendations.
                    </p>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-primary w-100">
                        Student Login →
                    </a>

                </div>

            </div>

            <!-- COMPANY PORTAL -->
            <div class="col-md-4">

                <div class="portal-card">

                    <div class="portal-icon">🏢</div>

                    <h4>Company Portal</h4>

                    <p>
                        Post job opportunities, manage job listings,
                        view student applications and update
                        candidate recruitment statuses.
                    </p>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-primary w-100">
                        Company Login →
                    </a>

                </div>

            </div>

            <!-- ADMIN PORTAL -->
            <div class="col-md-4">

                <div class="portal-card">

                    <div class="portal-icon">🛡️</div>

                    <h4>Admin Portal</h4>

                    <p>
                        Manage student and company records,
                        monitor job postings and oversee
                        placement activities.
                    </p>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-primary w-100">
                        Admin Login →
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ABOUT SECTION -->
<section class="section-padding" id="about">

    <div class="container">

        <div class="section-heading text-center">

            <span>ABOUT OUR PLATFORM</span>

            <h2>Connecting Talent with Opportunities</h2>

            <p>
                CareerConnect brings students, companies and
                placement administrators together through
                one integrated placement platform.
            </p>

        </div>

        <div class="row mt-5 g-4">

            <div class="col-md-4">

                <div class="info-card">

                    <div class="info-icon">👨‍🎓</div>

                    <h4>For Students</h4>

                    <p>
                        Build your profile, upload your resume,
                        explore job openings and track
                        your applications.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="info-card">

                    <div class="info-icon">🏢</div>

                    <h4>For Companies</h4>

                    <p>
                        Publish job openings, review applicants
                        and manage recruitment activities
                        through your company dashboard.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="info-card">

                    <div class="info-icon">📊</div>

                    <h4>For Administrators</h4>

                    <p>
                        Manage platform records, monitor
                        recruitment activities and oversee
                        placement operations.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- JOBS SECTION -->
<section class="section-padding jobs-section" id="jobs">

    <div class="container">

        <div class="section-heading text-center">

            <span>CAREER OPPORTUNITIES</span>

            <h2>Explore Featured Jobs</h2>

            <p>
                Discover different types of opportunities
                available for aspiring professionals.
            </p>

        </div>

        <div class="row g-4 mt-4">

            <div class="col-md-4">

                <div class="job-card">

                    <span class="job-category">IT & SOFTWARE</span>

                    <h4>Junior Software Developer</h4>

                    <p>ABC Technologies</p>

                    <div class="job-details">
                        <span>📍 Remote / Hybrid</span>
                        <span>💼 Full Time</span>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-outline-primary w-100 mt-4">
                        Login to Apply
                    </a>

                </div>

            </div>

            <div class="col-md-4">

                <div class="job-card">

                    <span class="job-category">DATA & ANALYTICS</span>

                    <h4>Data Analyst Intern</h4>

                    <p>XYZ Solutions</p>

                    <div class="job-details">
                        <span>📍 On-site</span>
                        <span>💼 Internship</span>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-outline-primary w-100 mt-4">
                        Login to Apply
                    </a>

                </div>

            </div>

            <div class="col-md-4">

                <div class="job-card">

                    <span class="job-category">WEB DEVELOPMENT</span>

                    <h4>PHP Web Developer</h4>

                    <p>Tech Innovations</p>

                    <div class="job-details">
                        <span>📍 Hybrid</span>
                        <span>💼 Full Time</span>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/auth/login.php"
                       class="btn btn-outline-primary w-100 mt-4">
                        Login to Apply
                    </a>

                </div>

            </div>

        </div>

        <p class="text-center text-muted mt-4">
            The above listings are illustrative examples.
            Actual job postings can be explored after student login.
        </p>

    </div>

</section>


<!-- FEATURES SECTION -->
<section class="section-padding" id="features">

    <div class="container">

        <div class="section-heading text-center">

            <span>OUR KEY FEATURES</span>

            <h2>Everything You Need in One Platform</h2>

            <p>
                Useful tools designed to simplify the
                placement and recruitment process.
            </p>

        </div>

        <div class="row g-4 mt-4">

            <div class="col-md-4">

                <div class="feature-card">
                    <div class="info-icon">📄</div>

                    <h4>Resume Management</h4>

                    <p>
                        Upload and maintain your resume
                        for job applications.
                    </p>
                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">
                    <div class="info-icon">🤖</div>

                    <h4>AI Job Recommendations</h4>

                    <p>
                        Discover relevant job opportunities
                        based on your skills using AI.
                    </p>
                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">
                    <div class="info-icon">📌</div>

                    <h4>Application Tracking</h4>

                    <p>
                        Monitor application progress and
                        view recruitment status updates.
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>


<!-- CONTACT / CTA SECTION -->
<section class="contact-section section-padding" id="contact">

    <div class="container text-center">

        <span class="cta-label">TAKE THE NEXT STEP</span>

        <h2>Build Your Future with CareerConnect</h2>

        <p>
            Start exploring career opportunities
            and move closer to your professional goals.
        </p>

        <a href="<?php echo BASE_URL; ?>/auth/register.php"
           class="btn btn-light btn-lg mt-3">
            Get Started →
        </a>

    </div>

</section>


<?php

require_once __DIR__ . "/includes/footer.php";

?>