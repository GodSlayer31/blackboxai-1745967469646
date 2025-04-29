<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_number = trim($_POST['id_number']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if ID exists in valid_ids
    $stmt = $conn->prepare("SELECT role FROM valid_ids WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $role = $row['role'];

        // Check if username or email already exists
        $stmt2 = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt2->bind_param("ss", $username, $email);
        $stmt2->execute();
        $result2 = $stmt2->get_result();

        if ($result2->num_rows === 0) {
            if ($password === $confirm_password) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt3 = $conn->prepare("INSERT INTO users (username, password_hash, id_number, role, email) VALUES (?, ?, ?, ?, ?)");
                $stmt3->bind_param("sssss", $username, $password_hash, $id_number, $role, $email);

if ($stmt3->execute()) {
    // Generate a verification token
    $verification_token = bin2hex(random_bytes(16));

    // Store the token in session or database (for simplicity, session here)
    $_SESSION['verification_token'] = $verification_token;
    $_SESSION['pending_user'] = [
        'username' => $username,
        'role' => $role,
        'email' => $email,
        'id_number' => $id_number,
        'password_hash' => $password_hash
    ];

    // Send verification email with token link
    $verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?token=" . $verification_token;
    $subject = "Verify Your Email Address";
    $body = "Hello $username,<br>Please click the following link to verify your email address:<br><a href='$verification_link'>$verification_link</a>";
    sendEmail($email, $subject, $body);

    // Redirect to a page informing user to check email
    header("Location: verify_notice.php");
    exit();
} else {
    $message = "Error during registration.";
}
            } else {
                $message = "Passwords do not match.";
            }
        } else {
            $message = "Username or email already exists.";
        }
    } else {
        $message = "Invalid ID number.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>
        <?php if ($message): ?>
            <div class="bg-red-200 text-red-800 p-3 rounded mb-4"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php" class="space-y-4">
            <div>
                <label for="id_number" class="block mb-1 font-semibold">Student/Teacher ID Number</label>
                <input type="text" id="id_number" name="id_number" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="username" class="block mb-1 font-semibold">Username</label>
                <input type="text" id="username" name="username" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="email" class="block mb-1 font-semibold">Email</label>
                <input type="email" id="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="password" class="block mb-1 font-semibold">Password</label>
                <input type="password" id="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <div>
                <label for="confirm_password" class="block mb-1 font-semibold">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="w-full border border-gray-300 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Register</button>
        </form>
        <p class="mt-4 text-center text-sm">
            Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login here</a>.
        </p>
    </div>
</body>
</html>
