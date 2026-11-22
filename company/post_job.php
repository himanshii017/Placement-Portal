
<?php
session_start();

require_once __DIR__ . "/../config/database.php";

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

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    exit("Company profile not found.");
}

$company_id = $company["company_id"];
$message = "";

// Keep entered values if validation fails
$job_title = "";
$job_description = "";
$location = "";
$job_type = "";
$salary = "";
$eligibility = "";
$application_deadline = "";

// Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $job_title = trim($_POST["job_title"] ?? "");
    $job_description = trim($_POST["job_description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $job_type = $_POST["job_type"] ?? "";
    $salary = trim($_POST["salary"] ?? "");
    $eligibility = trim($_POST["eligibility"] ?? "");
    $application_deadline = $_POST["application_deadline"] ?? "";

    $allowed_types = ["Full Time", "Part Time", "Internship"];

    if (!hash_equals(
        $_SESSION["csrf_token"],
        $_POST["csrf_token"] ?? ""
    )) {
        $message = "Invalid form submission. Please try again.";
    }
    elseif (
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
        (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $application_deadline) ||
            !checkdate(
                (int) substr($application_deadline, 5, 2),
                (int) substr($application_deadline, 8, 2),
                (int) substr($application_deadline, 0, 4)
            )
        )
    ) {
        $message = "Please enter a valid application deadline.";
    }
    elseif (
        $application_deadline !== "" &&
        $application_deadline < date("Y-m-d")
    ) {
        $message = "Application deadline cannot be in the past.";
    }
    else {

        try {

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

            // Prevent duplicate submission on page refresh
            $_SESSION["job_message"] = "Job posted successfully!";
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

            header("Location: manage_jobs.php");
            exit();

        } catch (PDOException $e) {

            error_log("Job posting error: " . $e->getMessage());
            $message = "Unable to post the job. Please try again.";
        }
    }
}

// Display success message after redirect
if (isset($_SESSION["job_message"])) {
    $message = $_SESSION["job_message"];
    unset($_SESSION["job_message"]);
}

$pageTitle = "Post a Job | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <div class="card shadow-sm border-0 p-4">

        <h2 class="mb-4">Post a New Job</h2>

        <?php if ($message !== ""): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION["csrf_token"], ENT_QUOTES, "UTF-8"); ?>">

            <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input type="text"
                       name="job_title"
                       class="form-control"
                       value="<?= htmlspecialchars($job_title, ENT_QUOTES, "UTF-8"); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Job Description</label>
                <textarea name="job_description"
                          class="form-control"
                          rows="4"
                          required><?= htmlspecialchars($job_description, ENT_QUOTES, "UTF-8"); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text"
                       name="location"
                       class="form-control"
                       value="<?= htmlspecialchars($location, ENT_QUOTES, "UTF-8"); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Job Type</label>
                <select name="job_type" class="form-select" required>

                    <option value="">Select Job Type</option>

                    <?php foreach (["Full Time", "Part Time", "Internship"] as $type): ?>
                        <option value="<?= $type; ?>"
                            <?= $job_type === $type ? "selected" : ""; ?>>
                            <?= $type; ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Salary / Package</label>
                <input type="text"
                       name="salary"
                       class="form-control"
                       placeholder="Example: 3-5 LPA"
                       value="<?= htmlspecialchars($salary, ENT_QUOTES, "UTF-8"); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Eligibility Criteria</label>
                <textarea name="eligibility"
                          class="form-control"
                          rows="3"
                          required><?= htmlspecialchars($eligibility, ENT_QUOTES, "UTF-8"); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Application Deadline</label>
                <input type="date"
                       name="application_deadline"
                       class="form-control"
                       min="<?= date("Y-m-d"); ?>"
                       value="<?= htmlspecialchars($application_deadline, ENT_QUOTES, "UTF-8"); ?>">
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

<?php require_once __DIR__ . "/../includes/footer.php"; ?>