
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

// Update application status
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $application_id = filter_input(
        INPUT_POST,
        "application_id",
        FILTER_VALIDATE_INT
    );

    $status = $_POST["status"] ?? "";

    $allowed_statuses = [
        "Applied",
        "Shortlisted",
        "Selected",
        "Rejected"
    ];

    if (
        $application_id &&
        in_array($status, $allowed_statuses, true)
    ) {
        // Ensure this application belongs to this company's job
        $sql = "UPDATE applications
                INNER JOIN jobs
                    ON applications.job_id = jobs.job_id
                SET applications.status = ?
                WHERE applications.application_id = ?
                AND jobs.company_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$status, $application_id, $company_id]);

        if ($stmt->rowCount() > 0) {
            $message = "Application status updated successfully!";
        } else {
            $message = "No changes made, or application not found.";
        }
    } else {
        $message = "Invalid application details.";
    }
}

// Fetch applicants for this company's jobs
$sql = "SELECT applications.application_id,
               applications.applied_at,
               applications.status,
               users.full_name,
               users.email,
               students.phone,
               students.course,
               students.specialization,
               students.cgpa,
               students.resume_path,
               jobs.job_title
        FROM applications
        INNER JOIN students
            ON applications.student_id = students.student_id
        INNER JOIN users
            ON students.user_id = users.user_id
        INNER JOIN jobs
            ON applications.job_id = jobs.job_id
        WHERE jobs.company_id = ?
        ORDER BY applications.applied_at DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$company_id]);
$applicants = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Applicants - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <h2 class="mb-4">Manage Student Applications</h2>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <?php if ($message !== ""): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (count($applicants) > 0): ?>

        <div class="table-responsive">

            <table class="table table-bordered table-striped bg-white">

                <thead class="table-dark">
                    <tr>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>CGPA</th>
                        <th>Job</th>
                        <th>Resume</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($applicants as $applicant): ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($applicant["full_name"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($applicant["email"]); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($applicant["course"] ?? "Not provided"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($applicant["cgpa"] ?? "Not provided"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($applicant["job_title"]); ?>
                            </td>

                            <td>
                                <?php if (!empty($applicant["resume_path"])): ?>
                                    <a href="../<?php echo htmlspecialchars($applicant["resume_path"]); ?>"
                                       target="_blank" rel="noopener">
                                        View Resume
                                    </a>
                                <?php else: ?>
                                    Not uploaded
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($applicant["status"]); ?>
                            </td>

                            <td>
                                <form method="POST">

                                    <input type="hidden"
                                           name="application_id"
                                           value="<?php echo $applicant["application_id"]; ?>">

                                    <select name="status" class="form-select mb-2">

                                        <option value="Applied"
                                            <?php if ($applicant["status"] == "Applied") echo "selected"; ?>>
                                            Applied
                                        </option>

                                        <option value="Shortlisted"
                                            <?php if ($applicant["status"] == "Shortlisted") echo "selected"; ?>>
                                            Shortlisted
                                        </option>

                                        <option value="Selected"
                                            <?php if ($applicant["status"] == "Selected") echo "selected"; ?>>
                                            Selected
                                        </option>

                                        <option value="Rejected"
                                            <?php if ($applicant["status"] == "Rejected") echo "selected"; ?>>
                                            Rejected
                                        </option>

                                    </select>

                                    <button type="submit"
                                            class="btn btn-primary btn-sm">
                                        Update
                                    </button>

                                </form>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
            No students have applied for your jobs yet.
        </div>

    <?php endif; ?>

</div>

</body>
</html>
