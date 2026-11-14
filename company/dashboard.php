
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: login.php");
    exit();
}

// Get company details
$sql = "SELECT * FROM companies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);

$company = $stmt->fetch();

if (!$company) {
    exit("Company profile not found.");
}

// Count jobs posted by this company
$sql = "SELECT COUNT(*) FROM jobs WHERE company_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$company["company_id"]]);

$total_jobs = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Company Dashboard - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h1>Company Dashboard</h1>

        <h4>
            Welcome, <?php echo htmlspecialchars($company["company_name"]); ?>!
        </h4>

        <p>Manage your recruitment activities through CareerConnect.</p>

        <hr>

        <div class="row">

            <div class="col-md-6 mb-3">
                <div class="card bg-primary text-white p-3">
                    <h5>Total Jobs Posted</h5>
                    <h2><?php echo $total_jobs; ?></h2>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card bg-success text-white p-3">
                    <h5>Recruitment Portal</h5>
                    <p>Manage your job opportunities.</p>
                </div>
            </div>

        </div>

        <hr>

        <h5>Company Activities</h5>

        <ul>
            <li><a href="post_job.php">Post a New Job</a></li>
            <li><a href="manage_jobs.php">Manage Posted Jobs</a></li>
            <li><a href="applicants.php">View Student Applications</a></li>
            <li><a href="applicants.php">Manage Applicants</a></li>
        </ul>

        <a href="../auth/logout.php" class="btn btn-danger">
            Logout
        </a>

    </div>

</div>

</body>
</html>
