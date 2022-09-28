<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../lib/Session.php');
include_once ($filepath . '/../classes/Common.php');
$common = new Common();

Session::adminLogin();

// phpmailer start
include_once ($filepath . "/../PHPMailer/PHPMailer.php");
include_once ($filepath . "/../PHPMailer/SMTP.php");
include_once ($filepath . "/../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php';
// phpmailer end

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {
	$email = $_POST['email'];
  	$password = $_POST['password'];
  	
  	$error = 0;
  	if(empty($email)) {
      $email_err = '<div class="alert alert-danger">Email is required!</div>';
      $error++;
    }
  	if(empty($password)) {
      $password_err = '<div class="alert alert-danger">Password is required!</div>';
      $error++;
    }
  	if($error == 0) {
    	$admin_info = $common->select("`admin`", "`email` = '$email' && `password` = '$password'");
      	if($admin_info) {
          $admin_infos = mysqli_fetch_assoc($admin_info);
          Session::set('admin_login', true);
          Session::set('admin_id', $admin_infos['id']);
          header("Location: https://mejorcadadia.com/admin");
        } else {
        	$error_msg = '<div class="alert alert-danger">Email or password is wrong!</div>';
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_forgot_password'])) {
	$forgot_email = $_POST['forgot_email'];
  	
  	$forgot_error = 0;
  	if(empty($forgot_email)) {
      $forgot_email_err = '<div class="alert alert-danger">Email is required!</div>';
      $forgot_error++;
    }
  
  	if($forgot_error == 0) {
    	$forgot_info = $common->select("`admin`", "`email` = '$forgot_email'");
      	if($forgot_info) {
          $forgot_infos = mysqli_fetch_assoc($forgot_info);
          $forgot_email = $forgot_infos['email'];
          $forgot_password = $forgot_infos['password'];
          //---------------Email sender---------------
          $mail_body = "Hola, bienvenido a MejorCadaDÃa, <br>
                            Email: " . $forgot_email . " <br>
                            Password: " . $forgot_password . " <br><br>

                            Estamos creando la comunidad lÃƒder de empoderamiento personal, mÃƒÂ¡s grande del Planeta. <br>
                            Estamos viviendo momentos desafiantes y ahora mÃƒÂ¡s que nunca tenemos que trabajar en nosotros mismos. El mundo necesita nuestra Mejor versiÃƒÂ³n y no una descafeinada. <br>
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

          $mail->addAddress($forgot_email); //enter receiver email address

          if($mail->send()) {
            $forgot_error_msg = '<div class="alert alert-success">Please check your email!</div>';
          } else {
            $forgot_error_msg = '<div class="alert alert-danger">Failed to send mail!</div>';
          }
          $mail->smtpClose();

          //-------------Email Sender Ends------------
        } else {
        	$forgot_error_msg = '<div class="alert alert-danger">Account not found!</div>';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mejorcadadia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
	<section class="admin-login">
      <div class="wrapper">
        <div class="container">
          <div class="col-left" style="opacity: 0;">
            <div class="login-text">
              <h2>Welcome Back</h2>
            </div>
          </div>
          <div class="col-right">
            <div class="login-form" style="display: <?= isset($forgot_error_msg) ? 'none' : 'block'; ?>;">
              <h2>Login</h2>
              <form action="" method="POST">
                <p>
                  <label style="margin-bottom: 10px;">Email address<span>*</span></label>
                  <input type="email" placeholder="Email" name="email" value="<?= isset($email) ? $email : ''; ?>" required>
                  <?= isset($email_err) ? $email_err : ''; ?>
                </p>
                <p>
                  <label style="margin-bottom: 10px;">Password<span>*</span></label>
                  <input type="password" placeholder="Password" name="password" required>
                  <?= isset($password_err) ? $password_err : ''; ?>
                  <?= isset($error_msg) ? $error_msg : ''; ?>
                </p>
                <p>
                  <input type="submit" name="admin_login" value="Sing In" />
                </p>
                <p>
                  <a href="javascript:void(0);" id="forgot_panel">Forget Password?</a>
                </p>
              </form>
            </div>
            <div class="forgot-form" style="display: <?= isset($forgot_error_msg) ? 'block' : 'none'; ?>;">
              <h2>Forgot password</h2>
              <form action="" method="POST">
                <p>
                  <label style="margin-bottom: 10px;">Email address<span>*</span></label>
                  <input type="email" placeholder="Email" name="forgot_email" value="<?= isset($forgot_email) ? $forgot_email : ''; ?>" required>
                  <?= isset($forgot_error_msg) ? $forgot_error_msg : ''; ?>
                </p>
                <p>
                  <input type="submit" name="admin_forgot_password" value="Send" />
                </p>
                <p>
                  <a href="javascript:void(0);" id="login_panel">Login</a>
                </p>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
 	<script>
    	$(document).ready(function(){
          $('#forgot_panel').click(function() {
              $('.login-form').hide();
              $('.forgot-form').show();
          });
          $('#login_panel').click(function() {
              $('.forgot-form').hide();
              $('.login-form').show();
          });
        });
  	</script>
</body>
</html>