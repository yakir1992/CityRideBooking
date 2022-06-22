<?php

require 'PHPMailer/PHPMailerAutoload.php';

define('GUSER', 'donotreply@bookcityride.co.il'); // username
define('GPWD', 'tal991149'); // password

function smtpmailer($to, $ccAddress, $from, $from_name, $subject, $body) {
    global $error;
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    // $mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only - צריך להוריד בפרודקשן
    $mail->SMTPAuth = true;  // authentication enabled
    // $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
    $mail->SMTPAutoTLS = false;
    $mail->Host = 'mail.bookcityride.co.il';
    $mail->Port = 25;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    $mail->isHTML(true);
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->CharSet = 'UTF-8';
    //$mail->AddEmbeddedImage("includes/logo.png", "includes/", "logo.png");
    $mail->AddAddress($to); // אפשר להעתיק שוב את השורה ולשלוח לכמה כתובות אפשר גם להוסיף פסיק ושם המקבל
    $mail->addCC($ccAddress);
    /*$mail->addReplyTo($address, $name);
    $mail->addBCC($address, $name);
    $mail->AltBody = $body; // טוב לעשות אלט באשי ללא הטמל למקרה שהקליינט לא יכול לקרוא */
    if (!$mail->Send()) {
        $error = 'Mail error: ' . $mail->ErrorInfo;
        return false;
    } else {
        $error = 'Message sent!';
        return true;
    }
}



function smtpmailer_contact_us($to, $from, $from_name, $subject, $body) {
    global $error;
    $mail = new PHPMailer();  // create a new object
    $mail->IsSMTP(); // enable SMTP
    // $mail->SMTPDebug = 1;  // debugging: 1 = errors and messages, 2 = messages only - צריך להוריד בפרודקשן
    $mail->SMTPAuth = true;  // authentication enabled
    // $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
    $mail->SMTPAutoTLS = false;
    $mail->Host = 'mail.bookcityride.co.il';
    $mail->Port = 25;
    $mail->Username = GUSER;
    $mail->Password = GPWD;
    $mail->isHTML(true);
    $mail->SetFrom($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->CharSet = 'UTF-8';
    $mail->AddAddress($to); // אפשר להעתיק שוב את השורה ולשלוח לכמה כתובות אפשר גם להוסיף פסיק ושם המקבל
    if (!$mail->Send()) {
        $error = 'Mail error: ' . $mail->ErrorInfo;
        return false;
    } else {
        $error = 'Message sent!';
        return true;
    }
}



