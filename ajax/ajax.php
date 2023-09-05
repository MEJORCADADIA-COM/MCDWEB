<?php

$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../lib/Database.php');
include_once ($filepath . '/../lib/Session.php');
include_once ($filepath . '/../helper/Format.php');
include_once($filepath . '/../lib/RememberCookie.php');

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
 
    $email_check = $common->first("users", "gmail = :email", ['email' => $gmail]);
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
      $mail->Username = "miguel@mejorcadadia.com"; //enter you email address
      $mail->Password = "k8#pb27%Iae*ml!A!ZlxD3(C"; //enter you email password
      $mail->Subject = "Bienvenido a MejorCadaDÃa.com";
      $mail->setFrom('miguel@mejorcadadia.com');
      $mail->addReplyTo('miguel@mejorcadadia.com');
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

if(isset($_POST['type']) && ($_POST['type'] == 'google' || $_POST['type'] == 'facebook' || $_POST['type'] == 'email'|| $_POST['type'] == 'instagram')) {
    $type = $format->validation($_POST['type']);
    if (!empty($type)) {
        if ($type == 'google') {
            $CLIENT_ID = "51609443177-jb3b6pl4onl6h54pnq11isn07bqhr563.apps.googleusercontent.com";
            $id_token = $_POST['credential'];
            $client = new Google_Client(['client_id' => $CLIENT_ID]);
            $jwt = new \Firebase\JWT\JWT;
            $jwt::$leeway = 60;
            $payload = $client->verifyIdToken($id_token);

            $full_name = $format->validation($payload['name']);
            $gmail = $format->validation($payload['email']);
            $image = $format->validation($payload['picture']);

            $google_checks = $common->first("users", "gmail = :email", ['email' => $gmail]);
            if ($google_checks) {
                $type_check = $google_checks['type'];
                if($google_checks['status'] == '1') {
                    Session::set('login', true);
                    Session::set('user_id', $google_checks['id']);
                    //(new RememberCookie())->setRememberCookie($google_checks);
                    echo 'logged_in';
                } else {
                    echo 'Your account has been blocked!';
                }
            } else {
                $google_insert = $common->insert('users', ['full_name' => $full_name, 'type' => $type, 'gmail' => $gmail, 'image' => $image]);
                if ($google_insert) {
                    $user_infos = $common->first("`users`", "`gmail` = :email", ['email' => $gmail]);
                  	Session::set('login', true);
                    Session::set('user_id', $user_infos['id']);
                   // (new RememberCookie())->setRememberCookie($user_infos);
                    echo 'logged_in';
                }
            }
        } elseif ($type == 'facebook') {
            $full_name = $format->validation($_POST['full_name']);
            $gmail = $format->validation($_POST['gmail']);
            $facebook_id = $format->validation($_POST['facebook_id']);
            $image = $format->validation($_POST['image']);

            if (!empty($gmail)) {
                $facebook_checks = $common->first("`users`", "gmail = :email || facebook_id = :facebook_id", ['email' => $gmail, 'facebook_id' => $facebook_id]);
            } else {
                $facebook_checks = $common->first("`users`", "`facebook_id` = :facebook_id", ['facebook_id' => $facebook_id]);
                $gmail = NULL;
            }
            if ($facebook_checks) {
                $type_check = $facebook_checks['type'];
                if($facebook_checks['status'] == '1') {
                   Session::set('login', true);
                    	Session::set('user_id', $facebook_checks['id']);
                       // (new RememberCookie())->setRememberCookie($facebook_checks);
                    echo 'logged_in';
                } else {
                    echo 'Your account has been blocked!';
                }
            } else {
                $facebook_insert = $common->insert("users", [
                    'full_name' => $full_name,
                    'type' => $type,
                    'gmail' => $gmail,
                    'facebook_id' => $facebook_id,
                    'image' => $image
                ]);

                if ($facebook_insert) {
                    $user_infos = $common->first("`users`", "`facebook_id` = :facebook_id", ['facebook_id' => $facebook_id]);
                    Session::set('login', true);
                    Session::set('user_id', $user_infos['id']);
                   // (new RememberCookie())->setRememberCookie($user_infos);
                    echo 'logged_in';
                }
            }
        } elseif ($type == 'email') {
            $gmail = $format->validation($_POST['gmail']);
            $password = $format->validation($_POST['password']);
            $email_checks = $common->first("`users`", "`gmail` = :email", ['email' => $gmail]);
            if ($email_checks) {                
                    if(password_verify($password, $email_checks['password'])) {
                        if($email_checks['status'] == '1') {
                            Session::set('login', true);
                    		Session::set('user_id', $email_checks['id']);
                            //(new RememberCookie())->setRememberCookie($email_checks);
                            echo 'logged_in';
                        } else {
                            echo 'Your account has been blocked!';
                        }
                    } else {
                        echo 'Password does not match!';
                    }
                
            } else {
                echo 'No account found!';
            }
        }elseif($type=='instagram'){
             $code= $_POST['credential'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.instagram.com/oauth/access_token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = array(
                'client_id' => '614656524100935',
                'client_secret' => '27b9887112c7ff3261d1f5f14cf579ed',
                'grant_type' => 'authorization_code',
                'redirect_uri' => 'https://mejorcadadia.com/',
                'code' => $code
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            $result = curl_exec($ch);
           
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            $resObj=json_decode($result);
            if(!empty($resObj)){
                $access_token=$resObj->access_token;
                $user_id=$resObj->user_id;
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://graph.instagram.com/v17.0/'.$user_id.'?fields=account_type,id,username&access_token='.$access_token);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                $resObj=json_decode($result);

                if(!empty($resObj)){
                    $user_checks = $common->first("users", "gmail = :email AND type= :type", ['email' => $resObj->id,'type'=>$type]);
                    if ($google_checks) {
                        $type_check = $google_checks['type'];
                        if($google_checks['status'] == '1') {
                            Session::set('login', true);
                            Session::set('user_id', $google_checks['id']);
                           echo 'logged_in';
                        } else {
                            echo 'Your account has been blocked!';
                        }
                    } else {
                        $user_insert = $common->insert('users', ['full_name' => $resObj->username, 'type' => $type, 'gmail' => $resObj->id]);
                        if ($user_insert) {
                            $user_infos = $common->first("`users`", "`gmail` = :email AND type= :type", ['email' => $resObj->id,'type'=>$type]);
                              Session::set('login', true);
                            Session::set('user_id', $user_infos['id']);
                           // (new RememberCookie())->setRememberCookie($user_infos);
                           echo 'logged_in';
                        }
                    }
                }
            }
        }
    }
}

if(isset($_POST['forgot_password']) && ($_POST['forgot_password'] == 'forgot_password')) {
	$gmail = $format->validation($_POST['email']); 
    $email_check = $common->first("users", "gmail = :email", ['email' => $gmail]);
    if($email_check){
        $code_rand = time();
        $key = "mejorcadadia.com";
        $time = time();
        $hash = hash_hmac('sha256', $time, $key);
        $code_newhashpass = $hash;
        Session::set($code_newhashpass, $gmail);
        
        $mail_body = "Has olvidado tu contraseña MejorCadaDía, <br><br>
                        Ha solicitado restablecer su contraseña.<br>
                        Haga clic en el enlace a continuación para restablecer su contraseña.<br>  <br>   
                        ".$reset_link." <br><br>

                        Bienvenido, <br>
                        Miguel De La Fuente <br>";
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.ionos.es";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = "info@mejorcadadia.com"; //enter you email address
        $mail->Password = "k8#pb27%Iae*ml!A!ZlxD3(C";
        $mail->Subject = "Has miguelolvidado tu contraseña MejorCadaDÃa.com";
        $mail->setFrom('miguel@mejorcadadia.com');
        $mail->addReplyTo('miguel@mejorcadadia.com');
        $mail->isHTML(true);
        $mail->Body = $mail_body;
        $mail->addAddress($gmail); //enter receiver email address
        if($mail->send()) {
            echo 'Se ha enviado un correo electrónico a su dirección de correo electrónico con instrucciones para restablecer la contraseña.';
          } else {
            echo 'Failed to send mail!';
          }
          $mail->smtpClose();

    }else{
        echo 'No account is registered with this email.';
    }
    
}

// email verification and login
if(isset($_POST['email_verification_login'])) {
    $dob = $format->validation($_POST['dob']);
    if(!empty($dob)){
        $dob=date('Y-m-d',strtotime($dob));
    }else{
        $dob='';
    }
    $gmail = $format->validation($_POST['email']);
    $password = password_hash($format->validation($_POST['password']), PASSWORD_DEFAULT);
    $code = $format->validation($_POST['code']);
    if (isset($_SESSION[$gmail])) {
      if ($code == Session::get($gmail)) {
        $full_name_exp = explode("@", $gmail);
        $full_name = $full_name_exp[0];
        $email_insert = $common->insert("users", ['full_name' => $full_name, 'type' => 'email', 'gmail' => $gmail, 'password' => $password,'dob'=>$dob]);
        if ($email_insert) {
          $user_infos = $common->first("`users`", "gmail = :email", ['email' => $gmail]);
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
