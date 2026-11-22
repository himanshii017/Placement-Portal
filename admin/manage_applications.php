<?php
session_start();
require_once "../config/database.php";

// Admin authentication
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit();
}

$message = "";
$messageType = "";

// Escape output
function clean($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

// Update application status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["application_id"], $_POST["status"])) {

    $application_id = filter_var($_POST["application_id"], FILTER_VALIDATE_INT);
    $status = $_POST["status"];

    $allowedStatuses = [
        "Applied",
        "Shortlisted",
        "Selected",
        "Rejected"
    ];

    if (!$application_id || !in_array($status, $allowedStatuses, true)) {
        $message = "Invalid application or status.";
        $messageType = "error";
    } else {
        try {
            $update = $conn->prepare("
                UPDATE applications
                SET status = ?
                WHERE application_id = ?
            ");

            $update->execute([$status, $application_id]);

            $message = "Application status updated successfully.";
            $messageType = "success";

        } catch (PDOException $e) {
            $message = "Unable to update application status.";
            $messageType = "error";
        }
    }
}

// Fetch all applications
try {
    $sql = "
        SELECT
            a.application_id,
            a.applied_at,
            a.status,
            u.full_name,
            u.email,
            j.job_title,
            c.company_name
        FROM applications a
        INNER JOIN students s
            ON a.student_id = s.student_id
        INNER JOIN users u
            ON s.user_id = u.user_id
        INNER JOIN jobs j
            ON a.job_id = j.job_id
        LEFT JOIN companies c
            ON j.company_id = c.company_id
        ORDER BY a.applied_at DESC
    ";

    $stmt = $conn->query($sql);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error loading applications. Please check your database tables.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Applications | CareerConnect</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px 20px;
            background: #f4f6fb;
            font-family: Arial, sans-serif;
            color: #263247;
        }

        .container {
            width: 100%;
            max-width: 1250px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
            color: #172b4d;
        }

        .subtitle {
            color: #667085;
            margin: 0;
        }

        .back-btn {
            display: inline-block;
            padding: 11px 18px;
            background: #243b64;
            color: white;
            text-decoration: none;
            border-radius: 7px;
        }

        .back-btn:hover {
            background: #172b4d;
        }

        .message {
            padding: 13px;
            margin-bottom: 20px;
            border-radius: 7px;
        }

        .success {
            background: #dff5e5;
            color: #176b35;
        }

        .error {
            background: #ffe3e3;
            color: #a32020;
        }

        .table-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 950px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #243b64;
            color: white;
        }

        tr:hover td {
            background: #f8faff;
        }

        .status-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        select {
            padding: 9px;
            border: 1px solid #ccd2dc;
            border-radius: 6px;
            background: white;
        }

        .update-btn {
            padding: 9px 12px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .update-btn:hover {
            background: #1d4ed8;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #667085;
        }

        @media(max-width: 600px) {
            body {
                padding: 20px 10px;
            }

            .table-card {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <div>
            <h1>Manage Applications</h1>
            <p class="subtitle">
                Review student applications and update their placement status.
            </p>
        </div>

        <a href="dashboard.php" class="back-btn">
            Back to Dashboard
        </a>
    </div>

    <?php if ($message !== ""): ?>
        <div class="message <?= clean($messageType) ?>">
            <?= clean($message) ?>
        </div>
    <?php endif; ?>

    <div class="table-card">

        <h2>Student Applications</h2>

        <div class="table-wrapper">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Applied Date</th>
                        <th>Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (count($applications) > 0): ?>

                    <?php foreach ($applications as $app): ?>

                        <tr>
                            <td><?= clean($app["application_id"]) ?></td>

                            <td><?= clean($app["full_name"]) ?></td>

                            <td><?= clean($app["email"]) ?></td>

                            <td><?= clean($app["job_title"]) ?></td>

                            <td>
                                <?= clean($app["company_name"] ?? "Not available") ?>
                            </td>

                            <td>
                                <?= clean(date(
                                    "d M Y",
                                    strtotime($app["applied_at"])
                                )) ?>
                            </td>

                            <td>
                                <?= clean($app["status"] ?? "Applied") ?>
                            </td>

                            <td>
                                <form method="POST" class="status-form">

                                    <input
                                        type="hidden"
                                        name="application_id"
                                        value="<?= clean($app["application_id"]) ?>"
                                    >

                                    <select name="status" required>
                                        <?php
                                        $statuses = [
                                            "Applied",
                                            "Shortlisted",
                                            "Selected",
                                            "Rejected"
                                        ];
                                        ?>

                                        <?php foreach ($statuses as $status): ?>
                                            <option
                                                value="<?= clean($status) ?>"
                                                <?= ($app["status"] === $status) ? "selected" : "" ?>
                                            >
                                                <?= clean($status) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <button type="submit" class="update-btn">
                                        Update
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="8" class="empty">
                            No applications found.
                        </td>
                    </tr>

                <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

</div>

</body>
</html>