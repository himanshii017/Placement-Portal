
<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Check student authentication
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "student"
) {
    header("Location: ../auth/login.php");
    exit();
}

// Safe output function
function e($value) {
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$jobs = [];
$error = "";

try {

    // Fetch available jobs from the database
    $sql = "SELECT
                j.job_id,
                j.job_title,
                j.job_description,
                j.location,
                j.job_type,
                j.salary,
                j.eligibility,
                j.application_deadline,
                j.created_at,
                c.company_name
            FROM jobs j
            INNER JOIN companies c
                ON j.company_id = c.company_id
            WHERE j.status = 'Open'
              AND (
                  j.application_deadline IS NULL
                  OR j.application_deadline >= CURDATE()
              )
            ORDER BY j.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $ex) {

    error_log("Jobs page error: " . $ex->getMessage());

    $error = "Unable to load job opportunities. Please try again later.";
}

// Common header
$pageTitle = "Explore Jobs | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<style>

    .jobs-page {
        min-height: 70vh;
    }

    .jobs-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 30px;
    }

    .jobs-heading h2 {
        font-weight: 700;
        color: #172554;
    }

    .jobs-subtitle {
        color: #64748b;
        margin-top: 8px;
    }

    .job-card {
        height: 100%;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .job-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,0.08);
    }

    .job-title {
        font-size: 21px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .company-name {
        color: #2563eb;
        font-weight: 600;
        margin-bottom: 18px;
    }

    .job-info {
        color: #475569;
        font-size: 14px;
        line-height: 1.9;
        margin-bottom: 15px;
    }

    .job-description {
        color: #475569;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 18px;
        overflow-wrap: anywhere;
    }

    .job-section-title {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }

    .job-type {
        display: inline-block;
        width: fit-content;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .apply-button {
        margin-top: auto;
        width: 100%;
        font-weight: 600;
        padding: 11px;
    }

    .empty-state {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 45px 20px;
        text-align: center;
    }

    .empty-state h4 {
        color: #1e293b;
        font-weight: 700;
    }

    .empty-state p {
        color: #64748b;
        margin-top: 10px;
    }

    @media (max-width: 576px) {
        .jobs-heading h2 {
            font-size: 23px;
        }

        .job-card {
            padding: 19px;
        }
    }

</style>

<div class="container py-5 jobs-page">

    <!-- Page Heading -->
    <div class="jobs-heading">

        <div>

            <h2>Explore Job Opportunities</h2>

            <p class="jobs-subtitle">
                Discover opportunities and take the next step
                towards your career.
            </p>

        </div>

        <a
            href="dashboard.php"
            class="btn btn-outline-secondary"
        >
            Back to Dashboard
        </a>

    </div>

    <!-- Error Message -->
    <?php if ($error !== ""): ?>

        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>

    <?php else: ?>

        <!-- Job Count -->
        <div class="mb-4">

            <span class="badge bg-primary fs-6">
                <?= count($jobs) ?> Jobs Available
            </span>

        </div>

        <?php if (!empty($jobs)): ?>

            <div class="row g-4">

                <?php foreach ($jobs as $job): ?>

                    <div class="col-lg-6">

                        <div class="job-card">

                            <!-- Job Title -->
                            <h4 class="job-title">
                                <?= e($job["job_title"]) ?>
                            </h4>

                            <!-- Company -->
                            <p class="company-name">
                                <?= e($job["company_name"]) ?>
                            </p>

                            <!-- Job Type -->
                            <span class="job-type">
                                <?= e($job["job_type"]) ?>
                            </span>

                            <!-- Job Details -->
                            <div class="job-info">

                                <div>
                                    <strong>Location:</strong>
                                    <?= e($job["location"] ?: "Not specified") ?>
                                </div>

                                <div>
                                    <strong>Salary:</strong>
                                    <?= e($job["salary"] ?: "Not disclosed") ?>
                                </div>

                                <div>
                                    <strong>Application Deadline:</strong>

                                    <?php if (!empty($job["application_deadline"])): ?>

                                        <?= e(
                                            date(
                                                "d M Y",
                                                strtotime($job["application_deadline"])
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        Not specified

                                    <?php endif; ?>

                                </div>

                            </div>

                            <!-- Description -->
                            <h6 class="job-section-title">
                                Job Description
                            </h6>

                            <p class="job-description">
                                <?= nl2br(e($job["job_description"])) ?>
                            </p>

                            <!-- Eligibility -->
                            <h6 class="job-section-title">
                                Eligibility
                            </h6>

                            <p class="job-description">
                                <?= e($job["eligibility"] ?: "Not specified") ?>
                            </p>

                            <!-- Apply Button -->
                            <a
                                href="apply_job.php?job_id=<?= (int) $job["job_id"] ?>"
                                class="btn btn-primary apply-button"
                            >
                                Apply Now
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <!-- No Jobs Available -->
            <div class="empty-state">

                <h4>No Jobs Available Right Now</h4>

                <p>
                    There are currently no open job opportunities.
                    Please check again later for new vacancies.
                </p>

                <a
                    href="dashboard.php"
                    class="btn btn-primary mt-3"
                >
                    Return to Dashboard
                </a>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>