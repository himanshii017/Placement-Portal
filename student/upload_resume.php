
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

// Safe HTML output
function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$user_id = (int) $_SESSION["user_id"];

$message = "";
$messageType = "info";
$resume_path = "";

// Create CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}

try {

    // 1. Check student profile and existing resume
    $stmt = $conn->prepare(
        "SELECT student_id, resume_path
         FROM students
         WHERE user_id = ?"
    );

    $stmt->execute([$user_id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        exit("Student profile not found. Please complete your profile.");
    }

    $resume_path = $student["resume_path"] ?? "";

    // 2. Process resume upload
    if (
        $_SERVER["REQUEST_METHOD"] === "POST" &&
        isset($_FILES["resume"])
    ) {

        // Validate CSRF token
        $submittedToken = $_POST["csrf_token"] ?? "";

        if (
            !is_string($submittedToken) ||
            !hash_equals(
                $_SESSION["csrf_token"],
                $submittedToken
            )
        ) {

            $message = "Invalid form request. Please refresh and try again.";
            $messageType = "danger";

        } else {

            $file = $_FILES["resume"];

            // Check upload errors
            if ($file["error"] !== UPLOAD_ERR_OK) {

                $message = "Please select a valid PDF file.";
                $messageType = "danger";

            } elseif (
                !is_uploaded_file($file["tmp_name"])
            ) {

                $message = "Invalid file upload.";
                $messageType = "danger";

            } elseif (
                $file["size"] <= 0 ||
                $file["size"] > 2 * 1024 * 1024
            ) {

                $message = "File size must be between 1 byte and 2 MB.";
                $messageType = "danger";

            } else {

                // Check file extension
                $extension = strtolower(
                    pathinfo(
                        $file["name"],
                        PATHINFO_EXTENSION
                    )
                );

                // Check actual MIME type
                $fileInfo = new finfo(FILEINFO_MIME_TYPE);

                $mimeType = $fileInfo->file(
                    $file["tmp_name"]
                );

                // Check PDF file signature
                $fileHandle = fopen(
                    $file["tmp_name"],
                    "rb"
                );

                $fileSignature = fread(
                    $fileHandle,
                    5
                );

                fclose($fileHandle);

                if (
                    $extension !== "pdf" ||
                    $mimeType !== "application/pdf" ||
                    $fileSignature !== "%PDF-"
                ) {

                    $message = "Only valid PDF files are allowed.";
                    $messageType = "danger";

                } else {

                    // 3. Create upload directory
                    $uploadDir = __DIR__ . "/../uploads/resumes/";

                    if (
                        !is_dir($uploadDir) &&
                        !mkdir($uploadDir, 0755, true) &&
                        !is_dir($uploadDir)
                    ) {

                        throw new RuntimeException(
                            "Unable to create upload directory."
                        );
                    }

                    // 4. Generate unique filename
                    $fileName = "resume_" .
                        $user_id . "_" .
                        bin2hex(random_bytes(16)) .
                        ".pdf";

                    $destination = $uploadDir . $fileName;

                    $databasePath =
                        "uploads/resumes/" . $fileName;

                    // 5. Move uploaded file
                    if (
                        move_uploaded_file(
                            $file["tmp_name"],
                            $destination
                        )
                    ) {

                        try {

                            // 6. Update database
                            $updateStmt = $conn->prepare(
                                "UPDATE students
                                 SET resume_path = ?
                                 WHERE user_id = ?"
                            );

                            $updateStmt->execute([
                                $databasePath,
                                $user_id
                            ]);

                            // 7. Delete previous resume after DB update
                            if (
                                !empty($resume_path) &&
                                $resume_path !== $databasePath
                            ) {

                                $oldFile = realpath(
                                    __DIR__ . "/../" . $resume_path
                                );

                                $resumeDirectory = realpath(
                                    $uploadDir
                                );

                                if (
                                    $oldFile !== false &&
                                    $resumeDirectory !== false &&
                                    dirname($oldFile) === $resumeDirectory &&
                                    is_file($oldFile)
                                ) {

                                    unlink($oldFile);
                                }
                            }

                            $resume_path = $databasePath;

                            $message = "Resume uploaded successfully!";
                            $messageType = "success";

                            // Refresh CSRF token
                            $_SESSION["csrf_token"] = bin2hex(
                                random_bytes(32)
                            );

                        } catch (Throwable $e) {

                            // Remove new file if database update fails
                            if (is_file($destination)) {
                                unlink($destination);
                            }

                            throw $e;
                        }

                    } else {

                        $message = "Unable to upload resume. Please try again.";
                        $messageType = "danger";
                    }
                }
            }
        }
    }

} catch (Throwable $e) {

    error_log(
        "Resume upload error: " . $e->getMessage()
    );

    $message = "An error occurred while processing your resume.";
    $messageType = "danger";
}

// Load common header
$pageTitle = "Upload Resume | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="card shadow-sm border-0">

                <div class="card-body p-4">

                    <h2 class="mb-2">Upload Your Resume</h2>

                    <p class="text-muted mb-4">
                        Upload your latest resume to help companies
                        review your profile during recruitment.
                    </p>

                    <!-- Message -->
                    <?php if (!empty($message)): ?>

                        <div
                            class="alert alert-<?= e($messageType) ?>"
                            role="alert"
                        >
                            <?= e($message) ?>
                        </div>

                    <?php endif; ?>

                    <!-- Upload Form -->
                    <form
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION["csrf_token"]) ?>"
                        >

                        <div class="mb-3">

                            <label
                                for="resume"
                                class="form-label fw-semibold"
                            >
                                Choose Your Resume (PDF only)
                            </label>

                            <input
                                type="file"
                                name="resume"
                                id="resume"
                                class="form-control"
                                accept=".pdf,application/pdf"
                                required
                            >

                        </div>

                        <p class="text-muted small">
                            Maximum file size: 2 MB.
                            Only PDF documents are accepted.
                        </p>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Upload Resume
                        </button>

                        <a
                            href="dashboard.php"
                            class="btn btn-outline-secondary"
                        >
                            Back to Dashboard
                        </a>

                    </form>

                    <!-- Previously Uploaded Resume -->
                    <?php if (!empty($resume_path)): ?>

                        <hr class="my-4">

                        <h5>Previously Uploaded Resume</h5>

                        <p class="text-muted small">
                            Your current resume is available below.
                            Uploading another resume will replace it.
                        </p>

                        <a
                            href="<?= e("../" . $resume_path) ?>"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-outline-success btn-sm"
                        >
                            View My Resume
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>