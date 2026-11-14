
<?php
session_start();
require_once "../config/database.php";

// Check whether the student is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$message = "";

// Update profile when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $phone = trim($_POST["phone"]);
    $course = trim($_POST["course"]);
    $specialization = trim($_POST["specialization"]);
    $graduation_year = $_POST["graduation_year"];
    $cgpa = $_POST["cgpa"];

    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $message = "Enter a valid phone number (10–15 digits).";
    }
    elseif (!is_numeric($cgpa) || $cgpa < 0 || $cgpa > 10) {
        $message = "CGPA must be between 0 and 10.";
    }
    elseif (!preg_match("/^[0-9]{4}$/", $graduation_year)) {
        $message = "Enter a valid graduation year.";
    }
    else {
        $sql = "UPDATE students
                SET phone = ?, course = ?, specialization = ?,
                    graduation_year = ?, cgpa = ?
                WHERE user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $phone,
            $course,
            $specialization,
            $graduation_year,
            $cgpa,
            $user_id
        ]);

        $message = "Profile updated successfully!";
    }
}

// Fetch existing profile details
$sql = "SELECT * FROM students WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$student = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow p-4">

        <h2 class="mb-4">My Student Profile</h2>

        <?php if ($message != ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control"
                       value="<?php echo htmlspecialchars($student["phone"] ?? ""); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">Course</label>
                <input type="text" name="course" class="form-control"
                       value="<?php echo htmlspecialchars($student["course"] ?? ""); ?>"
                       placeholder="Example: BCA" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" class="form-control"
                       value="<?php echo htmlspecialchars($student["specialization"] ?? ""); ?>"
                       placeholder="Example: AI and Data Science" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Graduation Year</label>
                <input type="number" name="graduation_year"
                       class="form-control" min="2020" max="2100"
                       value="<?php echo htmlspecialchars($student["graduation_year"] ?? ""); ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label">CGPA (out of 10)</label>
                <input type="number" name="cgpa" class="form-control"
                       step="0.01" min="0" max="10"
                       value="<?php echo htmlspecialchars($student["cgpa"] ?? ""); ?>"
                       required>
            </div>

            <button type="submit" class="btn btn-primary">
                Save Profile
            </button>

            <a href="dashboard.php" class="btn btn-secondary">
                Back to Dashboard
            </a>

        </form>

    </div>

</div>

</body>
</html>
