
<?php
require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $company_name = trim($_POST["company_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($company_name) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    }
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }
    elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    }
    else {
        try {
            $conn->beginTransaction();

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (full_name, email, password, role)
                    VALUES (?, ?, ?, 'company')";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $company_name,
                $email,
                $hashed_password
            ]);

            $user_id = $conn->lastInsertId();

            $profile = $conn->prepare(
                "INSERT INTO companies (user_id, company_name)
                 VALUES (?, ?)"
            );

            $profile->execute([$user_id, $company_name]);

            $conn->commit();

            $message = "Company registered successfully!";

        } catch (PDOException $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            if ($e->getCode() == "23000") {
                $message = "This email is already registered.";
            } else {
                $message = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Company Registration - CareerConnect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow p-4">

                <h2 class="text-center mb-4">Company Registration</h2>

                <?php if ($message != ""): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Company Email</label>
                        <input type="email" name="email"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password"
                               class="form-control" minlength="8" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password"
                               class="form-control" minlength="8" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Register Company
                    </button>

                </form>

                <p class="text-center mt-3">
                    Already registered?
                    <a href="login.php">Login here</a>
                </p>

            </div>

        </div>
    </div>
</div>

</body>
</html>
