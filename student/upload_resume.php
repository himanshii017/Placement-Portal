
<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["resume"])) {

    $file = $_FILES["resume"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $message = "Please select a valid file.";
    }
    elseif ($file["size"] > 2 * 1024 * 1024) {
        $message = "File size must not exceed 2 MB.";
    }
    else {
        // Check the actual file type
        $fileInfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($file["tmp_name"]);

        if ($mimeType !== "application/pdf") {
            $message = "Only PDF files are allowed.";
        }
        else {
            $uploadDir = "../uploads/resumes/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate a unique filename
            $fileName = "resume_" . $user_id . "_" . bin2hex(random_bytes(8)) . ".pdf";

            $destination = $uploadDir . $fileName;
            $databasePath = "uploads/resumes/" . $fileName;

            if (move_uploaded_file($file["tmp_name"], $destination)) {

                $sql = "UPDATE students SET resume_path = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$databasePath, $user_id]);

                $message = "Resume uploaded successfully!";

            } else {
                $message = "Unable to upload resume. Please try again.";
            }
        }
    }
}

// Get previously uploaded resume
$stmt = $conn->prepare(
    "SELECT resume_path FROM students WHERE user_id = ?"
);
$stmt->execute([$user_id]);
$student = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Resume - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow p-4">

        <h2 class="mb-3">Upload Your Resume</h2>

        <?php if ($message !== ""): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Choose Resume (PDF only)</label>

                <input type="file"
                       name="resume"
                       class="form-control"
                       accept=".pdf,application/pdf"
                       required>
            </div>

            <p class="text-muted">
                Maximum file size: 2 MB.
            </p>

            <button type="submit" class="btn btn-primary">
                Upload Resume
            </button>

            <a href="dashboard.php" class="btn btn-secondary">
                Back to Dashboard
            </a>

        </form>

        <?php if (!empty($student["resume_path"])): ?>
            <hr>
            <h5>Previously Uploaded Resume</h5>

            <a href="../<?php echo htmlspecialchars($student["resume_path"]); ?>"
               target="_blank" rel="noopener">
                View My Resume
            </a>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
