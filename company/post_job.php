
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
    exit("Company profile not found.");
}

$company_id = $company["company_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $job_title = trim($_POST["job_title"]);
    $job_description = trim($_POST["job_description"]);
    $location = trim($_POST["location"]);
    $job_type = $_POST["job_type"];
    $salary = trim($_POST["salary"]);
    $eligibility = trim($_POST["eligibility"]);
    $application_deadline = $_POST["application_deadline"];

    $allowed_types = ["Full Time", "Part Time", "Internship"];

    if (
        $job_title === "" ||
        $job_description === "" ||
        $location === "" ||
        $salary === "" ||
        $eligibility === ""
    ) {
        $message = "Please fill in all required fields.";
    }
    elseif (!in_array($job_type, $allowed_types, true)) {
        $message = "Please select a valid job type.";
    }
    elseif (
        $application_deadline !== "" &&
        $application_deadline < date("Y-m-d")
    ) {
        $message = "Application deadline cannot be in the past.";
    }
    else {
        $sql = "INSERT INTO jobs
                (company_id, job_title, job_description, location,
                 job_type, salary, eligibility, application_deadline)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            $company_id,
            $job_title,
            $job_description,
            $location,
            $job_type,
            $salary,
            $eligibility,
            $application_deadline !== "" ? $application_deadline : null
        ]);

        $message = "Job posted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Post a Job - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow p-4">

        <h2 class="mb-4">Post a New Job</h2>

        <?php if ($message !== ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input type="text" name="job_title"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Job Description</label>
                <textarea name="job_description"
                          class="form-control" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" name="location"
                       class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Job Type</label>
                <select name="job_type" class="form-select" required>
                    <option value="">Select Job Type</option>
                    <option value="Full Time">Full Time</option>
                    <option value="Part Time">Part Time</option>
                    <option value="Internship">Internship</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Salary / Package</label>
                <input type="text" name="salary"
                       class="form-control"
                       placeholder="Example: 3–5 LPA" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Eligibility Criteria</label>
                <textarea name="eligibility"
                          class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Application Deadline</label>
                <input type="date" name="application_deadline"
                       class="form-control"
                       min="<?php echo date('Y-m-d'); ?>">
            </div>

            <button type="submit" class="btn btn-primary">
                Publish Job
            </button>

            <a href="dashboard.php" class="btn btn-secondary">
                Back to Dashboard
            </a>

        </form>

    </div>

</div>

</body>
</html>
