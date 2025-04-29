<?php
namespace PHPMailer\PHPMailer;

class PHPMailer
{
    public function isSMTP() {}
    public function Host($host = null) {}
    public function SMTPAuth($auth = null) {}
    public function Username($username = null) {}
    public function Password($password = null) {}
    public function SMTPSecure($secure = null) {}
    public function Port($port = null) {}
    public function setFrom($address, $name = '') {}
    public function addAddress($address, $name = '') {}
    public function isHTML($isHtml = true) {}
    public function Subject($subject = '') {}
    public function Body($body = '') {}
    public function send() { return true; }
}
?>
