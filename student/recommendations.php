
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
$student_skills = [];
$recommendations = [];
$student_id = 0;

try {

    // 1. Fetch student ID
    $stmt = $conn->prepare(
        "SELECT student_id
         FROM students
         WHERE user_id = ?"
    );

    $stmt->execute([$user_id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {

        $message = "Student profile not found. Please complete your profile.";

    } else {

        $student_id = (int) $student["student_id"];

        // 2. Fetch student skills
        $skillStmt = $conn->prepare(
            "SELECT s.skill_name
             FROM student_skills ss
             INNER JOIN skills s
                ON ss.skill_id = s.skill_id
             WHERE ss.student_id = ?
             ORDER BY s.skill_name ASC"
        );

        $skillStmt->execute([$student_id]);

        $student_skills = $skillStmt->fetchAll(
            PDO::FETCH_COLUMN
        );

        if (empty($student_skills)) {

            $message = "No skills found for this student. Please add skills to your profile.";

        } else {

            // 3. Prepare Flask API request
            $api_url = "http://127.0.0.1:5000/recommend-from-database";

            $payload = json_encode([
                "student_id" => $student_id
            ]);

            if ($payload === false) {

                $message = "Unable to prepare the AI request.";

            } else {

                // 4. Initialize cURL
                $ch = curl_init($api_url);

                if ($ch === false) {

                    $message = "Unable to initialize the AI service.";

                } else {

                    curl_setopt_array($ch, [
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true,
                        CURLOPT_HTTPHEADER => [
                            "Content-Type: application/json",
                            "Accept: application/json"
                        ],
                        CURLOPT_POSTFIELDS => $payload,
                        CURLOPT_CONNECTTIMEOUT => 5,
                        CURLOPT_TIMEOUT => 20
                    ]);

                    // 5. Execute API request
                    $response = curl_exec($ch);

                    $curl_error = curl_error($ch);

                    $http_code = curl_getinfo(
                        $ch,
                        CURLINFO_HTTP_CODE
                    );

                    // PHP 8.5 compatible handle cleanup
                    unset($ch);

                    // 6. Handle connection errors
                    if ($response === false || $response === null) {

                        error_log(
                            "Flask connection error: " . $curl_error
                        );

                        $message = "Unable to connect to the AI recommendation service. Please try again later.";

                    } elseif ($http_code !== 200) {

                        error_log(
                            "Flask HTTP error: " . $http_code .
                            " Response: " . $response
                        );

                        $message = "The AI service is temporarily unavailable. Please try again later.";

                    } else {

                        // 7. Decode Flask JSON response
                        $result = json_decode($response, true);

                        if (json_last_error() !== JSON_ERROR_NONE ||
                            !is_array($result)) {

                            error_log(
                                "Invalid Flask JSON response: " . $response
                            );

                            $message = "Invalid response received from the AI service.";

                        } elseif (empty($result["success"])) {

                            $message = $result["message"] ??
                                "AI recommendations could not be generated.";

                        } else {

                            // 8. Fetch recommendations
                            $recommendations =
                                $result["recommendations"] ?? [];

                            if (!is_array($recommendations)) {

                                $recommendations = [];

                                $message = "Invalid recommendation data received.";

                            } elseif (empty($recommendations)) {

                                $message = "No matching jobs found for your skills.";
                            }
                        }
                    }
                }
            }
        }
    }

} catch (PDOException $e) {

    error_log(
        "Recommendation database error: " . $e->getMessage()
    );

    $message = "A database error occurred while loading recommendations.";

} catch (Throwable $e) {

    error_log(
        "Recommendation system error: " . $e->getMessage()
    );

    $message = "Something went wrong while generating recommendations.";
}

// Load common header
require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <!-- Page Heading -->
    <div class="mb-4">

        <h2>AI-Based Job Recommendations</h2>

        <p class="text-muted">
            Explore job opportunities recommended according
            to your skills using Artificial Intelligence.
        </p>

    </div>

    <!-- Student Skills -->
    <?php if (!empty($student_skills)): ?>

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <h5 class="mb-3">Your Skills</h5>

                <?php foreach ($student_skills as $skill): ?>

                    <span class="badge bg-primary me-2 mb-2">
                        <?= e($skill) ?>
                    </span>

                <?php endforeach; ?>

            </div>

        </div>

    <?php endif; ?>

    <!-- Information Message -->
    <?php if (!empty($message)): ?>

        <div class="alert alert-info" role="alert">
            <?= e($message) ?>
        </div>

    <?php endif; ?>

    <!-- Recommended Jobs -->
    <?php if (!empty($recommendations)): ?>

        <h4 class="mb-3">Recommended Jobs</h4>

        <div class="row">

            <?php foreach ($recommendations as $job): ?>

                <?php
                $similarity = (float) (
                    $job["similarity"] ?? 0
                );

                $similarity = max(
                    0,
                    min(100, $similarity)
                );

                $job_title = $job["job_title"] ??
                    "Job Opportunity";
                ?>

                <div class="col-md-6 mb-4">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <h5 class="card-title">
                                <?= e($job_title) ?>
                            </h5>

                            <p class="text-muted mb-2">
                                AI Skill Match
                            </p>

                            <h4 class="text-success">
                                <?= number_format($similarity, 2) ?>%
                            </h4>

                            <!-- Match Progress Bar -->
                            <div
                                class="progress mb-3"
                                style="height: 10px;"
                                role="progressbar"
                                aria-label="AI skill match"
                                aria-valuenow="<?= $similarity ?>"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            >

                                <div
                                    class="progress-bar bg-success"
                                    style="width: <?= $similarity ?>%;"
                                ></div>

                            </div>

                            <p class="small text-muted mb-0">
                                This job is recommended based
                                on your listed skills.
                            </p>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<?php
require_once __DIR__ . "/../includes/footer.php";
?>
