
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard - CareerConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">
        <h1>Welcome to CareerConnect!</h1>

        <h4>
            Hello, <?php echo htmlspecialchars($_SESSION["full_name"]); ?>!
        </h4>

        <p>You have successfully logged in.</p>

        <hr>

        <h5>Student Dashboard</h5>

        <ul>
            <li><a href="profile.php">My Profile</a></li>
            <li><a href="upload_resume.php">Upload Resume</a></li>
            <li><a href="jobs.php">Explore Jobs</a></li>
            <li><a href="applications.php">View Applications</a></li>
            <li><a href="recommendations.php">AI Job Recommendations</a></li>
        </ul>

        <a href="../auth/logout.php" class="btn btn-danger">
            Logout
        </a>
    </div>

</div>

</body>
</html>
