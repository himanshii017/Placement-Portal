

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

// Safe HTML output
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$user_id = (int) $_SESSION["user_id"];
$message = "";
$messageType = "info";
$job = null;
$alreadyApplied = false;
$applicationSubmitted = false;

// Get job ID from URL
$job_id = filter_input(
    INPUT_GET,
    "job_id",
    FILTER_VALIDATE_INT
);

// If submitted through POST, use the submitted job ID
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $job_id = filter_input(
        INPUT_POST,
        "job_id",
        FILTER_VALIDATE_INT
    );
}

if (!$job_id || $job_id <= 0) {
    exit("Invalid job selected.");
}

try {

    // 1. Get student ID
    $stmt = $conn->prepare(
        "SELECT student_id
         FROM students
         WHERE user_id = ?"
    );

    $stmt->execute([$user_id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        exit("Student profile not found. Please complete your profile.");
    }

    $student_id = (int) $student["student_id"];

    // 2. Generate CSRF token
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(
            random_bytes(32)
        );
    }

    // 3. Check whether job exists and is open
    $stmt = $conn->prepare(
        "SELECT job_id, job_title, location, job_type,
                application_deadline
         FROM jobs
         WHERE job_id = ?
         AND status = 'Open'
         AND (
             application_deadline IS NULL
             OR application_deadline >= CURDATE()
         )"
    );

    $stmt->execute([$job_id]);

    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {

        $message = "This job is no longer available.";
        $messageType = "warning";

    } else {

        // 4. Check existing application
        $stmt = $conn->prepare(
            "SELECT application_id
             FROM applications
             WHERE student_id = ?
             AND job_id = ?"
        );

        $stmt->execute([
            $student_id,
            $job_id
        ]);

        $existingApplication = $stmt->fetch(
            PDO::FETCH_ASSOC
        );

        if ($existingApplication) {

            $alreadyApplied = true;

            $message = "You have already applied for this job.";
            $messageType = "info";

        } elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

            // 5. Validate CSRF token
            $submittedToken = $_POST["csrf_token"] ?? "";

            if (
                !is_string($submittedToken) ||
                !hash_equals(
                    $_SESSION["csrf_token"],
                    $submittedToken
                )
            ) {

                $message = "Invalid form request. Please refresh the page and try again.";
                $messageType = "danger";

            } else {

                // 6. Submit application
                try {

                    $insertStmt = $conn->prepare(
                        "INSERT INTO applications
                         (student_id, job_id)
                         VALUES (?, ?)"
                    );

                    $insertStmt->execute([
                        $student_id,
                        $job_id
                    ]);

                    // Prevent accidental form resubmission
                    unset($_SESSION["csrf_token"]);

                    $_SESSION["application_message"] =
                        "Your job application was submitted successfully!";

                    $_SESSION["application_message_type"] =
                        "success";

                    header(
                        "Location: apply_job.php?job_id=" .
                        $job_id
                    );

                    exit();

                } catch (PDOException $e) {

                    error_log(
                        "Application submission error: " .
                        $e->getMessage()
                    );

                    if ($e->getCode() == "23000") {

                        $alreadyApplied = true;

                        $message = "You have already applied for this job.";
                        $messageType = "info";

                    } else {

                        $message = "Application failed. Please try again.";
                        $messageType = "danger";
                    }
                }
            }
        }
    }

} catch (PDOException $e) {

    error_log(
        "Apply job database error: " . $e->getMessage()
    );

    $message = "A database error occurred. Please try again later.";
    $messageType = "danger";
}

// Display success message after redirect
if (isset($_SESSION["application_message"])) {

    $message = $_SESSION["application_message"];

    $messageType = $_SESSION["application_message_type"] ??
        "success";

    $applicationSubmitted = true;

    unset($_SESSION["application_message"]);
    unset($_SESSION["application_message_type"]);
}

// Load common header
$pageTitle = "Apply for Job | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h2 class="mb-3">
                        Job Application
                    </h2>

                    <p class="text-muted">
                        Review the job details before confirming your application.
                    </p>

                    <?php if (!empty($message)): ?>

                        <div
                            class="alert alert-<?= e($messageType) ?>"
                            role="alert"
                        >
                            <?= e($message) ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($job): ?>

                        <!-- Job Details -->
                        <div class="bg-light rounded p-3 mb-4">

                            <h5>
                                <?= e($job["job_title"]) ?>
                            </h5>

                            <p class="mb-2">
                                <strong>Location:</strong>
                                <?= e($job["location"] ?? "Not specified") ?>
                            </p>

                            <p class="mb-2">
                                <strong>Job Type:</strong>
                                <?= e($job["job_type"]) ?>
                            </p>

                            <p class="mb-0">
                                <strong>Application Deadline:</strong>

                                <?= !empty($job["application_deadline"])
                                    ? e(date(
                                        "d M Y",
                                        strtotime($job["application_deadline"])
                                    ))
                                    : "Not specified" ?>
                            </p>

                        </div>

                    <?php endif; ?>

                    <!-- Application Confirmation -->
                    <?php if (
                        $job &&
                        !$alreadyApplied &&
                        !$applicationSubmitted &&
                        $messageType !== "danger"
                    ): ?>

                        <form method="POST" action="apply_job.php">

                            <input
                                type="hidden"
                                name="job_id"
                                value="<?= (int) $job_id ?>"
                            >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e($_SESSION["csrf_token"]) ?>"
                            >

                            <p>
                                Are you sure you want to apply
                                for this job?
                            </p>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Confirm Application
                            </button>

                        </form>

                    <?php endif; ?>

                    <a
                        href="jobs.php"
                        class="btn btn-outline-secondary mt-3"
                    >
                        Back to Jobs
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>