<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../lib/Database.php');
include_once($filepath . '/../lib/Session.php');
include_once($filepath . '/../lib/RememberCookie.php');
include_once($filepath . '/../helper/Format.php');
include_once($filepath . '/../classes/Common.php');


$db = new Database();
$fm = new Format();
$common = new Common();

$user_infos = null;
$rememberCookieData = RememberCookie::getRememberCookieData();
if ($rememberCookieData) {
    if ($rememberCookieData[RememberCookie::PASSWORD]) {
        $passwordComparator = "=";
    } else {
        $passwordComparator = "IS";
    }

    $user_infos = $common->first(
        "`users`",
        "`id` = :id AND password {$passwordComparator} :password AND remember_token = :remember_token",
        ['id' => $rememberCookieData[RememberCookie::ID], 'remember_token' => $rememberCookieData[RememberCookie::REMEMBER_TOKEN], 'password' => $rememberCookieData[RememberCookie::PASSWORD]]
    );
}

if ($user_infos === null && Session::get('user_id') !== NULL) {
    $user_id = Session::get('user_id');
    $user_infos = $common->first("users", "id = :id", ['id' => $user_id]);
}

$profile_info = $common
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#3367D6">
    <!-- Add to homescreen for Chrome on Android. -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="MejorCadaDía">  

    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="https://blog.mejorcadadia.com/wp-content/uploads/2022/04/mcdf-01.png" type="image/x-icon">
    <title>MejorCadaDía</title>
    <meta name="description" content="Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" />
    <meta property="og:title" content="MejorCadaDía" />
<meta property="og:description" content="Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" />
<meta property="og:image" content="https://mejorcadadia.com/assets/images/major-512x512.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId=1108588056758284&autoLogAppEvents=1" nonce="JQBAhE2Y"></script>
    <link rel="stylesheet" href="./assets/style.css">
    <script>
        var SITE_URL = '<?= SITE_URL; ?>';
    </script>
    <style>
        .login-option-buttons{
            width:375px; margin:0 auto;
        }
        @media only screen and (min-width: 768px) {
            .desktop-only {
                display: block;
            }

            .mobile-only {
                display: none;
            }

            .res_logo .mobile {
                display: none;
            }

            .res_logo .desktop {
                display: block;
            }
        }

        @media only screen and (max-width: 767px) {
            .desktop-only {
                display: none;
            }

            body.logged-in .desktop-only {
                display: block;
            }

            body.logged-in .responsive_view_text,
            body.logged-in .mobile-only {
                display: none;
            }

            body.guest .mobile-only {
                display: block;
            }

            body.guest .responsive_nav .blog-btn {
                display: none !important;
            }

            .login-option-buttons{
                width:100%; margin:0 auto;
            }

            .res_logo .mobile {
                display: block;
                margin: 0 auto;
            }

            .res_logo .desktop {
                display: none;
            }
            .nav{
                padding:20px 10px;
            }
            .nav-brand.mobile-only.ballon-menu{
                text-align:right; width:100%;
            }
            .ballon-menu .ballon-item{
                padding:6px 8px;
            }
            .ballon-menu{
                text-align:right; width:100%;
            }
            .nav-brand.desktop-only.logo-icon{
                display:none;
            }
        }
        #login_email_check_part{
            display:none;
        }
        #register-email-form{
            display:none;
        }
        #googleLoginBtnWrap{
            margin-bottom:1rem;
        }
        #googleRegisterBtnWrap{
            margin-bottom:1rem;
        }
        
        .alert.alert-white{
            background:#FFF;
            color:rgb(22, 24, 35);
            border: 1px solid rgba(22, 24, 35, 0.12);
            justify-content: center;
            align-items: center;
            cursor: pointer;
            padding:0.5rem;
            padding-left:1.6rem;
        }
        .alert.alert-white .icon-wrap{
            left: 12px;
            font-size: 20px;
            position: absolute;
            display: flex;
        }
        
        .bi {
            width: 1em;
            height: 1em;
        }
        #login-back-btn,#register-back-btn{
            position:absolute;
            top:12px;
            left:20px;
            cursor: pointer;
            z-index: 99999;
        }
        #login-back-btn, #register-back-btn{display:none;}
    </style>
</head>

<body class="<?= (Session::get('login')) ? 'logged-in' : 'guest'; ?>">