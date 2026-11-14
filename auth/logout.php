
<?php
session_start();

$role = $_SESSION["role"] ?? "";

$_SESSION = [];

session_destroy();

if ($role === "company") {
    header("Location: ../company/login.php");
} else {
    header("Location: login.php");
}

exit();
?>
