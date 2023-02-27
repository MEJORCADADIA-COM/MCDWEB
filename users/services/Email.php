<?php

namespace Users\services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once base_path("/PHPMailer/PHPMailer.php");
require_once base_path("/PHPMailer/SMTP.php");
require_once base_path("/PHPMailer/Exception.php");

class Email
{
    public function __construct(public readonly string $from = 'verify@mejorcadadia.com', public readonly string $password = 'hQjg-D?x9Pr+Knvb@rexU)4J%9E?fVD,dzK')
    {
    }

    /**
     * @throws Exception
     */
    public function send($subject, $to, $body): bool
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.ionos.es";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = $this->from;
        $mail->Password = $this->password;
        $mail->Subject = $subject;
        $mail->setFrom($this->from);
        $mail->addReplyTo($this->from);
        $mail->addReplyTo($to);
        $mail->isHTML(true);
        $mail->Body = "<html>
            <head>
                <title>{$subject}</title>
            </head>
            <body>
                <div style='background-color:#f3f2f0;'>                        
                    {$body}
                </div>
            </body>
            <footer style='background-color: #fef200; padding:20px;'>
                <p style='clear:both; margin:0; padding:0; text-align:center;'>Mejorcadadia.com</p>
                <p style='clear:both; margin:0; padding:0; text-align:center;'>All rights reserved 2022</p>
                <div style='clear:both; padding:0; margin:0;'></div>
            </footer>
        </html>";
        $mail->AltBody = "This is the plain text version of the email content";
        $mail->addAddress($to);
        $sent = $mail->send();
        $mail->smtpClose();

        return $sent;
    }
}