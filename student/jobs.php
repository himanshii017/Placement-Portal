
<?php
session_start();
require_once "../config/database.php";

// Check student login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all open jobs
$sql = "SELECT jobs.*, companies.company_name
        FROM jobs
        INNER JOIN companies
        ON jobs.company_id = companies.company_id
        WHERE jobs.status = 'Open'
        AND (jobs.application_deadline IS NULL
             OR jobs.application_deadline >= CURDATE())
        ORDER BY jobs.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute();
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Explore Jobs - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Explore Job Opportunities</h2>

        <a href="dashboard.php" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>

    <?php if (count($jobs) > 0): ?>

        <div class="row">

            <?php foreach ($jobs as $job): ?>

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">
                        <div class="card-body">

                            <h4>
                                <?php echo htmlspecialchars($job["job_title"]); ?>
                            </h4>

                            <h6 class="text-primary">
                                <?php echo htmlspecialchars($job["company_name"]); ?>
                            </h6>

                            <p>
                                <strong>Location:</strong>
                                <?php echo htmlspecialchars($job["location"] ?? "Not specified"); ?>
                            </p>

                            <p>
                                <strong>Job Type:</strong>
                                <?php echo htmlspecialchars($job["job_type"]); ?>
                            </p>

                            <p>
                                <strong>Salary:</strong>
                                <?php echo htmlspecialchars($job["salary"] ?? "Not disclosed"); ?>
                            </p>

                            <p>
                                <?php echo nl2br(htmlspecialchars($job["job_description"] ?? "")); ?>
                            </p>

                            <p>
                                <strong>Eligibility:</strong>
                                <?php echo htmlspecialchars($job["eligibility"] ?? "Not specified"); ?>
                            </p>

                            <p>
                                <strong>Last Date:</strong>
                                <?php echo htmlspecialchars($job["application_deadline"] ?? "Not specified"); ?>
                            </p>

                            <a href="apply_job.php?job_id=<?php echo $job['job_id']; ?>"
                                class="btn btn-primary">
                                Apply Now
                            </a>

                        </div>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
            No job opportunities are currently available.
            Please check again later.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
