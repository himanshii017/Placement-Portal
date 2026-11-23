<?php
session_start();

require_once "../config/database.php";

// Check student login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Find student ID
$stmt = $conn->prepare(
    "SELECT student_id FROM students WHERE user_id = ?"
);
$stmt->execute([$user_id]);
$student = $stmt->fetch();

if (!$student) {
    die("Student profile not found.");
}

$student_id = $student["student_id"];
$message = "";

// Save selected skills
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selected_skills = $_POST["skills"] ?? [];
    $new_skill = trim($_POST["new_skill"] ?? "");

    try {
        $conn->beginTransaction();

        // Add a new skill if entered
        if ($new_skill !== "") {

            $stmt = $conn->prepare(
                "INSERT IGNORE INTO skills (skill_name) VALUES (?)"
            );
            $stmt->execute([$new_skill]);

            // Get skill ID
            $stmt = $conn->prepare(
                "SELECT skill_id FROM skills WHERE skill_name = ?"
            );
            $stmt->execute([$new_skill]);

            $new_skill_id = $stmt->fetchColumn();

            if ($new_skill_id) {
                $selected_skills[] = $new_skill_id;
            }
        }

        // Remove previous selections
        $stmt = $conn->prepare(
            "DELETE FROM student_skills WHERE student_id = ?"
        );
        $stmt->execute([$student_id]);

        // Save selected skills
        $insert = $conn->prepare(
            "INSERT INTO student_skills (student_id, skill_id)
             VALUES (?, ?)"
        );

        foreach (array_unique($selected_skills) as $skill_id) {
            $skill_id = filter_var($skill_id, FILTER_VALIDATE_INT);

            if ($skill_id) {
                $insert->execute([$student_id, $skill_id]);
            }
        }

        $conn->commit();
        $message = "Skills updated successfully!";

    } catch (Exception $e) {
        $conn->rollBack();
        $message = "Error updating skills.";
    }
}

// Get all available skills
$stmt = $conn->query(
    "SELECT * FROM skills ORDER BY skill_name"
);
$all_skills = $stmt->fetchAll();

// Get student's selected skills
$stmt = $conn->prepare(
    "SELECT skill_id FROM student_skills WHERE student_id = ?"
);
$stmt->execute([$student_id]);

$selected_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Skills</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f8;
            padding: 30px;
        }

        .container {
            background: white;
            max-width: 650px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
        }

        .skill {
            display: inline-block;
            background: #eef2ff;
            padding: 12px;
            margin: 8px;
            border-radius: 6px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="text"] {
            padding: 10px;
            width: 70%;
        }

        .message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Manage My Skills</h2>

    <p>Select your skills:</p>

    <?php if ($message): ?>
        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <?php foreach ($all_skills as $skill): ?>

            <label class="skill">
                <input type="checkbox"
                    name="skills[]"
                    value="<?php echo $skill["skill_id"]; ?>"
                    <?php echo in_array(
                        $skill["skill_id"],
                        $selected_ids
                    ) ? "checked" : ""; ?>>

                <?php echo htmlspecialchars($skill["skill_name"]); ?>
            </label>

        <?php endforeach; ?>

        <hr>

        <h3>Add a New Skill</h3>

        <input type="text"
               name="new_skill"
               placeholder="Enter a new skill">

        <br><br>

        <button type="submit">Save My Skills</button>

    </form>

    <br>

    <a href="dashboard.php">Back to Dashboard</a>

</div>

</body>
</html>