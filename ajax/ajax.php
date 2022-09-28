<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../lib/Database.php');
include_once ($filepath . '/../lib/Session.php');
include_once ($filepath . '/../helper/Format.php');

spl_autoload_register(function($class_name) {
    include_once "../classes/" . $class_name . ".php";
});

$database = new Database();
$format = new Format();
$common = new Common();

// phpmailer start
include_once ($filepath . "/../PHPMailer/PHPMailer.php");
include_once ($filepath . "/../PHPMailer/SMTP.php");
include_once ($filepath . "/../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// phpmailer end

require_once '../vendor/autoload.php';

// registration and login part
  
if(isset($_POST['email_registration']) && ($_POST['email_registration'] == 'email_registration')) {
	$gmail = $format->validation($_POST['email']);
    $email_check = $common->select("`users`", "`gmail` = '$gmail'");
    if ($email_check) {
      echo 'You have already account with this email!';
    } else {
      $code_rand = rand(100000000, 999999999);
      $code_newhashpass = substr($code_rand, 0, 6);
      Session::set($gmail, $code_newhashpass);
      //---------------Email sender---------------
      $mail_body = "Hola, bienvenido a MejorCadaDía, <br>
                        Tu Codigo de Verificación: <br>
                        Code: ".$code_newhashpass." <br><br>
                        
                        Estamos creando la comunidad lÃder de empoderamiento personal, mÃ¡s grande del Planeta. <br>
                        Estamos viviendo momentos desafiantes y ahora mÃ¡s que nunca tenemos que trabajar en nosotros mismos. El mundo necesita nuestra Mejor versiÃ³n y no una descafeinada. <br>
                        Te invitamos a que participes en la medida que desees. <br>
                        Bienvenido, <br>
                        Miguel De La Fuente <br>";

      $mail = new PHPMailer();
      //SMTP Settings
      $mail->isSMTP();
      $mail->Host = "smtp.ionos.es";
      $mail->SMTPAuth = true;
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;
      $mail->Username = "verify@mejorcadadia.com"; //enter you email address
      $mail->Password = "Ta$77!/8H7u/SX?"; //enter you email password
      $mail->Subject = "Bienvenido a MejorCadaDÃa.com";
      $mail->setFrom('verify@mejorcadadia.com');
      $mail->addReplyTo('verify@mejorcadadia.com');
      $mail->isHTML(true);

      $mail->Body = $mail_body;   

      $mail->addAddress($gmail); //enter receiver email address

      if($mail->send()) {
        echo 'sent';
      } else {
        echo 'Failed to send mail!';
      }
      $mail->smtpClose();

      //-------------Email Sender Ends------------
    }
}
  
if(isset($_POST['type']) && ($_POST['type'] == 'google' || $_POST['type'] == 'facebook' || $_POST['type'] == 'email')) {
    $type = $format->validation($_POST['type']);
    if (!empty($type)) {
        if ($type == 'google') {
            $CLIENT_ID = "887364289703-4jju24o28dgn7mh41s3o97i5emucjupp.apps.googleusercontent.com";
            $id_token = $_POST['credential'];
            $client = new Google_Client(['client_id' => $CLIENT_ID]);
            $payload = $client->verifyIdToken($id_token);
            
            $full_name = $format->validation($payload['name']);
            $gmail = $format->validation($payload['email']);
            $image = $format->validation($payload['picture']);
            
            $google_check = $common->select("`users`", "`gmail` = '$gmail'");
            if ($google_check) {
                $google_checks = mysqli_fetch_assoc($google_check);
                $type_check = $google_checks['type'];
                if($google_checks['status'] == '1') {
                    if ($type_check == 'google') {
                      	Session::set('login', true);
                        Session::set('user_id', $google_checks['id']);
                        echo 'logged_in';
                    } else {
                        echo 'You have another account with this email!';
                    }
                } else {
                    echo 'Your account has been blocked!';
                }
            } else {
                $google_insert = $common->insert("`users`(`full_name`, `type`, `gmail`, `image`)", "('$full_name', '$type', '$gmail', '$image')");
                if ($google_insert) {
                    $user_info = $common->select("`users`", "`gmail` = '$gmail'");
                    $user_infos = mysqli_fetch_assoc($user_info);
                  	Session::set('login', true);
                    Session::set('user_id', $user_infos['id']);
                    echo 'logged_in';
                }
            }
        } elseif ($type == 'facebook') {
            $full_name = $format->validation($_POST['full_name']);
            $gmail = $format->validation($_POST['gmail']);
            $facebook_id = $format->validation($_POST['facebook_id']);
            $image = $format->validation($_POST['image']);

            if (!empty($gmail)) {
                $facebook_check = $common->select("`users`", "`gmail` = '$gmail' || `facebook_id` = '$facebook_id'");
            } else {
                $facebook_check = $common->select("`users`", "`facebook_id` = '$facebook_id'");
                $gmail = NULL;
            }
            if ($facebook_check) {
                $facebook_checks = mysqli_fetch_assoc($facebook_check);
                $type_check = $facebook_checks['type'];
                if($facebook_checks['status'] == '1') {
                    if ($type_check == 'facebook') {
                      	Session::set('login', true);
                    	Session::set('user_id', $facebook_checks['id']);
                        echo 'logged_in';
                    } else {
                        echo 'You have another account with this email!';
                    }
                } else {
                    echo 'Your account has been blocked!';
                }
            } else {
                $facebook_insert = $common->insert("`users`(`full_name`, `type`, `gmail`, `facebook_id`, `image`)", "('$full_name', '$type', '$gmail', '$facebook_id', '$image')");
                if ($facebook_insert) {
                    $user_info = $common->select("`users`", "`facebook_id` = '$facebook_id'");
                    $user_infos = mysqli_fetch_assoc($user_info);
                    Session::set('login', true);
                    Session::set('user_id', $user_infos['id']);
                    echo 'logged_in';
                }
            }
        } elseif ($type == 'email') {
            $gmail = $format->validation($_POST['gmail']);
            $password = $format->validation($_POST['password']);

            $email_check = $common->select("`users`", "`gmail` = '$gmail'");
            if ($email_check) {
                $email_checks = mysqli_fetch_assoc($email_check);
                if($email_checks['type'] == 'email') {
                    if($email_checks['password'] == $password) {
                        if($email_checks['status'] == '1') {
                            Session::set('login', true);
                    		Session::set('user_id', $email_checks['id']);
                            echo 'logged_in';
                        } else {
                            echo 'Your account has been blocked!';
                        }
                    } else {
                        echo 'Password does not match!';
                    }
                } else {
                    echo 'You have another account with this email!';
                }
            } else {
                echo 'No account found!';
            }
        }
    }
}


// email verification and login
if(isset($_POST['email_verification_login'])) {
    $age = $format->validation($_POST['age']);
    $gmail = $format->validation($_POST['email']);
    $password = $format->validation($_POST['password']);
    $code = $format->validation($_POST['code']);
    if (isset($_SESSION[$gmail])) {
      if ($code == Session::get($gmail)) {
        $full_name_exp = explode("@", $gmail);
        $full_name = $full_name_exp[0];
        $email_insert = $common->insert("`users`(`full_name`, `type`, `gmail`, `password`)", "('$full_name', 'email', '$gmail', '$password')");
        if ($email_insert) {
          $user_info = $common->select("`users`", "`gmail` = '$gmail'");
          $user_infos = mysqli_fetch_assoc($user_info);
          Session::set('login', true);
          Session::set('user_id', $user_infos['id']);
          Session::unset($gmail);
          echo 'logged_in';
        } else {
          echo 'Something is wrong!';
        }
      } else {
        echo 'Your code is wrong!';
      }
    } else {
      echo 'Please refresh your page and try again!';
    }
}


?>