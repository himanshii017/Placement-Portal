
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

// Helper function for safe HTML output
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$user_id = (int) $_SESSION["user_id"];

$applications = [];
$message = "";

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

        $message = "Student profile not found. Please complete your profile.";

    } else {

        $student_id = (int) $student["student_id"];

        // 2. Fetch all student applications
        $sql = "
            SELECT
                a.application_id,
                a.applied_at,
                a.status,
                j.job_title,
                j.location,
                j.job_type,
                c.company_name

            FROM applications a

            INNER JOIN jobs j
                ON a.job_id = j.job_id

            INNER JOIN companies c
                ON j.company_id = c.company_id

            WHERE a.student_id = ?

            ORDER BY a.applied_at DESC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([$student_id]);

        $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {

    error_log(
        "Student applications error: " . $e->getMessage()
    );

    $message = "Unable to load your applications. Please try again later.";
}

// Load common header
$pageTitle = "My Applications | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <!-- Page Heading -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="mb-2">My Job Applications</h2>

            <p class="text-muted mb-0">
                Track your job applications and recruitment progress.
            </p>
        </div>

        <a href="dashboard.php" class="btn btn-outline-secondary">
            Back to Dashboard
        </a>

    </div>

    <!-- Information Message -->
    <?php if (!empty($message)): ?>

        <div class="alert alert-info" role="alert">
            <?= e($message) ?>
        </div>

    <?php endif; ?>

    <!-- Application Summary -->
    <?php if (!empty($applications)): ?>

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-1">Application Summary</h5>

                <h3 class="text-primary mb-0">
                    <?= count($applications) ?>
                </h3>

                <p class="text-muted mb-0">
                    Total applications submitted
                </p>

            </div>

        </div>

        <!-- Applications Table -->
        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-dark">

                            <tr>
                                <th>Job Title</th>
                                <th>Company</th>
                                <th>Location</th>
                                <th>Job Type</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($applications as $application): ?>

                                <?php
                                // Assign badge colors according to status
                                $status = $application["status"];

                                $statusColors = [
                                    "Applied" => "primary",
                                    "Shortlisted" => "info",
                                    "Selected" => "success",
                                    "Rejected" => "danger"
                                ];

                                $badgeColor =
                                    $statusColors[$status] ?? "secondary";
                                ?>

                                <tr>

                                    <!-- Job Title -->
                                    <td>
                                        <strong>
                                            <?= e($application["job_title"]) ?>
                                        </strong>
                                    </td>

                                    <!-- Company -->
                                    <td>
                                        <?= e($application["company_name"]) ?>
                                    </td>

                                    <!-- Location -->
                                    <td>
                                        <?= e(
                                            $application["location"] ??
                                            "Not specified"
                                        ) ?>
                                    </td>

                                    <!-- Job Type -->
                                    <td>
                                        <?= e($application["job_type"]) ?>
                                    </td>

                                    <!-- Applied Date -->
                                    <td>
                                        <?= !empty($application["applied_at"])
                                            ? e(date(
                                                "d M Y",
                                                strtotime($application["applied_at"])
                                            ))
                                            : "Not available" ?>
                                    </td>

                                    <!-- Application Status -->
                                    <td>
                                        <span class="badge bg-<?= e($badgeColor) ?>">
                                            <?= e($status) ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php elseif (empty($message)): ?>

        <!-- Empty Applications Message -->
        <div class="card shadow-sm">

            <div class="card-body text-center py-5">

                <h5>No Applications Yet</h5>

                <p class="text-muted">
                    You have not applied for any jobs yet.
                    Explore available opportunities and start applying.
                </p>

                <a href="jobs.php" class="btn btn-primary">
                    Explore Jobs
                </a>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php
// Load common footer
require_once __DIR__ . "/../includes/footer.php";
?>