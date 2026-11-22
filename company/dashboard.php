
<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Check company login
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "company") {
    header("Location: login.php");
    exit();
}

// Get company details
$sql = "SELECT * FROM companies WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$_SESSION["user_id"]]);

$company = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$company) {
    exit("Company profile not found.");
}

// Count jobs posted by the company
$sql = "SELECT COUNT(*) FROM jobs WHERE company_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$company["company_id"]]);

$total_jobs = $stmt->fetchColumn();

$pageTitle = "Company Dashboard | CareerConnect";

require_once __DIR__ . "/../includes/header.php";
?>

<div class="container py-5">

    <div class="mb-4">
        <h2>Company Dashboard</h2>

        <h5>
            Welcome,
            <?= htmlspecialchars($company["company_name"], ENT_QUOTES, "UTF-8"); ?>!
        </h5>

        <p class="text-muted">
            Manage your recruitment activities through CareerConnect.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <h5>Total Jobs Posted</h5>
                <h2 class="text-primary">
                    <?= (int) $total_jobs; ?>
                </h2>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <h5>Recruitment Portal</h5>
                <p class="text-muted mb-0">
                    Create and manage job opportunities for students.
                </p>
            </div>
        </div>

    </div>

    <div class="card shadow-sm border-0 p-4 mt-4">

        <h4>Company Activities</h4>

        <div class="list-group mt-3">

            <a href="post_job.php" class="list-group-item list-group-item-action">
                Post a New Job
            </a>

            <a href="manage_jobs.php" class="list-group-item list-group-item-action">
                Manage Posted Jobs
            </a>

            <a href="applicants.php" class="list-group-item list-group-item-action">
                View Student Applications
            </a>

        </div>

        <div class="mt-4">
            <a href="../auth/logout.php" class="btn btn-danger">
                Logout
            </a>
        </div>

    </div>

</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>