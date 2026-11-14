
<?php
session_start();

require_once "../config/database.php";

// Admin login protection
if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../auth/login.php");
    exit();
}

// Get dashboard statistics
$students = $conn->query(
    "SELECT COUNT(*) FROM students"
)->fetchColumn();

$companies = $conn->query(
    "SELECT COUNT(*) FROM companies"
)->fetchColumn();

$jobs = $conn->query(
    "SELECT COUNT(*) FROM jobs"
)->fetchColumn();

$applications = $conn->query(
    "SELECT COUNT(*) FROM applications"
)->fetchColumn();

$placements = $conn->query(
    "SELECT COUNT(*) FROM placements"
)->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2>Admin Dashboard</h2>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
    </p>

    <hr>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="card text-center p-3">
                <h5>Total Students</h5>
                <h2><?php echo $students; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center p-3">
                <h5>Total Companies</h5>
                <h2><?php echo $companies; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center p-3">
                <h5>Total Jobs</h5>
                <h2><?php echo $jobs; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center p-3">
                <h5>Total Applications</h5>
                <h2><?php echo $applications; ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center p-3">
                <h5>Total Placements</h5>
                <h2><?php echo $placements; ?></h2>
            </div>
        </div>

    </div>

    <hr>

    <h4>Admin Activities</h4>

    <ul>
        <li><a href="manage_students.php">Manage Students</a></li>
        <li><a href="manage_companies.php">Manage Companies</a></li>
        <li><a href="manage_jobs.php">Manage Jobs</a></li>
    </ul>

    <a href="../auth/logout.php" class="btn btn-danger">
        Logout
    </a>

</div>

</body>
</html>
