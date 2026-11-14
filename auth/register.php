
<?php
require_once "../config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    }
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    }
    elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    }
    else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $conn->beginTransaction();

            $sql = "INSERT INTO users (full_name, email, password, role)
                    VALUES (?, ?, ?, 'student')";

            $stmt = $conn->prepare($sql);
            $stmt->execute([
                $full_name,
                $email,
                $hashed_password
            ]);

            $user_id = $conn->lastInsertId();

            $profile = $conn->prepare(
                "INSERT INTO students (user_id) VALUES (?)"
            );
            $profile->execute([$user_id]);

            $conn->commit();

            $message = "Registration successful! You can now log in.";

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
    <title>Student Registration - CareerConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow p-4">
                <h2 class="text-center mb-4">Create Your Account</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name"
                               class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
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

                    <button type="submit"
                            class="btn btn-primary w-100">
                        Register
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
