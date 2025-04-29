<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$id_number = $_SESSION['id_number'];

// Fetch grades for the student
$stmt = $conn->prepare("SELECT grade_data FROM grades WHERE student_id = ?");
$stmt->bind_param("s", $id_number);
$stmt->execute();
$result = $stmt->get_result();

$grades = [];
while ($row = $result->fetch_assoc()) {
    $grades[] = $row['grade_data'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Student Dashboard - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-6">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
        <h2 class="text-xl font-semibold mb-4">Your Grades Raw Data</h2>
        <?php if (count($grades) > 0): ?>
            <ul class="list-disc list-inside space-y-2">
                <?php foreach ($grades as $grade): ?>
                    <li class="bg-gray-100 p-3 rounded"><?= htmlspecialchars($grade) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No grades data available.</p>
        <?php endif; ?>
        <a href="logout.php" class="inline-block mt-6 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Logout</a>
    </div>
</body>
</html>
