<?php require_once "inc/header.php"; ?>
<?php
//Session::checkLogin();
?>

	<img src="assets/images/5-mejorcadadia-welcome-negro-better.png" alt="bg-image" class="bg_image">
	<section class="home-main">
      
    <!-- Button trigger modal -->
    <div class="nav" style=" padding-left:20px; display: flex;align-items: center;justify-content: space-between;">
        
    <div class="nav-brand desktop-only"><img class="desktop" src="assets/images/mcdf-01.png" alt="logo"></div>
            <div class="nav-brand mobile-only"><a class="mobile-blog-btn" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Blog</a></div>
            <div class="responsive_nav">
                <div class="responsive_view_text">
                    <div class="res_logo">
                        <img class="mobile" src="assets/images/mcdf-01.png" alt="logo">
                        <img class="desktop" src="assets/images/logo.png" alt="logo">
                    </div>
                </div>
                <a class="blog-btn desktop-only" href="https://blog.mejorcadadia.com/" style="background-color: #FF007A; display:inline-block;">Blog</a>
              	<?php
              	if (Session::get('login') == false) {
                ?>
              	<button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#loginModel"
                    style="background-color: #1e01ff;">
                    Accede
                </button>
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#registration"
                    style="background-color: #e60023;">
                    Regístrate
                </button>
              	<?php
                }
              	?>
                <?php
            if (Session::get('login') == true) {
            ?>
      		<a href="https://mejorcadadia.com/users/dailygoals.php" class="profile" title="profile" style="display:inline-block;">
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
                    <form id="email_check_part">
                        <div class="form-group">
                            <label class="fw-bold">Email</label>
                            <input type="email" id="reg_email" placeholder="Enter Email">
                            <div id="error_success_msg_reg_email" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="fw-bold">Password</label>
                            <input type="password" id="reg_password" placeholder="Enter password">
                            <div id="error_success_msg_reg_password" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <label class="fw-bold">Age</label>
                            <input type="number" id="reg_age" placeholder="Enter age">
                            <div id="error_success_msg_reg_age" class="msg d-none"></div>
                        </div>
                        <div class="form-group mt-3">
                            <input class="mb-3" id="email_registration" type="button" value="Continue">
                        </div>
                    </form>

                    <div id="email_verification_part" style="display: none; width: 270px; margin: auto;">
                        <h5 class="text-center text-muted mb-4">Enter verification code</h5>
                        <input class="form-control mb-3" type="text" id="code" placeholder="Enter code">
                        <div id="error_success_msg_verification" class="msg d-none"></div>
                        <button class="btn btn-primary w-100" id="email_verification_login">Verify</button>
                        <br/>
                        <br/>
                    </div>

                    <h6>or</h6>

                    <div onclick="fbLogin();">
                        <img src="./assets/images/facebook-login.png"
                            style="width: 270px; cursor: pointer; height: 40px; border-radius: 20px;">
                    </div>

                    <div id="buttonDiv"></div>
                  <span style="display: block;text-align: center; font-size: 13px; width: 259px;margin: 0 auto;">
                            By continuing, you agree to Mejorcadadia's <a href="https://mejorcadadia.com/terms-and-conditions.php">Terms of Service</a> and acknowledge you've
                            read our <a href="https://mejorcadadia.com/privacy-policy.php">Privacy Policy</a>
                        </span>
                </div>

            </div>
        </div>
    </div>

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
                    </form>
                    <h6>or</h6>
                    <div onclick="fbLogin();">
                        <img src="./assets/images/facebook-login.png"
                            style="width: 270px; cursor: pointer; height: 40px; border-radius: 20px;">
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
            if(windowHeight<0){
                
                document.querySelector(".bg_image").style.height = innerHeight+"px";
                document.querySelector(".bg_image").style.objectFit = "cover";

            }else{
                document.querySelector(".bg_image").style.height = "auto";
                document.querySelector(".bg_image").style.objectFit = "contain";
            }
        }
        setEqualHeight();
        window.onresize = setEqualHeight;
</script>
    




<?php require_once "inc/footer.php"; ?>