
<?php
session_start();

require_once "../config/database.php";

// Admin access protection
if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch all registered students
$sql = "
    SELECT
        users.user_id,
        users.full_name,
        users.email,
        students.student_id,
        students.phone,
        students.course,
        students.specialization,
        students.graduation_year,
        students.cgpa
    FROM users
    INNER JOIN students
        ON users.user_id = students.user_id
    WHERE users.role = 'student'
    ORDER BY users.user_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Students - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-2">Manage Students</h2>

    <p class="text-muted">
        View all students registered on CareerConnect.
    </p>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">
                Registered Students (<?php echo count($students); ?>)
            </h5>

            <?php if (count($students) > 0): ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">

                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Course</th>
                                <th>Specialization</th>
                                <th>Graduation Year</th>
                                <th>CGPA</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($students as $student): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars((string)$student["student_id"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($student["full_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($student["email"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($student["phone"] ?? "Not provided"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($student["course"] ?? "Not provided"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($student["specialization"] ?? "Not provided"); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars((string)($student["graduation_year"] ?? "Not provided")); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars((string)($student["cgpa"] ?? "Not provided")); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="alert alert-warning">
                    No students registered yet.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
