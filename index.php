
<?php
$projectName = "CareerConnect";
$tagline = "Your Career. Your Future. Your Opportunity.";
$year = date("Y");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $projectName; ?> | Placement Portal</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">

            <a class="navbar-brand fw-bold" href="index.php">
                Career<span>Connect</span>
            </a>

            <button class="navbar-toggler" type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#jobs">Explore Jobs</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>

                </ul>

                <div class="ms-lg-3 d-flex gap-2">
                    <a href="auth/login.php" class="btn btn-outline-primary">
                        Login
                    </a>

                    <a href="auth/register.php" class="btn btn-primary">
                        Register
                    </a>
                </div>

            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <section class="hero-section" id="home">

        <div class="container">
            <div class="row align-items-center">

                <div class="col-lg-7">

                    <span class="hero-label">
                        YOUR CAREER JOURNEY STARTS HERE
                    </span>

                    <h1>
                        Find the Right Opportunity
                        <span>Build Your Future.</span>
                    </h1>

                    <p class="hero-description">
                        Connect with leading companies, discover career
                        opportunities and take the next step towards
                        your professional journey.
                    </p>

                    <div class="d-flex flex-wrap gap-3 mt-4">

                        <a href="#jobs" class="btn btn-primary btn-lg">
                            Explore Opportunities
                        </a>

                        <a href="auth/register.php"
                           class="btn btn-outline-primary btn-lg">
                            Join Now
                        </a>

                    </div>

                </div>

                <div class="col-lg-5 text-center mt-5 mt-lg-0">

                    <div class="hero-card">

                        <div class="hero-icon">🎓</div>

                        <h3>Start Your Career Journey</h3>

                        <p>
                            Discover opportunities that match
                            your skills and ambitions.
                        </p>

                        <div class="career-stat">
                            <div>
                                <h4>Smart</h4>
                                <small>Job Matching</small>
                            </div>

                            <div>
                                <h4>Easy</h4>
                                <small>Applications</small>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </section>


    <!-- About Section -->
    <section class="section-padding" id="about">

        <div class="container">

            <div class="section-heading text-center">

                <span>ABOUT OUR PLATFORM</span>

                <h2>Making Placement Easier</h2>

                <p>
                    A single platform to bring students,
                    companies and placement cells together.
                </p>

            </div>

            <div class="row mt-5 g-4">

                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">👨‍🎓</div>
                        <h4>For Students</h4>
                        <p>
                            Create your profile, upload your resume,
                            explore jobs and track applications.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">🏢</div>
                        <h4>For Companies</h4>
                        <p>
                            Publish job openings, find talented
                            candidates and manage applications.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-card">
                        <div class="info-icon">📊</div>
                        <h4>For Placement Cell</h4>
                        <p>
                            Manage student records, monitor
                            recruitment and track placement activities.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </section>


    <!-- Jobs Section -->
    <section class="section-padding jobs-section" id="jobs">

        <div class="container">

            <div class="section-heading text-center">

                <span>CAREER OPPORTUNITIES</span>

                <h2>Explore Featured Jobs</h2>

                <p>
                    Discover some example opportunities available
                    on our placement platform.
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

                        <a href="auth/login.php"
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

                        <a href="auth/login.php"
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

                        <a href="auth/login.php"
                           class="btn btn-outline-primary w-100 mt-4">
                            Login to Apply
                        </a>

                    </div>
                </div>

            </div>

            <p class="text-center text-muted mt-4">
                These are sample job listings. Live job postings
                will be connected to the database later.
            </p>

        </div>

    </section>


    <!-- Features Section -->
    <section class="section-padding" id="features">

        <div class="container">

            <div class="section-heading text-center">

                <span>WHY CAREERCONNECT?</span>

                <h2>Everything You Need in One Place</h2>

            </div>

            <div class="row g-4 mt-4">

                <div class="col-md-4">
                    <div class="feature-card">
                        <h4>📄 Resume Upload</h4>
                        <p>
                            Students can upload and manage
                            their resumes.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <h4>🤖 AI Job Recommendations</h4>
                        <p>
                            Find relevant opportunities based
                            on your skills.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="feature-card">
                        <h4>📌 Application Tracking</h4>
                        <p>
                            Keep track of application progress
                            and recruitment updates.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </section>


    <!-- Contact Section -->
    <section class="contact-section section-padding" id="contact">

        <div class="container text-center">

            <h2>Ready to Take the Next Step?</h2>

            <p>
                Create your account and start exploring
                career opportunities.
            </p>

            <a href="auth/register.php"
               class="btn btn-light btn-lg mt-3">
                Get Started
            </a>

        </div>

    </section>


    <!-- Footer -->
    <footer class="footer">

        <div class="container text-center">

            <h4>CareerConnect</h4>

            <p>
                Connecting students with career opportunities.
            </p>

            <p class="mb-0">
                &copy; <?php echo $year; ?> CareerConnect.
                All Rights Reserved.
            </p>

        </div>

    </footer>


    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

</body>
</html>
