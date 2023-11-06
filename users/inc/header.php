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
$languages=[
    'en'=>"English",
    'es'=>"Spanish"
];
$locales=[
    'en'=>"en_EN",
    'es'=>"es_ES"
];

$currentURL = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if(isset($_GET['lang']) && !empty($_GET['lang'])){
    $slang=$_GET['lang'];
    if(!empty($languages[$slang])){
        Session::set('language', $slang);
        $userLanguage=$slang;
    }
    $paramNameToRemove='lang';
    $urlParts = parse_url($currentURL);
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParameters);
        unset($queryParameters[$paramNameToRemove]);
        $query = http_build_query($queryParameters);
        $updatedURL = $urlParts['scheme'] . '://' . $urlParts['host'] . $urlParts['path'];
        if (!empty($query)) {
            $updatedURL .= '?' . $query;
        }
    } else {
        $updatedURL = $currentURL;
    }
   header("Location: " . $updatedURL);
    exit();
}
function addParamToURL($paramName,$paramValue){
    global $currentURL;
     if (parse_url($currentURL, PHP_URL_QUERY)) {
         // If the URL already has query parameters, append the new one using "&"
         $updatedURL = $currentURL . "&" . $paramName . "=" . $paramValue;
     } else {
         // If the URL doesn't have any query parameters, add the new one using "?"
         $updatedURL = $currentURL . "?" . $paramName . "=" . $paramValue;
     }
     return $updatedURL;
 }
$userLanguage = Session::get('language');
if($userLanguage==false){
    $userLanguage='es';
}
$translations=[];
$translationFile = $filepath ."/../../translations/$userLanguage.json";
if(file_exists($translationFile)){
    $translations = json_decode(file_get_contents($translationFile), true);
}
function translate($key) {
    global $translations;
    if(!empty($translations[$key])){
        return $translations[$key];
    }
    return $key;
}
$user_infos = null;
$rememberCookieData = RememberCookie::getRememberCookieData();


$user_id = Session::get('user_id');
if (Session::get('user_id') !== NULL) {
    $user_id = Session::get('user_id');
    $user_infos = $common->first("`users`", "`id` = :id", ['id' => $user_id]);
    
}else{
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
}

$userFolders=[];


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
    <link rel="manifest" href="<?= SITE_URL; ?>/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="green">
    <meta name="apple-mobile-web-app-title" content="Mejor Cada Dia">
    <link rel="apple-touch-icon" href="/assets/images/major-192x192.png" sizes="192x192">
    <link rel="apple-touch-icon" href="/assets/images/major-512x512.png" sizes="512x512">
    <meta name="msapplication-TileImage" content="/assets/images/major-192x192.png">
    <meta name="msapplication-TileColor" content="green">



    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <?php if(!empty($preHead)){
        echo $preHead;
    } ?>
       
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">

    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v14.0&appId=1108588056758284&autoLogAppEvents=1" nonce="JQBAhE2Y"></script>
    <link rel="stylesheet" href="<?= SITE_URL; ?>/users/assets/bootstrap-datepicker.min.css">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css'>

    <link rel="stylesheet" href="./assets/style.css">
    <link rel="stylesheet" href="<?= SITE_URL; ?>/users/assets/button.css">
   <script src="<?= SITE_URL; ?>/build/ckeditor.js"></script>
    <script>
        window.editors = {};
        var SITE_URL = '<?= SITE_URL; ?>';
    </script>
    <style>
        .language-switcher .dropdown-menu{
            padding:0;
            width:113px;
            min-width:115px;
        }
        .ck-editor__editable_inline {
            min-height: 180px;
            font-size:16px
        }
        .ck-rounded-corners .ck.ck-editor__main>.ck-editor__editable, .ck.ck-editor__main>.ck-editor__editable.ck-rounded-corners {
            border-radius: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
        .ck-rounded-corners .ck.ck-editor__top .ck-sticky-panel .ck-toolbar, .ck.ck-editor__top .ck-sticky-panel .ck-toolbar.ck-rounded-corners {
            border-radius: 10px;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        body {
            font-family: 'Montserrat';
        }

        .goals-area {
                padding: 20px 0px;
        }
        @media screen and (max-width: 767px) {
            .uk-lightbox-button.uk-slidenav{
                display:none;
            }
            .navbar-brand 8
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

        @media screen and (max-width: 786px) {
            .language-switcher .dropdown-item, .language-switcher .dropdown-item:hover{
                color:#000;
                font-size:1rem;
            }
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
                font-size: 20px;
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
                font-size: 20px;
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
        @media screen and (min-width: 768px) {
            nav.sidebar.desktop-left-sidebar{
                width:286px;
            }
            .container-fluid main{
                width:calc(100% - 286px);
            }
        }
        .ck.ck-editor,.ck.ck-editor *{
            color:#000!important;
            font-size:1rem;
        }
        .sticky-top{
            z-index: 999;
        }
        .btn-social-icon{
            position: relative;
            height: 34px;
            width: 34px;
        }
        .btn-social-icon>:first-child {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 32px;
            line-height: 34px;
            font-size: 1.6em;
            text-align: center;
            border-right: 1px solid rgba(0,0,0,0.2);
        }
        .btn-social-icon>:first-child {
            border: none;
            text-align: center;
            width: 100% !important;
        }
        .btn-facebook {
    color: #fff;
    background-color: #3b5998;
    border-color: rgba(0,0,0,0.2)
}

.btn-facebook:focus,.btn-facebook.focus {
    color: #fff;
    background-color: #2d4373;
    border-color: rgba(0,0,0,0.2)
}

.btn-facebook:hover {
    color: #fff;
    background-color: #2d4373;
    border-color: rgba(0,0,0,0.2)
}

.btn-facebook:active,.btn-facebook.active,.open>.dropdown-toggle.btn-facebook {
    color: #fff;
    background-color: #2d4373;
    border-color: rgba(0,0,0,0.2)
}
.btn-twitter {
    color: #fff;
    background-color: #55acee;
    border-color: rgba(0,0,0,0.2)
}

.btn-twitter:focus,.btn-twitter.focus {
    color: #fff;
    background-color: #2795e9;
    border-color: rgba(0,0,0,0.2)
}

.btn-twitter:hover {
    color: #fff;
    background-color: #2795e9;
    border-color: rgba(0,0,0,0.2)
}

.btn-twitter:active,.btn-twitter.active,.open>.dropdown-toggle.btn-twitter {
    color: #fff;
    background-color: #2795e9;
    border-color: rgba(0,0,0,0.2)
}
.btn-whatsapp {
    color: #fff;
    background-color: #74BE41;
    border-color: rgba(0,0,0,0.2)
}
.btn-whatsapp:hover {
    color: #fff;
    background-color: #569a27;
    border-color: rgba(0,0,0,0.2)
}
.btn-email {
    color: #fff;
    background-color: #cc3783;
    border-color: rgba(0,0,0,0.2);
}
.btn-email:hover {
    color: #fff;
    background-color: #e064a4;
    border-color: rgba(0,0,0,0.2)
}

.secondray-navbar-menu .top-menu-items a{text-wrap:nowrap !important;}
.secondray-navbar-menu .top-menu-items{
    text-align:center;
}
.admin-dashbord {
    background: #ed008c;
}
.language-switcher{
    padding:0.5rem 0.75rem;
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

    <section class="admin-dashbord userinfoid-<?=$user_id;?>">
        <nav class="navbar header-navbar navbar-dark sticky-top flex-md-nowrap pb-2 navselect">

            <a class="btn d-block d-md-none custom-toggler" data-bs-toggle="offcanvas" href="#offcanvasWithBothOptions" role="button" aria-controls="offcanvasWithBothOptions">
                <span class="navbar-toggler-icon"></span>
            </a>
            <a class="navbar-brand mr-0 py-0" href="<?php echo SITE_URL; ?>">
                <img src="https://mejorcadadia.com/users/assets/logo.png" alt="image" width="100px">
            </a>
            <h1 class="heading1 d-none d-sm-block"><?=translate('Making Every Day Masterpiece');?></h1>
            <!-- Example single danger button -->
            <div class="brand-info-bar">
                <h1 class="heading1 d-block d-md-none"><?=translate('Making Every Day Masterpiece');?></h1>
                <h1 class="migualtitle">By Miguel De La Fuente
                    <p class="d-none d-md-block d-lg-block text-center">+507 6445-1418</p>
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
                        <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>/users/dailygoals.php"><i class="fa fa-arrow-left"></i> <?=translate('Atrás');?></a>
                    </li>
                    <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button"><?=translate('MejorNotes');?></a>
                                <ul class="submenu my-notes-menu" id="mynotesmenu">
                                    <li class="nav-item folder-item" id="folder-0">
                                        <a class="nav-link" href="mynotes.php" role="button">
                                        <i class="fa fa-list me-3"></i>
                                       <?=translate('Mis Notas');?>
                                        <span data-count="<?=$my_notes_count;?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$my_notes_count;?></span>
                                        </a>
                                    </li>
                                    <?php foreach($userFolders as $folder): ?>
                                        <li class="nav-item folder-item" id="folder-<?=$folder['id']; ?>">
                                        <a class="nav-link " href="mynotes.php?folder_id=<?=$folder['id']; ?>" role="button"><i class="fa fa-folder-o me-3"></i><?=$folder['name']; ?>  <span data-count="<?=$folder['notes_count'];?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$folder['notes_count'];?></span> </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="nav-item create-folder-nav" id="create_folder_nav_item">
                                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#createFolderModal" role="button"><i class="fa fa-folder-open me-3"></i><?=translate('Carpeta');?></a> 
                                    </li>                                    

                                </ul>
                            </li>  
                    <?php else: ?>  
                    
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>"> <i class="fa fa-home" aria-hidden="true"></i><?=translate('Home');?></a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#menuMejorjournal" id="mejorjournalLinkItem" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="menuMejorjournal"><i class="fa fa-tasks" aria-hidden="true"></i> <?=translate('ExitoTotal Journal');?></a>
                        <ul class="submenu list-unstyled fw-normal pb-1 small collapse hide pb-1 ms-3" id="menuMejorjournal" aria-labelledby="mejorjournalLinkItem">
                        <li class="nav-item"><a class="nav-link" href="dailygoals.php" role="button"> <i class="fa fa-diamond" aria-hidden="true"></i>  <?=translate('Victoria7');?></a> </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="cronovida.php" role="button">
                                <i class="fa fa-clock-o" aria-hidden="true"></i> <?=translate('CronoVida');?>
                                </a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="capsules.php" role="button"><i class="fa fa-flask" aria-hidden="true"></i> <?=translate('MejorCapsules');?></a> </li>
                            <li class="nav-item">
                                <a class="nav-link" href="dailycommitments.php" role="button">
                                <i class="fa fa-hand-rock-o" aria-hidden="true"></i> <?=translate('Guerrero Diario');?>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#SuperObjetivos" id="navbarDropdownsupergoals" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="SuperObjetivos">
                                <i class="fa fa-bullseye" aria-hidden="true"></i> <?=translate('SuperObjetivos');?>
                                </a>
                                <ul id="SuperObjetivos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdownsupergoals" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php"><?=translate('Semana');?></a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly"><?=translate('Mes');?></a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly"><?=translate('Trimestre');?></a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly"><?=translate('Año');?></a></li>
                                    <li class="nav-item"><a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime"><?=translate('100dreams');?></a></li>
                                    <!-- New Pages -->
                                    <br />
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/missions.php"><?=translate('Mi Missión');?></a>
                                    </li>
                                    
                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#mivisions" id="mivisionsDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                        Mi Visión
                                        </a>
                                        <ul id="mivisions" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="mivisionsDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=3"><?=translate('Visión 3-Años');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=5"><?=translate('Visión 5-Años');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=10"><?=translate('Visión 10-Años');?></a></li>                                            
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/commitments.php"><?=translate('Mis Compromisos');?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/agreements.php"><?=translate('Mis Acuerdos');?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/promises.php"><?=translate('Mis Promesas');?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= SITE_URL; ?>/users/lifeTasks.php"><?=translate('Mi tarea de Vida');?></a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a class="nav-link dropdown-toggle" href="#superMemorias" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <i class="fa fa-trophy" aria-hidden="true"></i> <?=translate('SuperVictorias');?>
                                </a>
                                <ul id="superMemorias" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dailyVictories.php"><?=translate('Mi Victoria Diaria');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/toRemember.php"><?=translate('Eventos para Recordar');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/biggestVictories.php"><?=translate('Mis Mayores Victorias');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/superdias.php"><?=translate('SuperDias');?></a></li>
                                    
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="supermemories.php"><i class="fa fa-tree" aria-hidden="true"></i> <?=translate('SuperMemorias');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#dentletter" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i> <?=translate('Cartas para la Eternidad');?>
                                </a>
                                <ul id="dentletter" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link<?= $path == 'index.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/users/index.php" id="navbarDropdown"><?=translate('Cartas');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="https://mejorcadadia.com/users/notebook.php"><?=translate('Escribe Carta');?></a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#Imagenes" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <i class="fa fa-picture-o" aria-hidden="true"></i> <?=translate('Imagenes');?>
                                </a>
                                <ul id="Imagenes" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/victory-images.php"><?=translate('Gallery');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dream-wall.php"><?=translate('Dream wall');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/photo-drive.php"><?=translate('Imagenes de Exito');?></a></li>
                                </ul>
                            </li>
                                    <li class="nav-item"><a class="nav-link" aria-current="page" href="<?= SITE_URL; ?>/users/victory-media.php?type=audio">
                                    <i class="fa fa-volume-up" aria-hidden="true"></i> <?=translate('Audios');?></a></li>
                                    
                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#mivideos" id="mivideosDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                        <i class="fa fa-video-camera" aria-hidden="true"></i> <?=translate('Videos');?>
                                        </a>
                                        <ul id="mivideos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="mivideosDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/video-playlists.php"><?=translate('Upload Video');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/victory-media.php?type=video"><?=translate('Victory 7 Videos');?></a></li>
                                            
                                        </ul>
                                    </li>
                                    
                            <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="mynotes.php"><i class="fa fa-sticky-note-o" aria-hidden="true"></i> <?=translate('MejorNotes');?></a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://blog.mejorcadadia.com"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <?=translate('MejorBlog');?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=" <?= SITE_URL; ?>/users/inspirations.php"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?=translate('MejorInspiration');?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-coffee" aria-hidden="true"></i> <?=translate('MejorCadaDía Chef');?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-bed" aria-hidden="true"></i> <?=translate('MejorCadaDía Hotel');?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa fa-fire" aria-hidden="true"></i> <?=translate('MejorFest');?></a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL; ?>/users/backup.php"><i class="fa fa-cloud-upload" aria-hidden="true"></i> <?=translate('DropBox Backup');?></a>
                    </li>   
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL; ?>/users/profile.php"><i class="fa fa-user-o" aria-hidden="true"></i> <?=translate('Perfil');?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= SITE_URL; ?>/users/logout.php" onclick="return confirm('Are you sure to logout?');"><i class="fa fa-power-off" aria-hidden="true"></i> <?=translate('Salir');?></a>
                    </li>
                    <?php endif; ?> 
                    <li class="nav-item">
                        <div class="dropdown language-switcher" style="display:inline-block;">
                            <button class="btn dropdown-toggle" style="background-color: #2DC3E7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?=SITE_URL;?>/assets/images/<?=$userLanguage;?>.png"> <?=$languages[$userLanguage];?>
                            </button>
                            <ul class="dropdown-menu" style="">
                                <?php foreach($languages as $code=>$name): if($code!=$userLanguage): ?>
                                    <li><a class="dropdown-item" href="<?=addParamToURL('lang',$code)?>"><img src="<?=SITE_URL;?>/assets/images/<?=$code;?>.png"> <?=$name;?></a></li>
                                <?php endif; endforeach; ?>
                            </ul>
                        </div>  
                    </li> 
                    <li class="text-center mt-3"> 
                    <div class="mobile-menu-logo text-center">
                        <a class="" href="https://mejorcadadia.com">
                            <img src="<?=SITE_URL;?>/users/assets/logo.png" alt="image" width="100px">
                        </a>                        
                    </div>
                    <div class="brand-info-bar text-center mb-2">
                        <h1 class="" style="color:#FFF; font-size:20px;"><?=translate('El Club de la Gente Excepcional');?></h1>                      
                    </div>          
                            <h6 class="text-light"><?=translate('Comparte MCD con tus amigos');?></h6>                     
                                <div class="text-center">
                                    <a target="_blank" href="http://www.facebook.com/sharer.php?u=mejorcadadia.com" class="btn-facebook btn btn-social-icon"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                    <a target="_blank" href="http://twitter.com/share?text=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-twitter"><i class="fa fa-twitter " aria-hidden="true"></i></a>
                                    <a target="_blank" href="https://wa.me/?text=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                                    <a target="_blank" href="mailto:?subject=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com&body=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-email"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
                                    
                                </div>
                    </li>
                    <li class="text-center">
                        <div class="text-center mt-3 mb-3">
                            <button id="install-button" class="btn btn-warning mx-auto"><?=translate('Add To HomeScreen');?></button>
                        </div>
                    </li>
                    <li class="nav-item lastfooteritem">
                    
                    <div class="brand-info-bar text-center">
                       <h1 class="migualtitle"><?=translate('By Miguel De La Fuente');?>
                            <p class="text-center">+507 6445-1418</p>
                        </h1>
                    </div>
                    
                    </li> 

                </ul>
            </div>
        </div>


        <div class="container-fluid">
            <div class="row">

                <nav class="col-md-2 d-none d-md-block sidebar desktop-left-sidebar" style="top: 89px;position: inherit;">
                    <h1 style="color: #ffffff; font-size: 17px; text-align: center; background-color: #fdaf40; padding: 7px; margin: 0px;"><?=translate('Menu');?></h1>
                    <div class="sidebar-sticky bg-info" style="padding-top: 0px;width: 100%;">

                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            
                            
                            <?php  if($current_file_name=='mynotes.php'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="<?= SITE_URL; ?>/users/dailygoals.php"><i class="fa fa-arrow-left"></i> <?=translate('Atrás');?></a>
                                </li>
                                <li class="nav-item dropdown">
                                <a class="nav-link" href="#" role="button"><?=translate('MejorNotes');?></a>
                                <ul class="submenu my-notes-menu" id="mynotesmenu">
                                    <li class="nav-item folder-item" id="folder-0">
                                        <a class="nav-link" href="mynotes.php" role="button">
                                        <i class="fa fa-list me-3"></i>
                                        <?=translate('Mis Notas');?>
                                        <span data-count="<?=$my_notes_count;?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$my_notes_count;?></span>
                                        </a>
                                    </li>
                                    <?php foreach($userFolders as $folder): ?>
                                        <li class="nav-item folder-item" id="folder-<?=$folder['id']; ?>">
                                        <a class="nav-link " href="mynotes.php?folder_id=<?=$folder['id']; ?>" role="button"><i class="fa fa-folder-o me-3"></i><?=$folder['name']; ?>  <span data-count="<?=$folder['notes_count'];?>" class="badge rounded-pill pull-right bg-light text-dark"><?=$folder['notes_count'];?></span> </a>
                                    </li>
                                    <?php endforeach; ?>
                                    <li class="nav-item create-folder-nav" id="create_folder_nav_item">
                                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#createFolderModal" role="button"><i class="fa fa-folder-open me-3"></i><?=translate('Carpeta');?></a> 
                                    </li>                                    

                                </ul>
                            </li>  
                            <?php else: ?>
                                <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="<?= SITE_URL; ?>"><i class="fa fa-home" aria-hidden="true"></i> <?=translate('Home');?></a>
                                </li>
                                
                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#menuMejorjournal" id="mejorjournalLinkItem" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="menuMejorjournal"><i class="fa fa-tasks" aria-hidden="true"></i> <?=translate('ExitoTotal Journal');?></a>
                                <ul class="submenu list-unstyled fw-normal pb-1 small collapse hide pb-1 ms-3" id="menuMejorjournal" aria-labelledby="mejorjournalLinkItem">
                        
                                
                                <li class="nav-item"><a class="nav-link" href="dailygoals.php" role="button">
                                <i class="fa fa-diamond" aria-hidden="true"></i> <?=translate('Victoria7');?></a> </li>
                                    
                                    <li class="nav-item">
                                        <a class="nav-link" href="cronovida.php" role="button">
                                        <i class="fa fa-clock-o"></i> <?=translate('CronoVida');?>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="capsules.php" role="button"><i class="fa fa-flask"></i> <?=translate('MejorCapsules');?></a> </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="dailycommitments.php" role="button">
                                        <i class="fa fa-hand-rock-o"></i> <?=translate('Guerrero Diario');?>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#SuperObjetivos" id="navbarDropdownsupergoals" role="button" data-bs-toggle="collapse" aria-expanded="false" aria-controls="SuperObjetivos">
                                        <i class="fa fa-bullseye"></i> <?=translate('SuperObjetivos');?>
                                        </a>
                                        <ul id="SuperObjetivos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdownsupergoals" style="margin-left:1rem;">
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php"><?=translate('Semanal');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly"><?=translate('Mensual');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly"><?=translate('Trimestral');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly"><?=translate('Anual');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime"><?=translate('100Dreams');?></a>
                                            </li>
                                            <!-- New Pages -->
                                            <br />
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/missions.php"><?=translate('Mi Missión');?></a>
                                            </li>
                                            
                                            <li class="nav-item">
                                                <a class="nav-link dropdown-toggle" href="#mivisions" id="mivisionsDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                                <?=translate('Mi Visión');?>
                                                </a>
                                                <ul id="mivisions" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="mivisionsDropdown" style="margin-left:1rem;">
                                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=3"><?=translate('Visión 3-Años');?></a></li>
                                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=5"><?=translate('Visión 5-Años');?></a></li>
                                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/vision.php?plan=10"><?=translate('Visión 10-Años');?></a></li>
                                                    
                                                </ul>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/commitments.php"><?=translate('Mis Compromisos');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/agreements.php"><?=translate('Mis Acuerdos');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/promises.php"><?=translate('Mis Promesas');?></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="<?= SITE_URL; ?>/users/lifeTasks.php"><?=translate('Mi tarea de Vida');?></a>
                                            </li>
                                        </ul>
                                    </li>

                                    <li>
                                        <a class="nav-link dropdown-toggle" href="#superMemorias" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                        <i class="fa fa-trophy"></i> <?=translate('SuperVictorias');?>
                                        </a>
                                        <ul id="superMemorias" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dailyVictories.php"><?=translate('Mi Victoria Diaria');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/toRemember.php"><?=translate('Eventos para Recordar');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/biggestVictories.php"><?=translate('Mis Mayores Victorias');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/superdias.php"><?=translate('SuperDias');?></a></li>
                                            
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="supermemories.php"><i class="fa fa-tree" aria-hidden="true"></i> <?=translate('SuperMemorias');?></a>
                            </li>
                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#dentletter" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                        <i class="fa fa-envelope-o"></i> <?=translate('Cartas para la Eternidad');?>
                                        </a>
                                        <ul id="dentletter" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link <?= $path == 'index.php' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/index.php" id="navbarDropdown"><?=translate('Cartas');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/notebook.php"><?=translate('Escribe Carta');?></a></li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                <a class="nav-link dropdown-toggle" href="#Imagenes" id="navbarDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                <i class="fa fa-picture-o"></i> <?=translate('Imagenes');?>
                                </a>
                                <ul id="Imagenes" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="navbarDropdown" style="margin-left:1rem;">
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/victory-images.php"><?=translate('Gallery');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/dream-wall.php"><?=translate('Dream wall');?></a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/photo-drive.php"><?=translate('Imagenes de Exito');?></a></li>
                                </ul>
                            </li>
                                    <li class="nav-item"><a class="nav-link" aria-current="page" href="<?= SITE_URL; ?>/users/victory-media.php?type=audio"><i class="fa fa-volume-up"></i> <?=translate('Audios');?></a></li>
                                    <li class="nav-item">
                                        <a class="nav-link dropdown-toggle" href="#mivideos" id="mivideosDropdown" role="button" data-bs-toggle="collapse" aria-expanded="false">
                                        <i class="fa fa-video-camera" aria-hidden="true"></i> <?=translate('Videos');?>
                                        </a>
                                        <ul id="mivideos" class="list-unstyled fw-normal pb-1 small collapse hide" aria-labelledby="mivideosDropdown" style="margin-left:1rem;">
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/video-playlists.php"><?=translate('Upload Video');?></a></li>
                                            <li class="nav-item"><a class="nav-link" href="<?= SITE_URL; ?>/users/victory-media.php?type=video"><?=translate('Victory 7 Videos');?></a></li>                                           
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                    <a class="nav-link" aria-current="page" href="mynotes.php"><i class="fa fa-sticky-note-o"></i> <?=translate('MejorNotes');?></a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://blog.mejorcadadia.com"><i class="fa fa-newspaper-o" aria-hidden="true"></i> <?=translate('MejorBlog');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/inspirations.php"><i class="fa fa-bar-chart" aria-hidden="true"></i> <?=translate('MejorInspiration');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fa fa-coffee" aria-hidden="true"></i> <?=translate('MejorCadaDía Chef');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fa fa-bed" aria-hidden="true"></i> <?=translate('MejorCadaDía Hotel');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"><i class="fa fa-fire" aria-hidden="true"></i> <?=translate('MejorFest');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/backup.php"><i class="fa fa-cloud-upload" aria-hidden="true"></i> <?=translate('DropBox Backup');?></a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/profile.php"><i class="fa fa-user-o" aria-hidden="true"></i> <?=translate('Perfil');?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= SITE_URL; ?>/users/logout.php" onclick="return confirm('<?=translate('Are you sure to logout?');?>');"><i class="fa fa-power-off" aria-hidden="true"></i> <?=translate('Salir');?></a>
                            </li>  
                            <?php endif; ?>
                            
                            <li class="nav-item">
                                <div class="dropdown language-switcher" style="display:inline-block;">
                                    <button class="btn dropdown-toggle" style="background-color: #2DC3E7;" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?=SITE_URL;?>/assets/images/<?=$userLanguage;?>.png"> <?=$languages[$userLanguage];?>
                                    </button>
                                    <ul class="dropdown-menu" style="">
                                        <?php foreach($languages as $code=>$name): if($code!=$userLanguage): ?>
                                            <li><a class="dropdown-item" href="<?=addParamToURL('lang',$code)?>"><img src="<?=SITE_URL;?>/assets/images/<?=$code;?>.png"> <?=$name;?></a></li>
                                        <?php endif; endforeach; ?>
                                    </ul>
                                </div>  
                            </li> 
                       
                           
                    
                            <li class="text-center mt-3">  
                            <div class="mobile-menu-logo text-center">
                                <a class="" href="https://mejorcadadia.com">
                                    <img src="<?=SITE_URL;?>/users/assets/logo.png" alt="image" width="100px">
                                </a>                        
                            </div>
                            <div class="brand-info-bar text-center mb-2">
                                <h1 class="" style="color:#FFF; font-size:20px;"><?=translate('El Club de la Gente Excepcional');?></h1>                      
                            </div>       
                            <h6 class="text-light"><?=translate('Comparte MCD con tus amigos');?></h6>                                  
                                <div class="text-center">
                                    <a target="_blank" href="http://www.facebook.com/sharer.php?u=mejorcadadia.com" class="btn-facebook btn btn-social-icon"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                    <a target="_blank" href="http://twitter.com/share?text=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-twitter"><i class="fa fa-twitter " aria-hidden="true"></i></a>
                                    <a target="_blank" href="https://wa.me/?text=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>
                                    <a target="_blank" href="mailto:?subject=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com&body=Este Mundo Necesita tu Mejor Versión y por eso te invitamos a que formes parte de la familia de mejorcadadia.com" class="btn btn-social-icon btn-email"><i class="fa fa-envelope-o" aria-hidden="true"></i></a>
                                    
                                </div>
                            </li>
                            <li class="text-center">
                                <div class="text-center mt-3 mb-3">
                                    <button id="install-button" class="btn btn-warning mx-auto"><?=translate('Add To HomeScreen.');?></button>
                                </div>
                            </li>


                        </ul>

                    </div>
                </nav>
