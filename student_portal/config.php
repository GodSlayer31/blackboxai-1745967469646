<?php
// Database configuration
$host = 'localhost';
$dbname = 'student_portal';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require_once 'PHPMailer/Exception.php';
require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'your@gmail.com'; // SMTP username
        $mail->Password = 'app password';         // SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('your@gmail.com', 'Student Portal');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>
