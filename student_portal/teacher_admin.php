<?php
session_start();
require 'config.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['grades_file'])) {
    $file = $_FILES['grades_file'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $file['tmp_name'];
        $content = file_get_contents($tmp_name);

        // For simplicity, assume each line is a grade entry: student_id,grade_data
        $lines = explode("\n", $content);
        $stmt = $conn->prepare("INSERT INTO grades (student_id, grade_data) VALUES (?, ?)");

        $conn->begin_transaction();
        try {
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                $parts = explode(',', $line, 2);
                if (count($parts) < 2) continue;
                $student_id = trim($parts[0]);
                $grade_data = trim($parts[1]);
                $stmt->bind_param("ss", $student_id, $grade_data);
                $stmt->execute();
            }
            $conn->commit();
            $message = "Grades uploaded successfully.";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error uploading grades: " . $e->getMessage();
        }
    } else {
        $message = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Teacher Admin - Student Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-3xl font-bold mb-6">Teacher Admin Panel</h1>
        <?php if ($message): ?>
            <div class="bg-green-200 text-green-800 p-3 rounded mb-4"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="POST" action="teacher_admin.php" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label for="grades_file" class="block mb-1 font-semibold">Upload Grades Raw Data File (CSV format: student_id,grade_data)</label>
                <input type="file" id="grades_file" name="grades_file" accept=".csv" required class="border border-gray-300 rounded px-3 py-2 w-full" />
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">Upload Grades</button>
        </form>
        <a href="logout.php" class="inline-block mt-6 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Logout</a>
    </div>
</body>
</html>
