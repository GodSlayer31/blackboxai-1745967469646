<?php
session_start();
require 'config.php';

$message = '';

if (isset($_GET['token']) && isset($_SESSION['verification_token']) && $_GET['token'] === $_SESSION['verification_token']) {
    if (isset($_SESSION['pending_user'])) {
        $user = $_SESSION['pending_user'];

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash, id_number, role, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user['username'], $user['password_hash'], $user['id_number'], $user['role'], $user['email']);

        if ($stmt->execute()) {
            // Clear session verification data
            unset($_SESSION['verification_token']);
            unset($_SESSION['pending_user']);

            $message = "Your email has been verified successfully. You can now <a href='login.php'>login</a>.";
        } else {
            $message = "Error verifying your account. Please try again.";
        }
    } else {
        $message = "No pending user found for verification.";
    }
} else {
    $message = "Invalid or expired verification token.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Email Verification - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md text-center">
        <h1 class="text-2xl font-bold mb-6">Email Verification</h1>
        <p class="text-lg"><?= htmlspecialchars($message) ?></p>
    </div>
</body>
</html>
