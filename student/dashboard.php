<?php
session_start();

// Check whether the student is logged in
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "student"
) {
    header("Location: ../auth/login.php");
    exit();
}

$full_name = $_SESSION["full_name"] ?? "Student";

// Common header
$pageTitle = "Student Dashboard | CareerConnect";
require_once __DIR__ . "/../includes/header.php";
?>

<style>
    .welcome-section {
        background: linear-gradient(120deg, #1d4ed8, #4338ca);
        color: white;
        padding: 35px;
        border-radius: 15px;
        margin-bottom: 35px;
    }

    .welcome-section p {
        color: #e0e7ff;
        line-height: 1.7;
    }

    .dashboard-card {
        height: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        transition: 0.2s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .card-icon {
        font-size: 30px;
        margin-bottom: 15px;
    }

    .dashboard-card p {
        color: #64748b;
        line-height: 1.6;
    }

    .dashboard-card .card-body {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .dashboard-card .btn {
        margin-top: auto;
    }

    .ai-card {
        border-color: #93c5fd;
        background: #f8fbff;
    }
</style>

<div class="container py-5">

    <!-- Welcome Section -->
    <section class="welcome-section">

        <h2 class="fw-bold">
            Welcome back, <?= htmlspecialchars($full_name, ENT_QUOTES, "UTF-8") ?>!
        </h2>

        <p class="mb-0">
            Take the next step towards your career.
            Explore job opportunities, manage your profile,
            upload your resume and track your applications.
        </p>

    </section>

    <!-- Dashboard Heading -->
    <div class="mb-4">

        <h3 class="fw-bold">Student Dashboard</h3>

        <p class="text-muted">
            Choose a service to continue your placement journey.
        </p>

    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4">

        <!-- My Profile -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">👤</div>

                    <h5 class="fw-bold">My Profile</h5>

                    <p>
                        View and update your personal information,
                        academic details and skills.
                    </p>

                    <a href="profile.php" class="btn btn-outline-primary">
                        Manage Profile
                    </a>

                </div>
            </div>

        </div>

        <!-- Upload Resume -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">📄</div>

                    <h5 class="fw-bold">Upload Resume</h5>

                    <p>
                        Upload your latest resume to make your
                        profile available for job applications.
                    </p>

                    <a href="upload_resume.php" class="btn btn-outline-primary">
                        Upload Resume
                    </a>

                </div>
            </div>

        </div>

        <!-- Manage Skills -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">🛠️</div>

                     <h5 class="fw-bold">Manage My Skills</h5>

                    <p>
                        Add new skills, select multiple skills,
                        and update or remove your existing skills.
                    </p>

                    <a href="manage_skills.php" class="btn btn-outline-primary">
                        Manage Skills
                    </a>

                </div>
         </div>

        </div>

        <!-- Explore Jobs -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">💼</div>

                    <h5 class="fw-bold">Explore Jobs</h5>

                    <p>
                        Discover job opportunities posted
                        by registered companies.
                    </p>

                    <a href="jobs.php" class="btn btn-outline-primary">
                        Explore Jobs
                    </a>

                </div>
            </div>

        </div>

        <!-- My Applications -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">📋</div>

                    <h5 class="fw-bold">My Applications</h5>

                    <p>
                        Check your submitted applications
                        and track their current status.
                    </p>

                    <a href="applications.php" class="btn btn-outline-primary">
                        View Applications
                    </a>

                </div>
            </div>

        </div>

        <!-- AI Recommendations -->
        <div class="col-md-6 col-lg-4">

            <div class="card dashboard-card ai-card shadow-sm">
                <div class="card-body p-4">

                    <div class="card-icon">🤖</div>

                    <h5 class="fw-bold">AI Job Recommendations</h5>

                    <p>
                        Discover job opportunities that match
                        your skills using Artificial Intelligence.
                    </p>

                    <a href="recommendations.php" class="btn btn-primary">
                        View Recommendations
                    </a>

                </div>
            </div>

        </div>

    </div>

    <!-- Logout -->
    <div class="text-end mt-4">

        <a href="../auth/logout.php" class="btn btn-danger">
            Logout
        </a>

    </div>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>