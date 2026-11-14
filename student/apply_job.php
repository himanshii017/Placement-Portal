
<?php
session_start();
require_once "../config/database.php";

// Check student login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// Get student ID
$stmt = $conn->prepare(
    "SELECT student_id FROM students WHERE user_id = ?"
);
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    exit("Student profile not found.");
}

$student_id = $student["student_id"];

// Get job ID from URL
$job_id = filter_input(INPUT_GET, "job_id", FILTER_VALIDATE_INT);

if (!$job_id || $job_id <= 0) {
    exit("Invalid job selected.");
}

// Check whether the job is open
$stmt = $conn->prepare(
    "SELECT job_id FROM jobs
     WHERE job_id = ?
     AND status = 'Open'
     AND (application_deadline IS NULL
          OR application_deadline >= CURDATE())"
);
$stmt->execute([$job_id]);
$job = $stmt->fetch();

if (!$job) {
    exit("This job is no longer available.");
}

// Check for an existing application
$stmt = $conn->prepare(
    "SELECT application_id FROM applications
     WHERE student_id = ? AND job_id = ?"
);
$stmt->execute([$student_id, $job_id]);

if ($stmt->fetch()) {
    $message = "You have already applied for this job.";
}
else {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        try {
            $sql = "INSERT INTO applications (student_id, job_id)
                    VALUES (?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$student_id, $job_id]);

            $message = "Your job application was submitted successfully!";

        } catch (PDOException $e) {
            if ($e->getCode() == "23000") {
                $message = "You have already applied for this job.";
            } else {
                $message = "Application failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apply for Job - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h2>Job Application</h2>

        <?php if ($message !== ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (
            !$stmt->rowCount() && 
            $message === "" 
        ): ?>
            <p>Confirm that you want to apply for this job.</p>
        <?php endif; ?>

        <?php if (
            $message === "" ||
            $message === "You have already applied for this job."
        ): ?>

            <?php if ($message === ""): ?>
                <form method="POST">
                    <button type="submit" class="btn btn-primary">
                        Confirm Application
                    </button>
                </form>
            <?php endif; ?>

        <?php endif; ?>

        <a href="jobs.php" class="btn btn-secondary mt-3">
            Back to Jobs
        </a>

    </div>

</div>

</body>
</html>
