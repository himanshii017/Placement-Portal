
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

// Fetch registered companies
$sql = "
    SELECT
        users.user_id,
        users.full_name,
        users.email,
        companies.company_id,
        companies.company_name
    FROM users
    INNER JOIN companies
        ON users.user_id = companies.user_id
    WHERE users.role = 'company'
    ORDER BY users.user_id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute();

$companies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Companies - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-2">Manage Companies</h2>

    <p class="text-muted">
        View all companies registered on CareerConnect.
    </p>

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Back to Dashboard
    </a>

    <div class="card shadow-sm">

        <div class="card-body">

            <h5 class="mb-3">
                Registered Companies (<?php echo count($companies); ?>)
            </h5>

            <?php if (count($companies) > 0): ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email Address</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($companies as $company): ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars((string)$company["company_id"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($company["company_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($company["full_name"]); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($company["email"]); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="alert alert-warning">
                    No companies registered yet.
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>
</html>
