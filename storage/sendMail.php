<?php

namespace Storage;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//Load Composer's autoloader
require 'vendor/autoload.php';
//Create an instance; passing `true` enables exceptions
class SendMail
{
    private $mail_host = MAIL_HOST;
    private $mail_user = MAIL_USER;
    private $mail_password = MAIL_PASSWORD;
    private $mail_port = MAIL_PORT;
    private $mail_debug = MAIL_DEBUG;
    private $phpMailer;
    public function __construct()
    {
        $this->phpMailer = new PHPMailer();
        $this->phpMailer->SMTPDebug = $this->mail_debug;
        $this->phpMailer->SMTPAuth   = true;
        $this->phpMailer->SMTPSecure = 'tls';
    }
    public function sendMail($toMail, $subject, $body)
    {
        try {
            //Server settings
            $this->phpMailer->isSMTP();
            $this->phpMailer->Host       = $this->mail_host;                    //Set the SMTP server to send through
            $this->phpMailer->Username   = $this->mail_user;                     //SMTP username
            $this->phpMailer->Password   = $this->mail_password;                               //SMTP password
            $this->phpMailer->Port       = $this->mail_port;                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $this->phpMailer->setFrom($this->mail_user, 'Admin from Fleety.space');
            $this->phpMailer->addAddress($toMail);
            //Content
            $this->phpMailer->isHTML(true);
            $this->phpMailer->Subject = $subject;
            $this->phpMailer->Body    = $body;
            $this->phpMailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendContactMail($name, $email, $subject, $message)
    {
        $body = '<h1>Contact form</h1>';
        $body .= '<p>Name: ' . $name . '</p>';
        $body .= '<p>Email: ' . $email . '</p>';
        $body .= '<p>Subject: ' . $subject . '</p>';
        $body .= '<p>Message: ' . $message . '</p>';

        $this->phpMailer->isHTML(true);
        $this->phpMailer->setFrom($email, $name);
        $this->phpMailer->addAddress($this->mail_user);
        $this->phpMailer->Subject = $subject;
        $this->phpMailer->Body    = $body;
        if ($this->phpMailer->send()) {
            return true;
        } else {
            return false;
        }
    }
}
