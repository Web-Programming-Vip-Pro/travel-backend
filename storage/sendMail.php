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
    private $phpMailer;
    public function __construct()
    {
        $this->phpMailer = new PHPMailer();
    }
    public function sendMail($toMail, $subject, $body)
    {
        try {
            //Server settings
            $this->phpMailer->SMTPDebug = SMTP::DEBUG_OFF;
            $this->phpMailer->isSMTP();
            $this->phpMailer->Host       = $this->mail_host;                    //Set the SMTP server to send through
            $this->phpMailer->SMTPAuth   = true;                                   //Enable SMTP authentication
            $this->phpMailer->Username   = $this->mail_user;                     //SMTP username
            $this->phpMailer->Password   = $this->mail_password;                               //SMTP password
            $this->phpMailer->SMTPSecure = 'tls';            //Enable implicit TLS encryption
            $this->phpMailer->Port       = $this->mail_port;                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //Recipients
            $this->phpMailer->setFrom($this->mail_user, 'Admin from Fleety.space');
            $this->phpMailer->addAddress($toMail);
            //Content
            $this->phpMailer->isHTML(true);                                  //Set email format to HTML
            $this->phpMailer->Subject = $subject;
            $this->phpMailer->Body    = $body;
            $this->phpMailer->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
