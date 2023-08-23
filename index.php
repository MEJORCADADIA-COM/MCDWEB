<?php
require_once "inc/header.php";
require_once "inc/inspirationQuote.php";
?>
<?php
//Session::checkLogin();
?>

<img src="assets/images/5-mejorcadadia-welcome-negro-better.png" alt="bg-image" class="bg_image">
<section class="home-main">

    <!-- Button trigger modal -->
    <div class="nav" style=" padding-left:20px; display: flex;align-items: center;justify-content: space-between;">

        <div class="nav-brand desktop-only logo-icon"><img class="desktop" src="assets/images/mcdf-01.png" alt="logo">
        </div>
        <div class="nav-brand mobile-only ballon-menu">
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Guía</a>
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Fest</a>
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Chef</a>
            <a class="mobile-blog-btn ballon-item" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Hostels</a>
        </div>
        <div class="responsive_nav ballon-menu">
            <div class="responsive_view_text">
                <div class="res_logo">
                    <img class="mobile" src="assets/images/mcdf-01.png" alt="logo">
                    <img class="desktop" src="assets/images/logo.png" alt="logo">
                </div>
            </div>
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Guía</a>
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Fest</a>
            <a class="blog-btn desktop-only ballon-item" href="#" style="background-color: #FF007A; display:inline-block;">Chef</a>
            <a class="blog-btn desktop-only ballon-item" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Hostels</a>
            <?php
            if (Session::get('login') == false) {
            ?>
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#newLoginModel" style="background-color: #F0EA20; color:#000 !important;">
                    Accede
                </button>
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#registration" style="background-color: #2DC3E7;">
                    Regístrate
                </button>
            <?php
            }
            ?>
            <?php
            if (Session::get('login') == true) {
            ?>
                <a href="<?php echo SITE_URL; ?>/users/dailygoals.php" class="profile" title="profile" style="display:inline-block;">
                    <img src="https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png" alt="profile image">
                </a>
            <?php
            }
            ?>
        </div>

    </div>



    <!-- Modal -->
    <div class="modal fade" id="registration" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content" style="border-radius: 32px;">
                <div id="register-back-btn">
                    <svg class="tiktok-1i5fgpz-StyledChevronLeftOffset eg439om1" width="1em" data-e2e="" height="1em" viewBox="0 0 42 42" fill="#000000" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.58579 22.5858L20.8787 6.29289C21.2692 5.90237 21.9024 5.90237 22.2929 6.29289L23.7071 7.70711C24.0976 8.09763 24.0976 8.7308 23.7071 9.12132L8.82843 24L23.7071 38.8787C24.0976 39.2692 24.0976 39.9024 23.7071 40.2929L22.2929 41.7071C21.9024 42.0976 21.2692 42.0976 20.8787 41.7071L4.58579 25.4142C3.80474 24.6332 3.80474 23.3668 4.58579 22.5858Z"></path></svg>
                </div>  
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="assets/images/cross.svg" alt="cut icon">
                </button>
                <div class="modal-body text-center pt-0">
                    <div class="company-logo pt-3">
                        <a href="#">
                            <img src="assets/images/logo.png" alt="logo" style="width: 200px;">
                        </a>
                    </div>
                    <h3>Bienvenido a MejorCadaDía</h3>
                    <p>Rechaza los límites</p>
              
                    <div id="register-email-form">
                        <form id="email_check_part">
                            <div class="form-group">
                                <label class="fw-bold">Email</label>
                                <input type="email" id="reg_email" placeholder="Email de acceso">
                                <div id="error_success_msg_reg_email" class="msg d-none"></div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="fw-bold">Contraseña</label>
                                <input type="password" id="reg_password" placeholder="Introduce Contraseña">
                                <div id="error_success_msg_reg_password" class="msg d-none"></div>
                            </div>
                            <div class="form-group mt-3">
                                <label class="fw-bold">Fecha de Nacimiento</label>
                                <input type="date" id="reg_age" placeholder="Enter age">
                                <div id="error_success_msg_reg_age" class="msg d-none"></div>
                            </div>
                            <div class="form-group mt-3">
                                <input class="mb-3" id="email_registration" type="button" value="Continúa">
                            </div>
                        </form>
                        <div id="email_verification_part" style="display: none; width: 270px; margin: auto;">
                            <h5 class="text-center text-muted mb-4">Enter verification code</h5>
                            <input class="form-control mb-3" type="text" id="code" placeholder="Enter code">
                            <div id="error_success_msg_verification" class="msg d-none"></div>
                            <button class="btn btn-primary w-100" id="email_verification_login">Verify</button>
                            <br />
                            <br />
                        </div>
                    </div>
                    <div class="login-option-buttons my-4" id="modal-register-options">
                        <div class="alert alert-white d-flex align-items-center" role="alert" onclick="showRegisterFrom()">
                            <div class="icon-wrap">
                                <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24.0003 7C20.1343 7 17.0003 10.134 17.0003 14C17.0003 17.866 20.1343 21 24.0003 21C27.8663 21 31.0003 17.866 31.0003 14C31.0003 10.134 27.8663 7 24.0003 7ZM13.0003 14C13.0003 7.92487 17.9252 3 24.0003 3C30.0755 3 35.0003 7.92487 35.0003 14C35.0003 20.0751 30.0755 25 24.0003 25C17.9252 25 13.0003 20.0751 13.0003 14ZM24.0003 33C18.0615 33 13.0493 36.9841 11.4972 42.4262C11.3457 42.9573 10.8217 43.3088 10.2804 43.1989L8.32038 42.8011C7.77914 42.6912 7.4266 42.1618 7.5683 41.628C9.49821 34.358 16.1215 29 24.0003 29C31.8792 29 38.5025 34.358 40.4324 41.628C40.5741 42.1618 40.2215 42.6912 39.6803 42.8011L37.7203 43.1989C37.179 43.3088 36.6549 42.9573 36.5035 42.4262C34.9514 36.9841 29.9391 33 24.0003 33Z"></path></svg>
                            </div>
                            <div>
                            Regístrate con Email address
                            </div>
                        </div>
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="fbLogin();">
                            <div class="icon-wrap">
                            <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 47C36.7025 47 47 36.7025 47 24C47 11.2975 36.7025 1 24 1C11.2975 1 1 11.2975 1 24C1 36.7025 11.2975 47 24 47Z" fill="white"></path><path d="M24 1C11.2964 1 1 11.2964 1 24C1 35.4775 9.40298 44.9804 20.3846 46.7205L20.3936 30.6629H14.5151V24.009H20.3936C20.3936 24.009 20.3665 20.2223 20.3936 18.5363C20.4206 16.8503 20.7542 15.2274 21.6288 13.7487C22.9722 11.4586 25.0639 10.3407 27.6335 10.0251C29.7432 9.76362 31.826 10.0521 33.9087 10.3407C34.0529 10.3587 34.125 10.3767 34.2693 10.4038C34.2693 10.4038 34.2783 10.6472 34.2693 10.8005C34.2603 12.4053 34.2693 16.0839 34.2693 16.0839C33.2685 16.0659 31.6096 15.9667 30.5096 16.138C28.6884 16.4175 27.6425 17.5806 27.6064 19.4108C27.5704 20.8354 27.5884 24.009 27.5884 24.009H33.9988L32.962 30.6629H27.5974V46.7205C38.597 44.9984 47.009 35.4775 47.009 24C47 11.2964 36.7036 1 24 1Z" fill="#0075FA"></path></svg>
                            </div>
                            <div>
                            Continuar con Facebook
                            </div>
                        </div>
                        <div id="googleRegisterBtnWrap" class=""></div>
               
                      
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="instaLogin()">
                            <div class="icon-wrap">
                                <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 46C36.1503 46 46 36.1503 46 24C46 11.8497 36.1503 2 24 2C11.8497 2 2 11.8497 2 24C2 36.1503 11.8497 46 24 46Z" fill="url(#InstagramCircleColor_paint1_radial)"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24 46C36.1503 46 46 36.1503 46 24C46 11.8497 36.1503 2 24 2C11.8497 2 2 11.8497 2 24C2 36.1503 11.8497 46 24 46Z" fill="url(#InstagramCircleColor_paint1_radial)"></path><path d="M12.2689 29.0393L12.2683 29.0257L12.2674 29.0121C12.2581 28.8681 12.2467 28.7257 12.2361 28.5942L12.2357 28.5899C12.2259 28.4679 12.2168 28.3551 12.2093 28.2443V19.8627C12.21 19.8592 12.2108 19.8558 12.2115 19.8523C12.2258 19.7843 12.2503 19.6567 12.2525 19.5033C12.2783 18.5107 12.3298 17.6235 12.5372 16.7855C13.0067 14.9166 14.0499 13.622 15.7951 12.8635C16.7343 12.4582 17.7702 12.3191 18.9552 12.2691C19.2441 12.2596 19.5271 12.2332 19.7741 12.2093H28.1373C28.1408 12.21 28.1442 12.2108 28.1477 12.2115C28.2157 12.2258 28.3433 12.2503 28.4967 12.2525C29.4893 12.2783 30.3765 12.3298 31.2145 12.5372C33.0835 13.0068 34.3781 14.05 35.1366 15.7954C35.5419 16.7345 35.6809 17.7702 35.7309 18.9552C35.7404 19.244 35.7668 19.5271 35.7907 19.7741V28.1373C35.79 28.1408 35.7892 28.1442 35.7885 28.1477C35.7742 28.2157 35.7497 28.3433 35.7475 28.4967C35.7217 29.4894 35.6701 30.3768 35.4627 31.2149C34.9931 33.0836 33.9499 34.3781 32.2048 35.1366C31.2656 35.5418 30.2298 35.6809 29.0448 35.7309C28.756 35.7404 28.4729 35.7668 28.2259 35.7907H19.8627C19.8592 35.79 19.8558 35.7892 19.8523 35.7885C19.7843 35.7742 19.6567 35.7497 19.5033 35.7475C18.5106 35.7217 17.6232 35.6701 16.7851 35.4627C14.9165 34.9931 13.622 33.95 12.8635 32.205C12.4575 31.2643 12.3187 30.2267 12.2689 29.0393Z" stroke="white" stroke-width="2.4186"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24 28.207C26.3015 28.207 28.2068 26.327 28.2068 24.0508C28.2068 21.7071 26.3437 19.8017 24.0421 19.7933C21.69 19.7933 19.7931 21.6649 19.7931 23.9917C19.7931 26.3186 21.6731 28.207 24 28.207ZM30.5 24.0084C30.5 27.6083 27.583 30.5084 23.9831 30.5C20.4001 30.4916 17.5 27.583 17.5 23.9916C17.5 20.3917 20.417 17.4916 24.0169 17.5C27.5999 17.5084 30.5 20.417 30.5 24.0084Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M30.4921 15.3327C31.2981 15.3327 31.9446 15.9712 31.9446 16.7692C31.9446 17.5672 31.2981 18.2216 30.5001 18.2216C29.7101 18.2216 29.0557 17.5672 29.0557 16.7692C29.0636 15.9791 29.7101 15.3327 30.4921 15.3327Z" fill="white"></path><defs><radialGradient id="InstagramCircleColor_paint1_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(13.6876 49.3889) rotate(-90) scale(43.6073 40.5582)"><stop stop-color="#FFDD55"></stop><stop offset="0.1" stop-color="#FFDD55"></stop><stop offset="0.5" stop-color="#FF543E"></stop><stop offset="1" stop-color="#C837AB"></stop></radialGradient><radialGradient id="InstagramCircleColor_paint1_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(-5.37023 5.16969) rotate(78.6806) scale(19.4926 80.3494)"><stop stop-color="#3771C8"></stop><stop offset="0.128" stop-color="#3771C8"></stop><stop offset="1" stop-color="#6600FF" stop-opacity="0"></stop></radialGradient></defs></svg>
                            </div>
                            <div>
                            Continuar con Instagram
                            </div>
                        </div>
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="tiktokLogin()">
                            <div class="icon-wrap">
                            <svg width="1em" data-e2e="" height="1em" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16"> <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/> </svg>
                            </div>
                            <div>
                            Continuar con TikTok
                            </div>
                        </div>
                      
                    </div>
                    
                   
                    <span style="display: block;text-align: center; font-size: 13px; width: 259px;margin: 0 auto;">
                    Al continuar, aceptas los Términos de servicio de Mejorcadadia <a href="https://mejorcadadia.com/terms-and-conditions.php">Términos de servicio</a> Y reconoce que has leído nuestra <a href="https://mejorcadadia.com/privacy-policy.php">política de privacidad</a>
                    </span>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="newLoginModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content" style="border-radius: 32px;">
                <div id="login-back-btn">
                    <svg class="tiktok-1i5fgpz-StyledChevronLeftOffset eg439om1" width="1em" data-e2e="" height="1em" viewBox="0 0 42 42" fill="#000000" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.58579 22.5858L20.8787 6.29289C21.2692 5.90237 21.9024 5.90237 22.2929 6.29289L23.7071 7.70711C24.0976 8.09763 24.0976 8.7308 23.7071 9.12132L8.82843 24L23.7071 38.8787C24.0976 39.2692 24.0976 39.9024 23.7071 40.2929L22.2929 41.7071C21.9024 42.0976 21.2692 42.0976 20.8787 41.7071L4.58579 25.4142C3.80474 24.6332 3.80474 23.3668 4.58579 22.5858Z"></path></svg>
                </div>                
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="assets/images/cross.svg" alt="cut icon">
                </button>
                <div class="modal-body text-center pt-0">
                    <div class="company-logo pt-3">
                        <a href="#">
                            <img src="assets/images/logo.png" alt="logo" style="width: 200px;">
                        </a>
                    </div>
                    <h3>Bienvenido a MejorCadaDía</h3>
                    <p>Rechaza los límites</p>
                    <div id="login_email_check_part">
                    <form id="login_email_form">
                        <div class="form-group">
                            <label class="fw-bold">Email</label>
                            <input type="email" id="login-email" placeholder="Email de acceso">
                            <div id="error_success_msg_email" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="fw-bold">Contraseña</label>
                            <input type="password" id="login-password" placeholder="Introduce Contraseña">
                            <div id="error_success_msg_password" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <input class="mb-3" id="email_login" type="button" value="Continúa">
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" id="forgot_panel">Tu contraseña ¿No la recuerdas?</a>
                        </div>
                    </form>
                    </div>
                    <div class="forgot-form" id="forgot-form" style="display: <?= isset($forgot_error_msg) ? 'block' : 'none'; ?>;">
                    <h2>Has olvidado tu contraseña</h2>
                    <div id="res-msgs" class="mx-2 my-2"></div>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label class="fw-bold">Email address<span>*</span></label>
                            <input type="email" id="forgot_email" name="forgot_email" placeholder="Email de acceso">
                            <div id="forgot_error_success_msg_email" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <input class="mb-3" id="forgot_password" type="button" value="Send">
                        </div>
                        <div class="form-group">
                        <a href="javascript:void(0);" id="login_panel">Login</a>
                        </div>
                    </form>
                    </div>
                    <div class="login-option-buttons my-4" id="modal-login-options">
                        <div class="alert alert-white d-flex align-items-center" role="alert" onclick="showLoginFrom()">
                            <div class="icon-wrap">
                                <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24.0003 7C20.1343 7 17.0003 10.134 17.0003 14C17.0003 17.866 20.1343 21 24.0003 21C27.8663 21 31.0003 17.866 31.0003 14C31.0003 10.134 27.8663 7 24.0003 7ZM13.0003 14C13.0003 7.92487 17.9252 3 24.0003 3C30.0755 3 35.0003 7.92487 35.0003 14C35.0003 20.0751 30.0755 25 24.0003 25C17.9252 25 13.0003 20.0751 13.0003 14ZM24.0003 33C18.0615 33 13.0493 36.9841 11.4972 42.4262C11.3457 42.9573 10.8217 43.3088 10.2804 43.1989L8.32038 42.8011C7.77914 42.6912 7.4266 42.1618 7.5683 41.628C9.49821 34.358 16.1215 29 24.0003 29C31.8792 29 38.5025 34.358 40.4324 41.628C40.5741 42.1618 40.2215 42.6912 39.6803 42.8011L37.7203 43.1989C37.179 43.3088 36.6549 42.9573 36.5035 42.4262C34.9514 36.9841 29.9391 33 24.0003 33Z"></path></svg>
                            </div>
                            <div>
                            Accede con Email
                            </div>
                        </div>
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="fbLogin();">
                            <div class="icon-wrap">
                            <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M24 47C36.7025 47 47 36.7025 47 24C47 11.2975 36.7025 1 24 1C11.2975 1 1 11.2975 1 24C1 36.7025 11.2975 47 24 47Z" fill="white"></path><path d="M24 1C11.2964 1 1 11.2964 1 24C1 35.4775 9.40298 44.9804 20.3846 46.7205L20.3936 30.6629H14.5151V24.009H20.3936C20.3936 24.009 20.3665 20.2223 20.3936 18.5363C20.4206 16.8503 20.7542 15.2274 21.6288 13.7487C22.9722 11.4586 25.0639 10.3407 27.6335 10.0251C29.7432 9.76362 31.826 10.0521 33.9087 10.3407C34.0529 10.3587 34.125 10.3767 34.2693 10.4038C34.2693 10.4038 34.2783 10.6472 34.2693 10.8005C34.2603 12.4053 34.2693 16.0839 34.2693 16.0839C33.2685 16.0659 31.6096 15.9667 30.5096 16.138C28.6884 16.4175 27.6425 17.5806 27.6064 19.4108C27.5704 20.8354 27.5884 24.009 27.5884 24.009H33.9988L32.962 30.6629H27.5974V46.7205C38.597 44.9984 47.009 35.4775 47.009 24C47 11.2964 36.7036 1 24 1Z" fill="#0075FA"></path></svg>
                            </div>
                            <div>
                            Continuar con Facebook
                            </div>
                        </div>
                        <div id="googleLoginBtnWrap" class=""></div>
               
                       <!-- <div class="alert alert-white d-flex align-items-center" role="button" onclick="handleGoogleLogin()">
                            <div class="icon-wrap">
                                <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M43 24.4313C43 23.084 42.8767 21.7885 42.6475 20.5449H24.3877V27.8945H34.8219C34.3724 30.2695 33.0065 32.2818 30.9532 33.6291V38.3964H37.2189C40.885 35.0886 43 30.2177 43 24.4313Z" fill="#4285F4"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24.3872 43.001C29.6219 43.001 34.0107 41.2996 37.2184 38.3978L30.9527 33.6305C29.2165 34.7705 26.9958 35.4441 24.3872 35.4441C19.3375 35.4441 15.0633 32.1018 13.5388 27.6108H7.06152V32.5337C10.2517 38.7433 16.8082 43.001 24.3872 43.001Z" fill="#34A853"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M13.5395 27.6094C13.1516 26.4695 12.9313 25.2517 12.9313 23.9994C12.9313 22.7472 13.1516 21.5295 13.5395 20.3894V15.4668H7.06217C5.74911 18.0318 5 20.9336 5 23.9994C5 27.0654 5.74911 29.9673 7.06217 32.5323L13.5395 27.6094Z" fill="#FBBC04"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24.3872 12.5568C27.2336 12.5568 29.7894 13.5155 31.7987 15.3982L37.3595 9.94866C34.0018 6.88281 29.6131 5 24.3872 5C16.8082 5 10.2517 9.25777 7.06152 15.4674L13.5388 20.39C15.0633 15.8991 19.3375 12.5568 24.3872 12.5568Z" fill="#EA4335"></path></svg>
                            </div>
                            <div>
                            Continue with Google
                            </div>
                        </div>-->
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="instaLogin()">
                            <div class="icon-wrap">
                        <svg width="1em" data-e2e="" height="1em" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24 46C36.1503 46 46 36.1503 46 24C46 11.8497 36.1503 2 24 2C11.8497 2 2 11.8497 2 24C2 36.1503 11.8497 46 24 46Z" fill="url(#InstagramCircleColor_paint0_radial)"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24 46C36.1503 46 46 36.1503 46 24C46 11.8497 36.1503 2 24 2C11.8497 2 2 11.8497 2 24C2 36.1503 11.8497 46 24 46Z" fill="url(#InstagramCircleColor_paint1_radial)"></path><path d="M12.2689 29.0393L12.2683 29.0257L12.2674 29.0121C12.2581 28.8681 12.2467 28.7257 12.2361 28.5942L12.2357 28.5899C12.2259 28.4679 12.2168 28.3551 12.2093 28.2443V19.8627C12.21 19.8592 12.2108 19.8558 12.2115 19.8523C12.2258 19.7843 12.2503 19.6567 12.2525 19.5033C12.2783 18.5107 12.3298 17.6235 12.5372 16.7855C13.0067 14.9166 14.0499 13.622 15.7951 12.8635C16.7343 12.4582 17.7702 12.3191 18.9552 12.2691C19.2441 12.2596 19.5271 12.2332 19.7741 12.2093H28.1373C28.1408 12.21 28.1442 12.2108 28.1477 12.2115C28.2157 12.2258 28.3433 12.2503 28.4967 12.2525C29.4893 12.2783 30.3765 12.3298 31.2145 12.5372C33.0835 13.0068 34.3781 14.05 35.1366 15.7954C35.5419 16.7345 35.6809 17.7702 35.7309 18.9552C35.7404 19.244 35.7668 19.5271 35.7907 19.7741V28.1373C35.79 28.1408 35.7892 28.1442 35.7885 28.1477C35.7742 28.2157 35.7497 28.3433 35.7475 28.4967C35.7217 29.4894 35.6701 30.3768 35.4627 31.2149C34.9931 33.0836 33.9499 34.3781 32.2048 35.1366C31.2656 35.5418 30.2298 35.6809 29.0448 35.7309C28.756 35.7404 28.4729 35.7668 28.2259 35.7907H19.8627C19.8592 35.79 19.8558 35.7892 19.8523 35.7885C19.7843 35.7742 19.6567 35.7497 19.5033 35.7475C18.5106 35.7217 17.6232 35.6701 16.7851 35.4627C14.9165 34.9931 13.622 33.95 12.8635 32.205C12.4575 31.2643 12.3187 30.2267 12.2689 29.0393Z" stroke="white" stroke-width="2.4186"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M24 28.207C26.3015 28.207 28.2068 26.327 28.2068 24.0508C28.2068 21.7071 26.3437 19.8017 24.0421 19.7933C21.69 19.7933 19.7931 21.6649 19.7931 23.9917C19.7931 26.3186 21.6731 28.207 24 28.207ZM30.5 24.0084C30.5 27.6083 27.583 30.5084 23.9831 30.5C20.4001 30.4916 17.5 27.583 17.5 23.9916C17.5 20.3917 20.417 17.4916 24.0169 17.5C27.5999 17.5084 30.5 20.417 30.5 24.0084Z" fill="white"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M30.4921 15.3327C31.2981 15.3327 31.9446 15.9712 31.9446 16.7692C31.9446 17.5672 31.2981 18.2216 30.5001 18.2216C29.7101 18.2216 29.0557 17.5672 29.0557 16.7692C29.0636 15.9791 29.7101 15.3327 30.4921 15.3327Z" fill="white"></path><defs><radialGradient id="InstagramCircleColor_paint0_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(13.6876 49.3889) rotate(-90) scale(43.6073 40.5582)"><stop stop-color="#FFDD55"></stop><stop offset="0.1" stop-color="#FFDD55"></stop><stop offset="0.5" stop-color="#FF543E"></stop><stop offset="1" stop-color="#C837AB"></stop></radialGradient><radialGradient id="InstagramCircleColor_paint1_radial" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(-5.37023 5.16969) rotate(78.6806) scale(19.4926 80.3494)"><stop stop-color="#3771C8"></stop><stop offset="0.128" stop-color="#3771C8"></stop><stop offset="1" stop-color="#6600FF" stop-opacity="0"></stop></radialGradient></defs></svg>
                            </div>
                            <div>
                            Continuar con Instagram
                            </div>
                        </div>
                        <div class="alert alert-white d-flex align-items-center" role="button" onclick="tiktokLogin()">
                            <div class="icon-wrap">
                            <svg width="1em" data-e2e="" height="1em" xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-tiktok" viewBox="0 0 16 16"> <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/> </svg>
                            </div>
                            <div>
                            Continuar con TikTok
                            </div>
                        </div>
                      
                    </div>
                    
                    

                    
                    <span style="display: block;text-align: center; font-size: 13px; width: 259px;margin: 0 auto;">
                    Al continuar, aceptas los Términos de servicio de Mejorcadadia <a href="https://mejorcadadia.com/terms-and-conditions.php">Términos de servicio</a> Y reconoce que has leído nuestra <a href="https://mejorcadadia.com/privacy-policy.php">política de privacidad</a>
                    </span>
                </div>

            </div>
        </div>
    </div>
    <?php if(Session::get('login')==false):?>
    <div id="g_id_onload"
     data-client_id="846740831076-qmsittms5rvsjes31a9fdqfrb3atigkl.apps.googleusercontent.com"
     data-context="signin"
     data-ux_mode="popup"
     data-callback="handleCredentialResponse"
     data-nonce=""
     data-auto_select="true"
     data-itp_support="true">
</div>
<?php endif; ?>
    <!-- Modal -->       
    <div class="modal fade" id="loginModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content" style="border-radius: 32px;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <img src="assets/images/cross.svg" alt="cut icon">
                </button>
                <div class="modal-body text-center pt-0">
                    <div class="company-logo pt-3">
                        <a href="#">
                            <img src="assets/images/logo.png" alt="logo" style="width: 200px;">
                        </a>
                    </div>
                    <h3>Bienvenido a MejorCadaDía</h3>
                    <p>Rechaza los límites</p>
                    <form id="login_email_check_part">
                        <div class="form-group">
                            <label class="fw-bold">Email</label>
                            <input type="email" id="login-email" placeholder="Enter Email">
                            <div id="error_success_msg_email" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="fw-bold">Password</label>
                            <input type="password" id="login-password" placeholder="Enter password">
                            <div id="error_success_msg_password" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <input class="mb-3" id="email_login" type="button" value="Continue">
                        </div>
                        <div class="form-group">
                            <a href="javascript:void(0);" id="forgot_panel">¿Contraseña olvidada?</a>
                        </div>
                    </form>
                    <div class="forgot-form" id="forgot-form" style="display: <?= isset($forgot_error_msg) ? 'block' : 'none'; ?>;">
                    <h2>Forgot password</h2>
                    <div id="res-msgs" class="mx-2 my-2"></div>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label class="fw-bold">Email address<span>*</span></label>
                            <input type="email" id="forgot_email" name="forgot_email" placeholder="Enter Email">
                            <div id="forgot_error_success_msg_email" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <input class="mb-3" id="forgot_password" type="button" value="Send">
                        </div>
                        <div class="form-group">
                        <a href="javascript:void(0);" id="login_panel">Login</a>
                        </div>
                    </form>
                    </div>
                    <h6>or</h6>
                    <div onclick="fbLogin();">
                        <img src="./assets/images/facebook-login.png" style="width: 270px; cursor: pointer; height: 40px; border-radius: 20px;">
                    </div>

                    <div id="registrationButtonDiv"></div>
                    <span style="display: block;text-align: center; font-size: 13px; width: 259px;margin: 0 auto;">
                        By continuing, you agree to Mejorcadadia's <a href="https://mejorcadadia.com/terms-and-conditions.php">Terms of Service</a> and acknowledge you've
                        read our <a href="https://mejorcadadia.com/privacy-policy.php">Privacy Policy</a>
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal -->

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