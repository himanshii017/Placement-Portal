
<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Check student authentication
if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "student"
) {
    header("Location: ../auth/login.php");
    exit();
}

// Safe output function
function e($value) {
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

$user_id = (int) $_SESSION["user_id"];

$message = "";
$messageType = "info";

// Generate CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}

try {

    // Fetch student profile
    $stmt = $conn->prepare(
        "SELECT * FROM students WHERE user_id = ?"
    );

    $stmt->execute([$user_id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        exit(
            "Student profile not found. Please contact the administrator."
        );
    }

    // Update profile
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        $token = $_POST["csrf_token"] ?? "";

        if (
            !is_string($token) ||
            !hash_equals($_SESSION["csrf_token"], $token)
        ) {

            $message = "Invalid form request. Please refresh the page.";
            $messageType = "danger";

        } else {

            $phone = trim($_POST["phone"] ?? "");
            $course = trim($_POST["course"] ?? "");
            $specialization = trim($_POST["specialization"] ?? "");
            $graduation_year = trim($_POST["graduation_year"] ?? "");
            $cgpa = trim($_POST["cgpa"] ?? "");

            // Validate phone number
            if (!preg_match("/^[0-9]{10,15}$/", $phone)) {

                $message = "Enter a valid phone number (10-15 digits).";
                $messageType = "danger";

            } elseif (
                $course === "" ||
                strlen($course) > 100 ||
                $specialization === "" ||
                strlen($specialization) > 150
            ) {

                $message = "Enter valid course and specialization details.";
                $messageType = "danger";

            } elseif (
                filter_var($graduation_year, FILTER_VALIDATE_INT) === false ||
                (int) $graduation_year < 2020 ||
                (int) $graduation_year > 2100
            ) {

                $message = "Enter a valid graduation year.";
                $messageType = "danger";

            } elseif (
                !is_numeric($cgpa) ||
                !is_finite((float) $cgpa) ||
                (float) $cgpa < 0 ||
                (float) $cgpa > 10 ||
                !preg_match('/^\d{1,2}(\.\d{1,2})?$/', $cgpa)
            ) {

                $message = "CGPA must be between 0 and 10 (maximum 2 decimal places).";
                $messageType = "danger";

            } else {

                // Update student details
                $updateStmt = $conn->prepare(
                    "UPDATE students
                     SET phone = ?,
                         course = ?,
                         specialization = ?,
                         graduation_year = ?,
                         cgpa = ?
                     WHERE user_id = ?"
                );

                $updateStmt->execute([
                    $phone,
                    $course,
                    $specialization,
                    (int) $graduation_year,
                    (float) $cgpa,
                    $user_id
                ]);

                $message = "Your profile has been updated successfully!";
                $messageType = "success";

                // Refresh CSRF token
                $_SESSION["csrf_token"] = bin2hex(
                    random_bytes(32)
                );

                // Refresh student details
                $stmt = $conn->prepare(
                    "SELECT * FROM students WHERE user_id = ?"
                );

                $stmt->execute([$user_id]);

                $student = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }
    }

    // Fetch student skills
    $skillStmt = $conn->prepare(
        "SELECT s.skill_name
         FROM student_skills ss
         INNER JOIN skills s
             ON ss.skill_id = s.skill_id
         WHERE ss.student_id = ?
         ORDER BY s.skill_name ASC"
    );

    $skillStmt->execute([
        (int) $student["student_id"]
    ]);

    $skills = $skillStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {

    error_log("Student profile error: " . $e->getMessage());

    $message = "Unable to process your profile. Please try again.";
    $messageType = "danger";

    $student = $student ?? [];
    $skills = $skills ?? [];
}

// Common header
$pageTitle = "My Profile | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-9">

            <!-- Page Heading -->
            <div class="mb-4">

                <h2 class="fw-bold">My Student Profile</h2>

                <p class="text-muted">
                    Manage your personal and academic information
                    to improve your placement opportunities.
                </p>

            </div>

            <!-- Status Message -->
            <?php if ($message !== ""): ?>

                <div
                    class="alert alert-<?= e($messageType) ?>"
                    role="alert"
                >
                    <?= e($message) ?>
                </div>

            <?php endif; ?>

            <!-- Profile Form -->
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body p-4">

                    <h4 class="mb-4">
                        Academic and Personal Details
                    </h4>

                    <form method="POST" action="profile.php">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION["csrf_token"]) ?>"
                        >

                        <div class="row g-3">

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Phone Number
                                </label>

                                <input
                                    type="tel"
                                    name="phone"
                                    class="form-control"
                                    value="<?= e($student["phone"] ?? "") ?>"
                                    pattern="[0-9]{10,15}"
                                    minlength="10"
                                    maxlength="15"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Course
                                </label>

                                <input
                                    type="text"
                                    name="course"
                                    class="form-control"
                                    value="<?= e($student["course"] ?? "") ?>"
                                    maxlength="100"
                                    placeholder="Example: BCA"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Specialization
                                </label>

                                <input
                                    type="text"
                                    name="specialization"
                                    class="form-control"
                                    value="<?= e($student["specialization"] ?? "") ?>"
                                    maxlength="150"
                                    placeholder="Example: AI and Data Science"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    Graduation Year
                                </label>

                                <input
                                    type="number"
                                    name="graduation_year"
                                    class="form-control"
                                    min="2020"
                                    max="2100"
                                    value="<?= e($student["graduation_year"] ?? "") ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label class="form-label fw-semibold">
                                    CGPA (Out of 10)
                                </label>

                                <input
                                    type="number"
                                    name="cgpa"
                                    class="form-control"
                                    step="0.01"
                                    min="0"
                                    max="10"
                                    value="<?= e($student["cgpa"] ?? "") ?>"
                                    required
                                >

                                <small class="text-muted">
                                    Enter your current overall CGPA.
                                </small>

                            </div>

                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Save Profile
                            </button>

                            <a
                                href="dashboard.php"
                                class="btn btn-outline-secondary"
                            >
                                Back to Dashboard
                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <!-- Technical Skills -->
            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">

                    <h4 class="mb-3">
                        My Technical Skills
                    </h4>

                    <?php if (!empty($skills)): ?>

                        <div class="d-flex flex-wrap gap-2">

                            <?php foreach ($skills as $skill): ?>

                                <span class="badge bg-primary-subtle text-primary p-2">
                                    <?= e($skill) ?>
                                </span>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <p class="text-muted mb-0">
                            No skills have been added to your profile yet.
                            Add skills to improve your AI job recommendations.
                        </p>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>