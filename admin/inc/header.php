<?php
ob_start();
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../../lib/Database.php');
include_once ($filepath . '/../../lib/Session.php');
include_once ($filepath . '/../../helper/Format.php');
include_once ($filepath . '/../../classes/Common.php');
Session::adminSession();
$db = new Database();
$fm = new Format();
$common = new Common();

if (Session::get('admin_id') !== NULL) {
	$admin_id = Session::get('admin_id');
    $admin_info = $common->select("`admin`", "`id` = '$admin_id'");
    if ($admin_info) {
      $admin_infos = mysqli_fetch_assoc($admin_info);
    }
}

if(isset($_GET['admin_logout'])) {
	Session::adminDestroy();
}

$path = explode('/', $_SERVER['PHP_SELF']);
$path = end($path);

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
    <style>
      @media screen and (max-width: 480px) {
        .adminnav {
            display: flex;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: space-between;
        }

        .buttonmargin {
            border-bottom: 1px solid #2c394a;
            border-radius: 0px;
            margin-right: 0px !important;
            margin-left: 0px !important;
        }
      }
      @media screen and (min-width: 600px) {
        .adminnav {
            display: flex;
            flex-wrap: nowrap;
            align-content: center;
            justify-content: space-between;
        }

        .buttonmargin {
            border-bottom: 1px solid #2c394a;
            border-radius: 0px;
            margin-right: 0px !important;
            margin-left: 0px !important;
        }
      }
    </style>
</head>
<body>
  <section class="admin-dashbord">
        <nav class="navbar navbar-dark sticky-top flex-md-nowrap pb-2 adminnav">
            <a class="navbar-brand col-sm-3 col-md-2 mr-0 py-0" href="https://mejorcadadia.com/admin/">
                <img src="https://mejorcadadia.com/assets/images/logo.png" alt="image" width="100px">
            </a>
            <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search" style="opacity: 0; visibility: hidden;">
            <!-- Example single danger button -->
            <div class="btn-group me-3 d-flex align-items-center">
              <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                Admin
              </button>
              <ul class="dropdown-menu" style="left: auto; right: 0;">
                <li><a class="dropdown-item" href="https://mejorcadadia.com/admin/profile.php">Edit Profile</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="https://mejorcadadia.com/admin/?admin_logout" onclick="return confirm('Are you sure to logout?');">Logout</a></li>
              </ul>
            </div>
        </nav>
        <div class="btn-group me-3 d-flex align-items-center buttonmargin res_nav_item">
            <a href="https://mejorcadadia.com/admin/" class="res_nav_item">Dashboard</a>
            <a href="https://mejorcadadia.com/admin/users.php" class="res_nav_item">Users</a>
            
            <li class="nav-item dropdown res_nav_item">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;">
                    <i class="zmdi zmdi-widgets"></i>
                    NoteBook
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: rgb(255 255 255); padding: 0rem 0.5rem; width: 100%;border: 0px;">
                    <li><a style="border-left: 5px solid transparent; color: #000000; padding: 0.2rem 0.2rem; font-size: 14px;" class="dropdown-item" href="https://mejorcadadia.com/admin/notebook.php">NoteBook Add</a></li>
                    <li><a style="border-left: 5px solid transparent; color: #000000; padding: 0.2rem 0.2rem; font-size: 14px;" class="dropdown-item" href="https://mejorcadadia.com/admin/notebookview.php">NoteBook View</a></li>
                </ul>
            </li>
        </div>
        <div class="container-fluid">
            <div class="row">
                <nav class="col-md-2 d-none d-md-block sidebar">
                    <div class="sidebar-sticky">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link<?= $path == 'index.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/admin/">
                                    <i class="zmdi zmdi-widgets"></i>
                                    Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?= $path == 'users.php' ? ' active' : ''; ?>" href="https://mejorcadadia.com/admin/users.php">
                                    <i class="zmdi zmdi-widgets"></i>
                                    Users
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 0.5rem 0.75rem 0.2rem 0.75rem;">
                                    <i class="zmdi zmdi-widgets"></i>
                                    NoteBook
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color: rgb(27, 36, 49); padding: 0rem 2.75rem; width: 100%;border: 0px;">
                                    <li><a style="border-left: 5px solid transparent; color: #738297; padding: 0.2rem 0.75rem; font-size: 14px;" class="dropdown-item" href="https://mejorcadadia.com/admin/notebook.php">NoteBook Add</a></li>
                                    <li><a style="border-left: 5px solid transparent; color: #738297; padding: 0.2rem 0.75rem; font-size: 14px;" class="dropdown-item" href="https://mejorcadadia.com/admin/notebookview.php">NoteBook View</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>