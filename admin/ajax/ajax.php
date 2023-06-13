<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../../lib/Database.php');
include_once ($filepath . '/../../lib/Session.php');
include_once ($filepath . '/../../helper/Format.php');


spl_autoload_register(function($class_name) {
    include_once "../../classes/" . $class_name . ".php";
});

$database = new Database();
$format = new Format();
$common = new Common();

// phpmailer start
include_once ($filepath . "/../../PHPMailer/PHPMailer.php");
include_once ($filepath . "/../../PHPMailer/SMTP.php");
include_once ($filepath . "/../../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// phpmailer end

require_once '../../vendor/autoload.php';

if(isset($_POST['LetterApplicationCheck']) && ($_POST['LetterApplicationCheck'] == 'LetterApplicationCheck')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    if (isset($LetterApplication)) {
        echo 'Insert';
    }
    else {
        echo 'Field Fill';
    }
}

if(isset($_POST['EmailSendCheck']) && ($_POST['EmailSendCheck'] == 'EmailSendCheck')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    $Title = $format->validation($_POST['Title']);
    $Date = $format->validation($_POST['Date']);
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $UserId = 0;
            $all_letterapplication = $common->selectcolumn("COUNT(id) AS id","`users`","gmail = '".$email."'");
            $usercheckid = mysqli_fetch_assoc($all_letterapplication);
            if($usercheckid['id'] >= 1) {
                $userdetails = $common->select("`users`","gmail = '".$email."'");
                $userdetailsId = mysqli_fetch_assoc($userdetails);
                $UserId = $userdetailsId['id'];
            }
            else {
                $UserId = 0;
            }
            $AdminId = Session::get('admin_id');
            $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$Date','$Title','$LetterApplication','$UserId','$AdminId')");
            if ($LetterApplication_insert) {  
                $mail = new PHPMailer(); 
                $mail->isSMTP();
                $mail->Host = "smtp.ionos.es";
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;
                $mail->Username = "verify@mejorcadadia.com";
                $mail->Password = "hQjg-D?x9Pr+Knvb@rexU)4J%9E?fVD,dzK";
                $mail->Subject = $Title;
                $mail->setFrom($email);
                $mail->addReplyTo('verify@mejorcadadia.com');
                $mail->isHTML(true);
                $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png');
                $mail->Body = '
                    <html>
                        <head>
                            <title>'.$Title.'</title>
                        </head>
                        <body>
                            <h1 style="font-size: 20px; text-align: left; font-weight: 600; font-family: sans-serif;">'.$Date.'</h1>
                            <p><center><img style="width: 10%;" src="cid:logoimg" /></center></p>
                            <h1 style="font-size: 40px; text-align: center; font-weight: 600; font-family: sans-serif;">'.$Title.'</h1>
                            <h1 style="font-size: 40px; text-align: left; font-weight: 600; font-family: sans-serif;">Message :</h1>
                            '.html_entity_decode($LetterApplication).'
                        </body>
                    </html>
                ';
                $mail->AltBody = "This is the plain text version of the email content";
                $mail->addAddress($emailto);
                if($mail->send()) {
                    echo 'Insert';
                } else {
                    echo 'Failed to send mail!';
                }
                $mail->smtpClose();
            } else {
                echo 'Something is wrong!';
            }
        } else {
            echo 'Something is wrong!';
        }
    }
    else {
        echo 'Field Fill';
    }
}

if(isset($_POST['EmailSendCheckOnlySend']) && ($_POST['EmailSendCheckOnlySend'] == 'EmailSendCheckOnlySend')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    $Title = $format->validation($_POST['Title']);
    $Date = $format->validation($_POST['Date']);
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $UserId = 0;
            $all_letterapplication = $common->selectcolumn("COUNT(id) AS id","`users`","gmail = '".$email."'");
            $usercheckid = mysqli_fetch_assoc($all_letterapplication);
            if($usercheckid['id'] >= 1) {
                $userdetails = $common->select("`users`","gmail = '".$email."'");
                $userdetailsId = mysqli_fetch_assoc($userdetails);
                $UserId = $userdetailsId['id'];
            }
            else {
                $UserId = 0;
            }
            $AdminId = Session::get('admin_id');
            $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$Date','$Title','$LetterApplication','$UserId','$AdminId')");
            if ($LetterApplication_insert) {  
                echo 'Insert';
            } else {
                echo 'Something is wrong!';
            }
        } else {
            echo 'Something is wrong!';
        }
    }
    else {
        echo 'Field Fill';
    }
}

if(isset($_POST['EmailIdCheck']) && ($_POST['EmailIdCheck'] == 'EmailIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("`letterapplication`", "`letterapplicationtext` = '$LetterApplication'", "`id` = $id");
        echo 'Update';
    }
    else {
        echo 'Something is wrong!';
    }
}

if(isset($_POST['SaveIdCheck']) && ($_POST['SaveIdCheck'] == 'SaveIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("`letterapplication`", "`letterapplicationtext` = '$LetterApplication'", "`id` = $id");
        echo 'Update';
    }
    else {
        echo 'Something is wrong!';
    }
}

if(isset($_POST['EmailDeleteCheck']) && ($_POST['EmailDeleteCheck'] == 'EmailDeleteCheck')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("`letterapplication`", "`id` = '$id'");
        echo 'Delete';
    }
    else {
        echo 'Something is wrong!';
    }
}

?>