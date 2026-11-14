
<?php
session_start();

require_once "../config/database.php";

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
$company = $stmt->fetch();

if (!$company) {
    die("Company profile not found.");
}

$company_id = $company["company_id"];
$message = "";

// Close a job
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["close_job"])) {

    $job_id = (int) $_POST["job_id"];

    $update = $conn->prepare(
        "UPDATE jobs SET status = 'Closed'
         WHERE job_id = ? AND company_id = ?"
    );

    $update->execute([$job_id, $company_id]);

    if ($update->rowCount() > 0) {
        $message = "Job closed successfully.";
    } else {
        $message = "Job was not updated. It may already be closed.";
    }
}

// Fetch company's jobs
$stmt = $conn->prepare(
    "SELECT * FROM jobs
     WHERE company_id = ?
     ORDER BY created_at DESC"
);

$stmt->execute([$company_id]);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Jobs - CareerConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2>Manage Posted Jobs</h2>
    <p>View and manage the jobs posted by your company.</p>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <a href="post_job.php" class="btn btn-primary mb-3">
        Post New Job
    </a>

    <?php if ($message !== ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
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
                            <?php echo htmlspecialchars($job["job_title"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($job["location"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($job["job_type"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($job["application_deadline"] ?? "Not specified"); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($job["status"]); ?>
                        </td>

                        <td>

                            <?php if ($job["status"] === "Open"): ?>

                                <form method="POST"
                                      onsubmit="return confirm('Are you sure you want to close this job?');">

                                    <input type="hidden"
                                           name="job_id"
                                           value="<?php echo $job["job_id"]; ?>">

                                    <button type="submit"
                                            name="close_job"
                                            class="btn btn-danger btn-sm">
                                        Close Job
                                    </button>

                                </form>

                            <?php else: ?>

                                <span class="text-muted">No action available</span>

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

</body>
</html>
