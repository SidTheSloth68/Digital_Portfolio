<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


$name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile_number'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

$mail = new PHPMailer(true);

try {
    // Enable debug mode for troubleshooting
    $mail->SMTPDebug = 0; // Change to 2 for debugging
    $mail->isSMTP();
    
    // Try multiple SMTP configurations
    $mail->Host       = 'smtp.gmail.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sayeedbinomar27@gmail.com'; 
    $mail->Password   = 'qddnewbhomrnrheo';    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Add timeout and connection options
    $mail->Timeout = 60;
    $mail->SMTPKeepAlive = true;
    
    // Use your verified email as sender to avoid issues
    $mail->setFrom('sayeedbinomar27@gmail.com', 'Portfolio Contact Form');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('sayeedbinomar27@gmail.com', 'Sayeed Bin Omar'); 

  
    $mail->isHTML(true);
    $mail->Subject = $subject ?: 'Contact Form Submission';
    $mail->Body    = "
        <b>Name:</b> $name<br>
        <b>Email:</b> $email<br>
        <b>Mobile:</b> $mobile<br>
        <b>Message:</b><br>" . nl2br(htmlspecialchars($message));

    $mail->send();
    echo "<script>alert('Message sent successfully!');window.location.href='index.php';</script>";
} catch (Exception $e) {
    // Try alternative SMTP settings if first attempt fails
    try {
        $mail->clearAddresses();
        $mail->clearReplyTos();
        
        // Try SSL/465 port as alternative
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        $mail->setFrom('sayeedbinomar27@gmail.com', 'Portfolio Contact Form');
        $mail->addReplyTo($email, $name);
        $mail->addAddress('sayeedbinomar27@gmail.com', 'Sayeed Bin Omar');
        
        $mail->send();
        echo "<script>alert('Message sent successfully!');window.location.href='index.php';</script>";
    } catch (Exception $e2) {
        // If SMTP fails completely, log the error and use mail() function as fallback
        error_log("SMTP Error: " . $e->getMessage());
        
        // Fallback to PHP's built-in mail() function
        $to = 'sayeedbinomar27@gmail.com';
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $email_subject = $subject ?: 'Contact Form Submission';
        $email_body = "<b>Name:</b> $name<br><b>Email:</b> $email<br><b>Mobile:</b> $mobile<br><b>Message:</b><br>" . nl2br(htmlspecialchars($message));
        
        if (mail($to, $email_subject, $email_body, $headers)) {
            echo "<script>alert('Message sent successfully!');window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Message could not be sent. Please check your network connection or try again later.');window.location.href='index.php';</script>";
        }
    }
}
?>