
<?php
session_start();

require_once "../config/database.php";

// Admin access protection
if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";

// Close a job
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["close_job"])) {

    $job_id = (int) $_POST["job_id"];

    $stmt = $conn->prepare(
        "UPDATE jobs
         SET status = 'Closed'
         WHERE job_id = ?"
    );

    $stmt->execute([$job_id]);

    if ($stmt->rowCount() > 0) {
        $message = "Job closed successfully.";
    } else {
        $message = "Job was not updated. It may already be closed.";
    }
}

// Fetch all posted jobs
$sql = "
    SELECT
        jobs.*,
        companies.company_name
    FROM jobs
    INNER JOIN companies
        ON jobs.company_id = companies.company_id
    ORDER BY jobs.job_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Jobs - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2>Manage Jobs</h2>

    <p class="text-muted">
        View and manage all job postings submitted by companies.
    </p>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <?php if ($message !== ""): ?>

        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">
                Total Job Postings: <?php echo count($jobs); ?>
            </h5>

            <?php if (count($jobs) > 0): ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">

                            <tr>
                                <th>Job ID</th>
                                <th>Job Title</th>
                                <th>Company</th>
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
                                        <?php echo htmlspecialchars((string)$job["job_id"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($job["job_title"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($job["company_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($job["location"] ?? "Not specified"); ?>
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
                    No job postings available yet.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
