
<?php

echo "<h2>PHP cURL Connection Test</h2>";

if (!function_exists('curl_init')) {
    die("ERROR: PHP cURL extension is not enabled.");
}

$ch = curl_init("http://127.0.0.1:5000/recommend-from-database");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "student_id" => 1
    ]),
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 20
]);

$response = curl_exec($ch);

$error = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "<p>HTTP Status: " . $status . "</p>";

if ($response === false) {
    echo "<p>cURL Error: " . htmlspecialchars($error) . "</p>";
} else {
    echo "<h3>Flask Response:</h3>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

unset($ch);

?>
