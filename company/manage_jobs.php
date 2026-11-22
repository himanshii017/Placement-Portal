
<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Check company login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: login.php");
    exit();
}

// Get company ID
$stmt = $conn->prepare(
    "SELECT company_id FROM companies WHERE user_id = ?"
);
$stmt->execute([$_SESSION["user_id"]]);

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    exit("Company profile not found.");
}

$company_id = $company["company_id"];
$message = "";

// Close a job
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["close_job"])) {

    $job_id = filter_input(INPUT_POST, "job_id", FILTER_VALIDATE_INT);

    if (!$job_id || $job_id <= 0) {
        $message = "Invalid job selected.";
    } else {

        $update = $conn->prepare(
            "UPDATE jobs
             SET status = 'Closed'
             WHERE job_id = ? AND company_id = ? AND status = 'Open'"
        );

        $update->execute([$job_id, $company_id]);

        if ($update->rowCount() > 0) {
            $message = "Job closed successfully.";
        } else {
            $message = "Job was not updated. It may already be closed.";
        }
    }
}

// Fetch jobs posted by this company
$stmt = $conn->prepare(
    "SELECT job_id, job_title, location, job_type,
            application_deadline, status
     FROM jobs
     WHERE company_id = ?
     ORDER BY created_at DESC"
);

$stmt->execute([$company_id]);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Manage Jobs | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <h2>Manage Posted Jobs</h2>

    <p class="text-muted">
        View and manage the jobs posted by your company.
    </p>

    <div class="mb-4">

        <a href="dashboard.php" class="btn btn-secondary">
            Back to Dashboard
        </a>

        <a href="post_job.php" class="btn btn-primary">
            Post New Job
        </a>

    </div>

    <?php if ($message !== ""): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </div>
    <?php endif; ?>

    <?php if (count($jobs) > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-striped bg-white">

                <thead class="table-dark">
                    <tr>
                        <th>Job Title</th>
                        <th>Location</th>
                        <th>Job Type</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($jobs as $job): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars($job["job_title"], ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job["location"] ?? "Not specified", ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job["job_type"], ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($job["application_deadline"] ?? "Not specified", ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>

                                <?php if ($job["status"] === "Open"): ?>

                                    <span class="badge bg-success">Open</span>

                                <?php else: ?>

                                    <span class="badge bg-danger">Closed</span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($job["status"] === "Open"): ?>

                                    <form method="POST"
                                          onsubmit="return confirm('Are you sure you want to close this job?');">

                                        <input type="hidden"
                                               name="job_id"
                                               value="<?= (int) $job["job_id"]; ?>">

                                        <button type="submit"
                                                name="close_job"
                                                class="btn btn-danger btn-sm">
                                            Close Job
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span class="text-muted">
                                        No action available
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-warning">
            You have not posted any jobs yet.
        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>