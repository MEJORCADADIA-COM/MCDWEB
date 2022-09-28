<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../../lib/Database.php');
include_once ($filepath . '/../../lib/Session.php');
include_once ($filepath . '/../../helper/Format.php');
include_once ($filepath . '/../../classes/Common.php');


$db = new Database();
$fm = new Format();
$common = new Common();

if (Session::get('user_id') !== NULL) {
	$user_id = Session::get('user_id');
    $user_info = $common->select("`users`", "`id` = '$user_id'");
    if ($user_info) {
      $user_infos = mysqli_fetch_assoc($user_info);
    }
}

Session::checkSession();
if(isset($_GET['logout'])) {
	Session::destroy();
}
$current_file_name = basename($_SERVER['SCRIPT_FILENAME']);
$goalType='';
if($current_file_name=='supergoals.php'){
    $goalType=isset($_REQUEST['type'])? trim($_REQUEST['type']):'weekly';
}



$profile_info = $common
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
    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId=1108588056758284&autoLogAppEvents=1"
        nonce="JQBAhE2Y"></script>
        <link rel="stylesheet" href="<?=SITE_URL;?>/users/assets/bootstrap-datepicker.min.css">
     
        <link rel="stylesheet" href="./assets/style.css">
        <script>
      var SITE_URL='<?=SITE_URL; ?>';
      </script>
    <style>
        @media screen and (max-width: 480px) {
            .dropdown-item:focus, .dropdown-item:hover {   
                color: #738297;
                background-color: transparent;
            } 

            .heading1 {
                font-size: 12px; 
                font-family: cursive; 
                color: #ffffff;
                margin-bottom: -40px;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                align-content: center;
            }

            .migualtitle {
                font-size: 8px;
                color: #ffffff;
                margin-right: 0rem!important;
            }

            .buttondiv {
                width: 75%;
                float: right;
            }

            .hidemobileshow {
                display: block;
            }
        }

        @media screen and (min-width: 600px) {
            .dropdown-item:focus, .dropdown-item:hover {   
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 12px; 
                font-family: cursive; 
                color: #ffffff;
                margin-bottom: -40px;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                align-content: center;
            }

            .migualtitle {
                font-size: 8px;
                color: #ffffff;
                margin-right: 0rem!important;
            }

            .buttondiv {
                width: 75%;
                float: right;
            }

            .hidemobileshow {
                display: block;
            }
        }

        @media screen and (min-width: 786px) {
            .dropdown-item:focus, .dropdown-item:hover {   
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px; 
                font-family: cursive; 
                color: #ffffff;
                margin-bottom: -40px;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                align-content: center;
            }

            .migualtitle {
                font-size: 22px;
                color: #ffffff;
                margin-right: 1rem!important;
            }

            .buttondiv {
                width: 40%;
                float: right;
            }

            .hidemobileshow {
                display: none;
            }
        }

        @media screen and (min-width: 992px) {
            .dropdown-item:focus, .dropdown-item:hover {   
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px; 
                font-family: cursive; 
                color: #ffffff;
                margin-bottom: -40px;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                align-content: center;
            }

            .migualtitle {
                font-size: 22px;
                color: #ffffff;
                margin-right: 1rem!important;
            }

            .buttondiv {
                width: 40%;
                float: right;
            }

            .hidemobileshow {
                display: none;
            }
        }

        @media screen and (min-width: 1200px) {
            .dropdown-item:focus, .dropdown-item:hover {   
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px; 
                font-family: cursive; 
                color: #ffffff;
                margin-bottom: -40px;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: center;
                align-content: center;
            }

            .migualtitle {
                font-size: 22px;
                color: #ffffff;
                margin-right: 1rem!important;
            }

            .buttondiv {
                width: 40%;
                float: right;
            }

            .hidemobileshow {
                display: none;
            }
        }
        .hidemobileshow  .nav-link {color:#FFF; padding: 0.5rem 0.75rem 0.2rem 0.75rem}
        .description-area .print-description{display:none;}
        @media print {
             section .header-navbar-mobile, .footer-navbar, .tox-statusbar, .tox-statusbar__path-item, .tox-statusbar__text-container, .tox-statusbar__wordcount, .tox-statusbar__branding, .tox-statusbar__text-container, .screenonly{display:none;}
             .heading1,.migualtitle{color:#FFF;} 
             .migualtitle{font-size:12px; padding-right:10px;}    
             .heading1{
                font-size:24px;
             }   
             .edit-actions{display:none !important;}
             .description-area .tox.tox-tinymce{display:none;}
             .description-area .print-description{display:block; color:#000; background:#FFF; padding:15px;}
            .admin-dashbord{
                overflow-y:unset;
                -webkit-print-color-adjust: exact;
            }
            .admin-dashbord {
                min-height: auto;
                height:auto;
            }
        }
    </style>
</head>
<body>
<section class="admin-dashbord" >
        <nav class="navbar header-navbar navbar-dark sticky-top flex-md-nowrap pb-2 navselect">
            <a class="navbar-brand col-sm-3 col-md-2 mr-0 py-0" href="https://mejorcadadia.com/admin/">
                <img src="https://mejorcadadia.com/users/assets/logo.png" alt="image" width="100px">
            </a>
            <h1 class="heading1">Making Ever Day Extraordinary</h1>
            <!-- Example single danger button -->
            <div>
                <h1 class="migualtitle">By Miguel De La Fuente</h1>
                <div class="btn-group me-3 d-flex align-items-center buttondiv screenonly">
                    <button type="button" class="btn btn-info dropdown-toggle screenonly" data-bs-toggle="dropdown" aria-expanded="false">
                        Users
                    </button>
                    <ul class="dropdown-menu" style="left: auto; right: 0;">
                        <li><a class="dropdown-item" href="https://mejorcadadia.com/users/profile.php">Edit Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="https://mejorcadadia.com/users/logout.php" onclick="return confirm('Are you sure to logout?');">Logout</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="hidemobileshow header-navbar-mobile screenonly" style="background-color: #1076be;">
            <li class="nav-item" style="list-style: none;">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">
                    Cartas Eternidad
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: #0158a3; padding: 0rem 0.75rem; width: 100%;border: 0px;border-radius: 0px;top: -2px;">
                    <li class="nav-item"><a class="nav-link<?= $path == 'index.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/users/index.php" id="navbarDropdown" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">Tablero</a></li>    
                    <li><a style="border-left: 5px solid transparent; color: #738297; padding: 0.2rem 0.75rem; font-size: 14px;font-size: 16px;color: #ffffff;" class="dropdown-item" href="https://mejorcadadia.com/users/notebook.php">Escribe Carta</a></li>
                </ul>
            </li>
            <li class="nav-item" style="list-style: none; screenonly">
                                <a class="nav-link dropdown-toggle " href="#" id="navbarDropdownsupergoals" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">
                                SuperObjetivos
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownsupergoals" style="background-color: #0158a3; padding: 0rem 0.75rem; width: 100%;border: 0px;">
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php" >Semanal</a></li>    
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=monthly">Mensual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=quarterly">Trimestral</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=yearly">Anual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=lifetime">De por Vida</a></li>
                                </ul>
                            </li>
        </div>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-md-block sidebar" style="top: 89px;width: 12%;position: inherit;">
                    <h1 style="color: #ffffff; font-size: 17px; text-align: center; background-color: #fdaf40; padding: 7px; margin: 0px;">Menu</h1>
                    <div class="sidebar-sticky" style="padding-top: 0px;width: 100%;background-color: #1076be;">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">
                                    Cartas Eternidad
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: #0158a3; padding: 0rem 0.75rem; width: 100%;border: 0px;">
                                    <li class="nav-item"><a class="nav-link<?= $path == 'index.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/users/index.php" id="navbarDropdown" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">Tablero</a></li>    
                                    <li><a style="border-left: 5px solid transparent; color: #738297; padding: 0.2rem 0.75rem; font-size: 14px;font-size: 16px;color: #ffffff;" class="dropdown-item" href="https://mejorcadadia.com/users/notebook.php">Escribe Carta</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle " href="#" id="navbarDropdownsupergoals" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;font-size: 16px;color: #ffffff;">
                                SuperObjetivos
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownsupergoals" style="background-color: #0158a3; padding: 0rem 0.75rem; width: 100%;border: 0px;">
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php" >Semanal</a></li>    
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=monthly">Mensual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=quarterly">Trimestral</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=yearly">Anual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?=SITE_URL;?>/users/supergoals.php?type=lifetime">De por Vida</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>