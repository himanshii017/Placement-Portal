
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Get student ID
$stmt = $conn->prepare(
    "SELECT student_id FROM students WHERE user_id = ?"
);
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    exit("Student profile not found.");
}

$sql = "SELECT applications.*, jobs.job_title,
               jobs.location, jobs.job_type, companies.company_name
        FROM applications
        INNER JOIN jobs
            ON applications.job_id = jobs.job_id
        INNER JOIN companies
            ON jobs.company_id = companies.company_id
        WHERE applications.student_id = ?
        ORDER BY applications.applied_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$student["student_id"]]);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Applications - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4">My Job Applications</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <?php if (count($applications) > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-striped bg-white">

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

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($application["job_title"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($application["company_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($application["location"] ?? "Not specified"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($application["job_type"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($application["applied_at"]); ?>
                            </td>

                            <td>
                                <span class="badge bg-primary">
                                    <?php echo htmlspecialchars($application["status"]); ?>
                                </span>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
            You have not applied for any jobs yet.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
