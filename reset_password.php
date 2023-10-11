<?php
$code=isset($_GET['code'])? trim($_GET['code']):'';
require_once "inc/header.php";
require_once "inc/inspirationQuote.php";
$has_valid_account=false;
$user_info=null;
if(!empty($code)){
    $email = trim(Session::get($code));
    if(!empty($email)){
        $user_info = $common->first("`users`", "`gmail` = :email", ['email' => $email]);        
        $has_valid_account=true;
    }
    
}

$errors='';
if(empty($user_info)){
    $errors='Password Reset link has been expired.';
}
$password_updated=false;
if(isset($_POST['reset_password']) && $_POST['reset_password']=='Submit'){
    $password=isset($_POST['password'])? trim($_POST['password']):'';
    $confirm_password=isset($_POST['confirm_password'])? trim($_POST['confirm_password']):'';
    if(strlen($password)>5){
        if($password==$confirm_password){
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $common->update('users', ['password' => $password_hash], 'id = :id', ['id' => $user_info['id']], false);
            $password_updated=true;
        }else{
            $errors='Password and confirm password not matched.';  
        }
    }else{
        $errors='Password length must be at least 6';
    }
}


//Session::checkLogin();
?>
<style>
     @media screen and (max-width: 767px) {
        .reset-form-wrapper{
            width:80%;
        }
     }
</style>
<img src="assets/images/5-mejorcadadia-welcome-negro-better.png" alt="bg-image" class="bg_image">
<section class="home-main">

    <!-- Button trigger modal -->
    <div class="nav" style=" padding-left:20px; display: flex;align-items: center;justify-content: space-between;">

        <div class="nav-brand desktop-only"><img class="desktop" src="assets/images/mcdf-01.png" alt="logo">
        </div>
        <div class="nav-brand mobile-only"><a class="mobile-blog-btn" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Blog</a></div>
        <div class="responsive_nav">
            <div class="responsive_view_text">
                <div class="res_logo">
                    <img class="mobile" src="assets/images/mcdf-01.png" alt="logo">
                    <img class="desktop" src="assets/images/logo.png" alt="logo">
                </div>
            </div>
            <a class="blog-btn desktop-only" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Blog</a>
            <div class="card reset-form-wrapper position-fixed top-50 start-50 translate-middle" style="">
                <div class="card-body">
                    <h3>Restablecer su contraseña</h3>
                    <?php if($password_updated==true): ?>
                        <div class="alert alert-success" role="alert">
                        Tu Contraseña ha sido Actualizada. Por Favor, Accede a tu Cuenta. 
                            </div>
                            <div class="text-center">
                                <a class="btn btn-primary" href="<?=SITE_URL;?>">Accede</a>
                            </div>
                    <?php else: ?>
                        <?php if(!empty($user_info)): ?>
                            <form id="email_check_part" method="post">
                            <div class="form-group">
                                <label class="fw-bold">New Password</label>
                                <input type="password"  name="password" id="password" placeholder="Escribe tu Nueva Contraseña" required>
                                <div class="msg d-none"></div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="fw-bold">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Escribe Contraseña">
                                <div  class="msg d-none"></div>
                            </div>
                            <div class="form-group mt-3">
                                <input class="mb-3" type="submit" name="reset_password" value="Enviar">
                            </div>
                        </form>
                        <?php else: ?>
                            <div class="alert alert-danger" role="alert">
                                <?=$errors;?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    
                    
                </div>
            </div>
           
        </div>

    </div>
    

</section>

<section>
    <!-- Inspiration Qoute Capsule -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

        .quote-text {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.2rem;
        }
    </style>

    <?php
    $inspirationQuote = getInspirationQuote();
    if (!empty($inspirationQuote)) :
    ?>
        <div class="navbar px-3 py-2 quote-text d-flex justify-content-center text-white bg-dark">
            <?= htmlspecialchars_decode($inspirationQuote) ?>
        </div>
    <?php endif; ?>
    <!-- Inspiration Qoute Capsul-->
    <nav class="navbar footer-navbar navbar-dark flex-md-nowrap pb-2 px-3" style="background-color: #fef200;">
        <h6 class="footertitleleft">Mejorcadadia.com</h6>
        <h6 class="footertitlerigth">All rights reserved 2022</h6>
    </nav>
</section>



<script>
    
    function getScrollMaxY() {
        "use strict";
        var innerh = window.innerHeight || ebody.clientHeight,
            yWithScroll = 0;

        if (window.innerHeight && window.scrollMaxY) {
            // Firefox 
            yWithScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight) {
            // all but Explorer Mac 
            yWithScroll = document.body.scrollHeight;
        } else {
            // works in Explorer 6 Strict, Mozilla (not FF) and Safari 
            yWithScroll = document.body.offsetHeight;
        }
        return yWithScroll - innerh;
    }

    function setEqualHeight() {
        let windowHeight = getScrollMaxY()
        console.log(windowHeight);
        let innerHeight = window.innerHeight || ebody.clientHeight
        if (windowHeight < 0) {

            document.querySelector(".bg_image").style.height = innerHeight + "px";
            document.querySelector(".bg_image").style.objectFit = "cover";

        } else {
            document.querySelector(".bg_image").style.height = "auto";
            document.querySelector(".bg_image").style.objectFit = "contain";
        }
    }
    setEqualHeight();
    window.onresize = setEqualHeight;
</script>





<?php require_once "inc/footer.php"; ?>