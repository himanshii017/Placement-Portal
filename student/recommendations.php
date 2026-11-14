
<?php
session_start();

require_once "../config/database.php";

// Check student login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

// Get student ID using logged-in user's ID
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT student_id FROM students WHERE user_id = ?"
);

$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student profile not found.");
}

$student_id = $student["student_id"];

// Connect to Python AI API
$api_url = "http://127.0.0.1:5000/recommend-from-database";

$data = json_encode([
    "student_id" => (int)$student_id
]);

$ch = curl_init($api_url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$curl_error = curl_error($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Decode API response
$result = null;
$error_message = "";

if ($response === false) {
    $error_message = "Unable to connect to the AI recommendation service. Make sure Flask is running.";
} else {
    $result = json_decode($response, true);

    if ($http_status !== 200) {
        $error_message = $result["error"] ?? "AI recommendation request failed.";
    } elseif (!is_array($result)) {
        $error_message = "Invalid response received from AI service.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AI Job Recommendations - CareerConnect</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

<div class="container py-5">

    <h2 class="mb-2">AI-Based Job Recommendations</h2>

    <p class="text-muted">
        Jobs recommended according to your skills using Artificial Intelligence.
    </p>

    <a href="dashboard.php" class="btn btn-secondary mb-4">
        Back to Dashboard
    </a>

    <?php if ($error_message !== ""): ?>

        <div class="alert alert-warning">
            <?php echo htmlspecialchars($error_message); ?>
        </div>

    <?php elseif (!empty($result["recommendations"])): ?>

        <div class="alert alert-success">
            AI recommendations generated successfully!
        </div>

        <h5>Your Skills:</h5>

        <div class="mb-4">

            <?php foreach ($result["student_skills"] as $skill): ?>

                <span class="badge bg-primary me-1 mb-1">
                    <?php echo htmlspecialchars($skill); ?>
                </span>

            <?php endforeach; ?>

        </div>

        <h4 class="mb-3">Recommended Jobs</h4>

        <?php foreach ($result["recommendations"] as $job): ?>

            <div class="card shadow-sm mb-3">

                <div class="card-body">

                    <h5 class="card-title">
                        <?php echo htmlspecialchars($job["job_title"]); ?>
                    </h5>

                    <p>
                        Job ID:
                        <?php echo (int)$job["job_id"]; ?>
                    </p>

                    <p>
                        AI Skill Similarity:

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                (string)$job["similarity"]
                            );
                            ?>%
                        </strong>
                    </p>

                    <a
                        href="apply_job.php?job_id=<?php echo (int)$job["job_id"]; ?>"
                        class="btn btn-primary"
                    >
                        Apply Now
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-info">
            No job recommendations are available currently.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
