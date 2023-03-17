<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../../lib/Database.php');
include_once($filepath . '/../../lib/Session.php');
include_once($filepath . '/../../lib/RememberCookie.php');
include_once($filepath . '/../../helper/Format.php');
include_once($filepath . '/../../classes/Common.php');
require_once base_path('/vendor/autoload.php');


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
$userFolders=[];
$user_id = Session::get('user_id');
if (!$user_infos && Session::get('user_id') !== NULL) {
    $user_id = Session::get('user_id');
    $user_infos = $common->first("`users`", "`id` = :id", ['id' => $user_id]);
    
}

if (!Session::checkSession() && !$user_infos) {
    header("Location: " . SITE_URL);
    return;
}

if (isset($_GET['logout'])) {
    Session::destroy();
}

$current_file_name = basename($_SERVER['SCRIPT_FILENAME']);
$goalType = '';
if ($current_file_name == 'supergoals.php') {
    $goalType = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'weekly';
}

$profile_info = $common;
$my_notes_count=0;

if (Session::get('user_id') !== NULL) {
    $user_id = Session::get('user_id');   
   $userFolders=$common->db->query('SELECT user_folders.*, (SELECT count(*) FROM user_notes WHERE folder_id=user_folders.id) as notes_count FROM user_folders WHERE user_id='.$user_id)->fetchAll();
   $my_notes_count = $common->count("user_notes", 'user_id = :user_id AND folder_id = :folder_id', ['user_id' => $user_id, 'folder_id' => 0]);
}

//$common->db->query('ALTER TABLE user_notes DROP COLUMN title;');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://blog.mejorcadadia.com/wp-content/uploads/2022/04/mcdf-01.png" type="image/x-icon">
    <title>Mejorcadadia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId=1108588056758284&autoLogAppEvents=1" nonce="JQBAhE2Y"></script>
    <link rel="stylesheet" href="<?= SITE_URL; ?>/users/assets/bootstrap-datepicker.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css'>

    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="<?= SITE_URL; ?>/users/assets/button.css">
    <script>
        var SITE_URL = '<?= SITE_URL; ?>';
    </script>
    <style>
        body {
            font-family: 'Montserrat';
        }

        .goals-area {
                padding: 20px 0px;
        }
        @media screen and (max-width: 767px) {
            .navbar-brand {
                order: 2;
            }

            .navbar-brand img {
                width: 78px !important;
            }

            .brand-info-bar {
                text-align: center;
            }

            .brand-info-bar .heading1 {
                margin-bottom: 5px;
            }

            .admin-dashbord .navbar {
                align-items: baseline;
            }

            

            .migualtitle {
                font-size: 10px;
                color: #ffffff;
                margin-right: 0rem !important;
            }

            .card-header {
                font-size: 1.1rem;
            }

            .goals-area ol li {
                font-size: 1rem !important;
                min-height: 30px;
            }

            .goals-area {
                padding-right: 5px !important;
            }
        }

        @media screen and (max-width: 480px) {

            .dropdown-item:focus,
            .dropdown-item:hover {
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 12px;
                font-family: cursive;
                color: #ffffff;
            }

            .navselect {
                background-color: #74be41 !important;
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                align-items: baseline;
                align-content: center;
            }

            .migualtitle {
                font-size: 10px;
                color: #ffffff;
                margin-right: 0rem !important;
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

            .dropdown-item:focus,
            .dropdown-item:hover {
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 12px;
                font-family: cursive;
                color: #FFF;
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
                margin-right: 0rem !important;
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

            .dropdown-item:focus,
            .dropdown-item:hover {
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px;
                font-family: cursive;
                color: #FFF;
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
                font-size: 20px;
                color: #ffffff;
                margin-right: 1rem !important;
            }
            .migualtitle p{font-size:18px; margin-top:2px;}

            .buttondiv {
                width: 40%;
                float: right;
            }

            .hidemobileshow {
                display: none;
            }
        }

        @media screen and (min-width: 992px) {

            .dropdown-item:focus,
            .dropdown-item:hover {
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px;
                font-family: cursive;
                color: #FFF;
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
                margin-right: 1rem !important;
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

            .dropdown-item:focus,
            .dropdown-item:hover {
                color: #738297;
                background-color: transparent;
            }

            .heading1 {
                font-size: 34px;
                font-family: cursive;
                color: #FFF;
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
                margin-right: 1rem !important;
            }

            .buttondiv {
                width: 40%;
                float: right;
            }

            .hidemobileshow {
                display: none;
            }
        }

        .hidemobileshow .nav-link {
            color: #FFF;
            padding: 0.5rem 0.75rem 0.2rem 0.75rem
        }

        .description-area .print-description {
            display: none;
        }

        .datepicker.datepicker-dropdown {
            z-index: 9999 !important;
        }

        @media print {

            section .header-navbar-mobile,
            .footer-navbar,
            .tox-statusbar,
            .tox-statusbar__path-item,
            .tox-statusbar__text-container,
            .tox-statusbar__wordcount,
            .tox-statusbar__branding,
            .tox-statusbar__text-container,
            .screenonly {
                display: none;
            }

            .heading1,
            .migualtitle {
                color: #FFF;
            }

            .migualtitle {
                font-size: 12px;
                padding-right: 10px;
            }

            .heading1 {
                font-size: 24px;
            }

            .edit-actions {
                display: none !important;
            }

            .description-area .tox.tox-tinymce {
                display: none;
            }

            .description-area .print-description {
                display: block;
                color: #000;
                background: #FFF;
                padding: 15px;
            }

            .admin-dashbord {
                overflow-y: unset;
                -webkit-print-color-adjust: exact;
            }

            .admin-dashbord {
                min-height: auto;
                height: auto;
            }
        }

        .custom-toggler,
        .custom-toggler:active {
            border: none;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255,255,255, 1)' stroke-width='4' stroke-linecap='round' stroke-miterlimit='10' d='M4 8h24M4 16h24M4 24h24'/%3E%3C/svg%3E") !important;
            width: 1.8em;
            height: 1.8em;
        }
        .offcanvas{
            z-index: 9999;
        }
        .offcanvas .navbar-nav .nav-link.active {
            font-weight: bold;
            color: #FFF;
        }

        .offcanvas li a {
            color: #FFF;
            padding: 5px 10px;
            font-size: 1.2rem;
        }

        .offcanvas li a:active,
        .offcanvas li a:hover {
            color: #FFF;
        }

        .offcanvas .submenu li {
            list-style: none;
        }

        .offcanvas a.profile-icon {
            display: inline-block;
            width: 50px;
        }

        .offcanvas a.profile-icon img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }

        .offcanvas .offcanvas-header {
            background: #74be41;
            color: #FFF
        }

        ;

        .desktop-left-sidebar.sidebar ul,
        .desktop-left-sidebar.sidebar li {
            list-style: none !important;
        }

        .desktop-left-sidebar.sidebar .nav-link {
            color: #FFF;
            font-size: 16px;
        }

        .sidebar ul.submenu {
            list-style: none;
            padding-left: 1rem;
        }

        .row main {
            padding-left: 0;
            padding-right: 0
        }
        .lastfooteritem{
            margin-top:40px;
        }
        .lastfooteritem .migualtitle{
            font-size:20px; text-align:center;
        }
        .lastfooteritem .migualtitle p{
            font-size:16px; 
            margin-top:4px;
        }
    </style>
</head>

<body>
<!-- Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" aria-labelledby="createFolderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <form method="post" id="createFolderForm">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createFolderModalLabel">Carpeta</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="folder_name" id="folder_name" class="form-control" required placeHolder="Carpeta">
        <input type="hidden" name="folder_id" id="folder_id" value="0">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" id="saveCreateFolderBtn" class="btn btn-primary">Save changes</button>
      </div>
    </form>
    </div>
  </div>
</div>
    <!-- Alerts Start -->
    <?php if (Session::hasSuccess()) : ?>
        <div class="alert-timeout alert alert-success w-sm-100 w-md-50 mx-auto fixed-top" role="alert">
            <?= Session::getSuccess() ?>
        </div>
    <?php endif; ?>
    <?php if (Session::hasError()) : ?>
        <div class="alert-timeout alert alert-danger w-sm-100 w-md-50 mx-auto fixed-top" role="alert">
            <?= Session::getError() ?>
        </div>
    <?php endif; ?>
    <!-- Alerts End -->

    <section class="admin-dashbord">
        <nav class="navbar header-navbar navbar-dark sticky-top flex-md-nowrap pb-2 navselect">

            <a class="btn d-block d-md-none custom-toggler" data-bs-toggle="offcanvas" href="#offcanvasWithBothOptions" role="button" aria-controls="offcanvasWithBothOptions">
                <span class="navbar-toggler-icon"></span>
            </a>
            <a class="navbar-brand mr-0 py-0" href="<?php echo SITE_URL; ?>">
                <img src="https://mejorcadadia.com/users/assets/logo.png" alt="image" width="100px">
            </a>
            <h1 class="heading1 d-none d-sm-block">Making Every Day Masterpiece</h1>
            <!-- Example single danger button -->
            <div class="brand-info-bar">
                <h1 class="heading1 d-block d-md-none">Making Every Day a Masterpiece</h1>
                <h1 class="migualtitle">By Miguel De La Fuente
                    <p class="d-none d-md-block d-lg-block">+507 6445-1418</p>
                </h1>


            </div>
        </nav>
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel" style="background-color: #1076be;">
            <div class="offcanvas-header">

                <h5 class="offcanvas-title">
                    <a href="#" class="profile-icon">
                        <?php if (!empty($user_infos['image'])) {
                            $profileIcon = $user_infos['image'];
                        } else {
                            $profileIcon = 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png';
                        }
                        $profileIcon = 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>
                        <img src="<?= $profileIcon; ?>" alt="image">
                    </a>
                    <?= $user_infos['full_name'] ?>
                </h5>

                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php  if($current_file_name=='mynotes.php'): ?>
                        <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>/users/dailygoals.php"><i class="fa fa-arrow-left"></i> Atrás</a>
                    </li>
                    <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button">MejorNotes</a>
                                <ul class="submenu my-notes-menu" id="mynotesmenu">
                                    <li class="nav-item folder-item" id="folder-0">
                                        <a class="nav-link" href="mynotes.php" role="button">
                                        <i class="fa fa-list me-3"></i>
                                        Mis Notas
                                        <span data-count="<?=$my_notes_count;?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$my_notes_count;?></span>
                                        </a>
                                    </li>
                                    <?php foreach($userFolders as $folder): ?>
                                        <li class="nav-item folder-item" id="folder-<?=$folder['id']; ?>">
                                        <a class="nav-link " href="mynotes.php?folder_id=<?=$folder['id']; ?>" role="button"><i class="fa fa-folder-o me-3"></i><?=$folder['name']; ?>  <span data-count="<?=$folder['notes_count'];?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$folder['notes_count'];?></span> </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="nav-item create-folder-nav" id="create_folder_nav_item">
                                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#createFolderModal" role="button"><i class="fa fa-folder-open me-3"></i>Carpeta</a> 
                                    </li>                                    

                                </ul>
                            </li>  
                    <?php else: ?>  
                    
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>">Home</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" role="button">MejorJournal</a>
                        <ul class="submenu">
                        <li class="nav-item"><a class="nav-link" href="dailygoals.php" role="button">Victoria7</a> </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="cronovida.php" role="button">
                                    CronoVida
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="capsules.php" role="button">MejorCapsules</a> </li>
                            <li class="nav-item">
                                <a class="nav-link" href="dailycommitments.php" role="button">
                                    Guerrero Diario
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#SuperObjetivos" id="navbarDropdownsupergoals" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="SuperObjetivos">
                                    SuperObjetivos
                                </a>
                                <ul id="SuperObjetivos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdownsupergoals" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php">Semanal</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly">Mensual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly">Trimestral</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly">Anual</a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime">De por Vida</a></li>
                                    <!-- New Pages -->
                                    <br />
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/missions.php">Mi Missión</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php">Mi Visión</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/commitments.php">Mis Compromisos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/agreements.php">Mis Acuerdos</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/promises.php">Mis Promesas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/lifeTasks.php">Mi tarea de Vida</a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a class="nav-link dropdown-toggle" href="#superMemorias" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                    SuperMemorias
                                </a>
                                <ul id="superMemorias" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dailyVictories.php">Mi Victoria Diaria</a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/toRemember.php">Eventos para Recordar</a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/biggestVictories.php">Mis Mayores Victorias</a></li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#dentletter" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                    Cartas para la Eternidad
                                </a>
                                <ul id="dentletter" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link<?= $path == 'index.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/users/index.php" id="navbarDropdown">Cartas</a></li>
                                    <li class="nav-item"><a class="nav-link" href="https://mejorcadadia.com/users/notebook.php">Escribe Carta</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                       <a class="nav-link" aria-current="page" href="mynotes.php">MejorNotes</a>
                    </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://blog.mejorcadadia.com">MejorBlog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=" <?= SITE_URL; ?>/users/inspirations.php">MejorInspiration</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL; ?>/users/profile.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL; ?>/users/logout.php" onclick="return confirm('Are you sure to logout?');">Salir</a>
                    </li>
                    <?php endif; ?>  
                    <li class="nav-item lastfooteritem">
                    <h1 class="migualtitle">By Miguel De La Fuente
                        <br>
                        <p>+507 6445-1418</p>
                    </h1>
                    </li> 

                </ul>
            </div>
        </div>


        <div class="container-fluid">
            <div class="row">

                <nav class="col-md-2 d-none d-md-block sidebar desktop-left-sidebar" style="top: 89px;position: inherit;">
                    <h1 style="color: #ffffff; font-size: 17px; text-align: center; background-color: #fdaf40; padding: 7px; margin: 0px;">Menu</h1>
                    <div class="sidebar-sticky bg-info" style="padding-top: 0px;width: 100%;">

                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            
                            
                            <?php  if($current_file_name=='mynotes.php'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="<?= SITE_URL; ?>/users/dailygoals.php"><i class="fa fa-arrow-left"></i> Atrás</a>
                                </li>
                                <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button">MejorNotes</a>
                                <ul class="submenu my-notes-menu" id="mynotesmenu">
                                    <li class="nav-item folder-item" id="folder-0">
                                        <a class="nav-link" href="mynotes.php" role="button">
                                        <i class="fa fa-list me-3"></i>
                                        Mis Notas
                                        <span data-count="<?=$my_notes_count;?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$my_notes_count;?></span>
                                        </a>
                                    </li>
                                    <?php foreach($userFolders as $folder): ?>
                                        <li class="nav-item folder-item" id="folder-<?=$folder['id']; ?>">
                                        <a class="nav-link " href="mynotes.php?folder_id=<?=$folder['id']; ?>" role="button"><i class="fa fa-folder-o me-3"></i><?=$folder['name']; ?>  <span data-count="<?=$folder['notes_count'];?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$folder['notes_count'];?></span> </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="nav-item create-folder-nav" id="create_folder_nav_item">
                                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#createFolderModal" role="button"><i class="fa fa-folder-open me-3"></i>Carpeta</a> 
                                    </li>                                    

                                </ul>
                            </li>  
                            <?php else: ?>
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>">Home</a>
                                </li>
                                
                                <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button">MejorJournal</a>
                                <ul class="submenu">
                                <li class="nav-item"><a class="nav-link" href="dailygoals.php" role="button">Victoria7</a> </li>
                                    
                                    <li class="nav-item">
                                        <a class="nav-link" href="cronovida.php" role="button">
                                            CronoVida
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="capsules.php" role="button">MejorCapsules</a> </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="dailycommitments.php" role="button">
                                            Guerrero Diario
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#SuperObjetivos" id="navbarDropdownsupergoals" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="SuperObjetivos">
                                            SuperObjetivos
                                        </a>
                                        <ul id="SuperObjetivos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdownsupergoals" style="margin-left:1rem;">
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php">Semanal</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly">Mensual</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly">Trimestral</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly">Anual</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime">De por Vida</a>
                                            </li>
                                            <!-- New Pages -->
                                            <br />
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/missions.php">Mi Missión</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php">Mi Visión</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/commitments.php">Mis Compromisos</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/agreements.php">Mis Acuerdos</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/promises.php">Mis Promesas</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/lifeTasks.php">Mi tarea de Vida</a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li>
                                        <a class="nav-link dropdown-toggle" href="#superMemorias" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                            SuperMemorias
                                        </a>
                                        <ul id="superMemorias" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dailyVictories.php">Mi Victoria Diaria</a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/toRemember.php">Eventos para Recordar</a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/biggestVictories.php">Mis Mayores Victorias</a></li>
                                        </ul>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#dentletter" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                            Cartas para la Eternidad
                                        </a>
                                        <ul id="dentletter" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link <?= $path == 'index.php' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/index.php" id="navbarDropdown">Cartas</a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/notebook.php">Escribe Carta</a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="mynotes.php">MejorNotes</a>
                                </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://blog.mejorcadadia.com">MejorBlog</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/inspirations.php">MejorInspiration</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/profile.php">Perfil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/logout.php" onclick="return confirm('Are you sure to logout?');">Salir</a>
                            </li>  
                            <?php endif; ?>
                             
                            
                           
                            


                        </ul>

                    </div>
                </nav>
