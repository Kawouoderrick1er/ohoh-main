<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

    function envoiemail($mail, $mailtosend, $message){
    //Server settings
    $mail->SMTPDebug =0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'kawouoderrick@gmail.com';                     //SMTP username
    $mail->Password   = 'hkebszfedxvgynis';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('kawouoderrick@gmail.com', 'abonnez vous a DXT');
    $mail->addAddress($mailtosend, 'derrick User');
    
    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'formation en ligne';
    $mail->Body    = ' <b>'.$message.'</b>';
    $mail->AltBody = '';

    $mail->send();
    echo 'Message  a ete envoyer avec succé';
} 