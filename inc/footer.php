<!-- Error Messsage Modal -->
<div class="modal fade" id="error_success_msg_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mt-4" role="document" style="max-width: 350px; margin: auto;">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h2 class="modal-title text-center w-100 font-weight-bold" id="exampleModalLabel">Notice</h2>
            </div>
            <div class="modal-body">
                <div id="error_success_msg" class="msg">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-block" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>

</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
</script>

<?php if(Session::get('login')==false): ?>
<script src="https://www.instagram.com/embed.js"></script>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<?php endif; ?>

<script>
        var isLoggedIn="<?php echo Session::get('login'); ?>";
    console.log('isLoggedIn',isLoggedIn);
    function handleCredentialResponse(response) {
            console.log("Encoded JWT ID token: " + response.credential);
            console.log('handleCredentialResponse', response);
            let type = 'google';
            let profile = response.credential;
            phpSignIn(profile, type);
        }
        window.onGoogleLibraryLoad = () => {
            google.accounts.id.initialize({
            //client_id: "846740831076-qmsittms5rvsjes31a9fdqfrb3atigkl.apps.googleusercontent.com",
            client_id:"51609443177-jb3b6pl4onl6h54pnq11isn07bqhr563.apps.googleusercontent.com",
            callback: handleCredentialResponse,
            auto_select:true
          });
          var btnConfig={
            width:"375px",
            text:"continue_with",
          };
          const parent = document.getElementById('googleLoginBtnWrap');
          google.accounts.id.renderButton(parent, btnConfig);
          const parent2 = document.getElementById('googleRegisterBtnWrap');
          
          google.accounts.id.renderButton(parent2, btnConfig);
          google.accounts.id.prompt((notification) => {
            console.log(notification);
            if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
                // continue with another identity provider.
            }
            });
        };
    
    const clientID = '614656524100935';
    const redirectURI = encodeURI('https://mejorcadadia.com/');
    $(document).ready(function() {
        if(!isLoggedIn){
            setTimeout(function(){
            $('#newLoginModel').modal('show');
        }, 2000);
        }
        
        
    });

    function showRegisterFrom(){
        console.log('showRegisterFrom')
        $("#register-email-form").show();
        $("#modal-register-options").hide();
        $("#register-back-btn").show();
    }
    function showLoginFrom(){
        console.log('showLoginFrom')
        $("#login_email_check_part").show();
        $("#modal-login-options").hide();
        $("#login-back-btn").show();
    }

    // facebook api login start
    window.fbAsyncInit = function() {
        console.log('fbAsyncInit');
        // FB JavaScript SDK configuration and setup
        FB.init({
           // appId: '1072087170094337', // FB App ID
            appId:'964650648118248',
            cookie: false, // enable cookies to allow the server to access the session
            xfbml: true, // parse social plugins on this page
            version: 'v14.0' // use graph api version 2.8
        });
        
       
    };

    // Load the JavaScript SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Facebook login with JavaScript SDK
    function tiktokLogin(){
        var screenX = typeof window.screenX !== 'undefined' ? window.screenX : window.screenLeft,
                screenY = typeof window.screenY !== 'undefined' ? window.screenY : window.screenTop,
                clientWidth = typeof window.outerWidth !== 'undefined' ? window.outerWidth : document.documentElement.clientWidth,
                clientHeight = typeof window.outerHeight !== 'undefined' ? window.outerHeight : (document.documentElement.clientHeight - 22),
                popupWidth = 400,
                popupHeight = 600,
                screenWidth = (screenX < 0) ? window.screen.width + screenX : screenX,
                popupX = parseInt(screenWidth + ((clientWidth - popupWidth) / 2), 10),
                popupY = parseInt(screenY + ((clientHeight - popupHeight) / 2.5), 10),
                popupFeatures = ('width=' + popupWidth + ',height=' + popupHeight + ',left=' + popupX + ',top=' + popupY + ',scrollbars=1,location=1,toolbar=0');
       
        const csrfState = Math.random().toString(36).substring(2);
        let url = 'https://www.tiktok.com/v2/auth/authorize/';

        // the following params need to be in `application/x-www-form-urlencoded` format.
        url += '?client_key=aw6swkmkq3chsy78';
        url += '&scope=user.info.basic';
        url += '&response_type=token';
        url += '&redirect_uri='+redirectURI;
        url += '&state=' + csrfState;
        const popup = window.open(
            url,
        'TikTokLogin',
        popupFeatures
        );

    }
    function instaLogin(){
       

        var redURI = encodeURI('https://mejorcadadia.com/');
        var screenX = typeof window.screenX !== 'undefined' ? window.screenX : window.screenLeft,
                screenY = typeof window.screenY !== 'undefined' ? window.screenY : window.screenTop,
                clientWidth = typeof window.outerWidth !== 'undefined' ? window.outerWidth : document.documentElement.clientWidth,
                clientHeight = typeof window.outerHeight !== 'undefined' ? window.outerHeight : (document.documentElement.clientHeight - 22),
                popupWidth = 400,
                popupHeight = 600,
                screenWidth = (screenX < 0) ? window.screen.width + screenX : screenX,
                popupX = parseInt(screenWidth + ((clientWidth - popupWidth) / 2), 10),
                popupY = parseInt(screenY + ((clientHeight - popupHeight) / 2.5), 10),
                popupFeatures = ('width=' + popupWidth + ',height=' + popupHeight + ',left=' + popupX + ',top=' + popupY + ',scrollbars=1,location=1,toolbar=0');
        const popup = window.open(
        `https://api.instagram.com/oauth/authorize?client_id=${clientID}&redirect_uri=${redURI}&response_type=code&scope=user_profile,user_media`,
        'InstagramLogin',
        popupFeatures
        );
        // Check for changes in the popup window's location (URL)
    const interval = setInterval(() => {
      try {
        // If the user has successfully logged in, the URL will include the access token
        if (popup.location.href.indexOf(redirectURI) === 0) {
          clearInterval(interval);
          //
            console.log(popup.location);
            popup.close();
            const urlParams = new URLSearchParams(popup.location.search);
                const accessToken = urlParams.get('code');
                console.log('accessToken',accessToken);
                phpSignIn(accessToken, 'instagram');
          
          

        }
      } catch (error) {
        // Ignore security errors due to cross-origin access restrictions
      }
    }, 100);
    }
    function fbLogin() {
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                getFbUserData();
            } else {
                FB.login(function(response) {
                    console.log('FBLOGIN',response);
                    if (response.authResponse) {
                        getFbUserData();
                    }
                }, {
                    scope: 'email,public_profile'
                });
            }
        });
        
    }

    // Fetch the user profile data from facebook
    function getFbUserData() {
       
        FB.api('/me', {
                locale: 'en_US',
                fields: 'id,first_name,last_name,email,picture'
            },
            function(response) {
                console.log('getFbUserData',response);
                let type = 'facebook';
                let profile = response;               
               phpSignIn(profile, type);
            });
    }
    // facebook api login end

    // email registration start
    $('#email_registration').click(function() {
        var email = $('#reg_email').val();
        var password = $('#reg_password').val();
        var dob = $('#reg_age').val();
        let errors = 0;
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (email.match(mailformat)) {
            $("#error_success_msg_reg_email").hide();
        } else {
            $("#error_success_msg_reg_email").text('Email is invalid!');
            $("#error_success_msg_reg_email").removeAttr('class').addClass("msg msg_error w-100");
            $("#error_success_msg_reg_email").show();
        }
        if (password.length < 6) {
            $("#error_success_msg_reg_password").text('Minimum 6 characters!');
            $("#error_success_msg_reg_password").removeAttr('class').addClass("msg msg_error w-100");
            $("#error_success_msg_reg_password").show();
        } else {
            $("#error_success_msg_reg_password").hide();
        }
        if (dob.length == 0) {
            $("#error_success_msg_").text('DOB is required!');
            $("#error_success_msg_reg_age").removeAttr('class').addClass("msg msg_error w-100");
            $("#error_success_msg_reg_age").show();
            errors++;
        } else {
            $("#error_success_msg_reg_age").hide();
        }
        if (errors == 0) {
            $.ajax({
                url: SITE_URL + "/ajax/ajax.php",
                type: "POST",
                data: {
                    email_registration: 'email_registration',
                    email: email,
                    password:password,
                    dob:dob
                },
                success: function(data) {
                    if (data == 'sent') {
                        $('#email_check_part').hide();
                        $('#email_verification_part').show();
                    } else {
                        $("#error_success_msg_email").text('Account already exist!');
                        $("#error_success_msg_email").removeAttr('class').addClass("msg msg_error w-100");
                        $("#error_success_msg_email").show();
                    }
                }
            });
        }
    });
    // email registration end
    $('#forgot_panel').click(function() {
                $('#login_email_check_part').hide();
                $('#forgot-form').show();
                $("#login-back-btn").show();
        });
        $('#login_panel').click(function() {
              $('#forgot-form').hide();
              $('#login_email_check_part').show();
              $("#login-back-btn").show();
        });
        $('#login-back-btn').click(function() {
              $('#forgot-form').hide();
              $('#login_email_check_part').hide();
              $('#modal-login-options').show();
              $("#login-back-btn").hide();
        });
        $('#register-back-btn').click(function() {
            $("#register-email-form").hide();
        $("#modal-register-options").show();
        
              $("#register-back-btn").hide();
        });
        $('#forgot_password').click(function() {
            console.log('clicked');
            var email = $('#forgot_email').val();
            let login_errors = 0;
            var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
            if (email.match(mailformat)) {
                $("#forgot_error_success_msg_email").hide();
            } else {
                $("#forgot_error_success_msg_email").text('Email is invalid!');
                $("#forgot_error_success_msg_email").removeAttr('class').addClass("msg msg_error w-100");
                $("#forgot_error_success_msg_email").show();
                
                login_errors++;
            }
            if (login_errors == 0) {
                $('#forgot_email').val('');
                $.ajax({
                    url: SITE_URL + "/ajax/ajax.php",
                    type: "POST",
                    data: {email:email,'forgot_password':'forgot_password'},
                    success: function(data) {
                        $("#res-msgs").html(data);
                    }
                });
            }
        });
    // email login start
    $('#email_login').click(function() {
        var email = $('#login-email').val();
        var password = $('#login-password').val();

        let login_errors = 0;
        var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
        if (email.match(mailformat)) {
            $("#error_success_msg_email").hide();
        } else {
            $("#error_success_msg_email").text('Email is invalid!');
            $("#error_success_msg_email").removeAttr('class').addClass("msg msg_error w-100");
            $("#error_success_msg_email").show();
            login_errors++;
        }
        if (password.length < 6) {
            $("#error_success_msg_password").text('Password is required');
            $("#error_success_msg_password").removeAttr('class').addClass("msg msg_error w-100");
            $("#error_success_msg_password").show();
            login_errors++;
        } else {
            $("#error_success_msg_password").hide();
        }

        if (login_errors == 0) {
            let type = 'email';
            const profile = {
                type: type,
                gmail: email,
                password: password
            };
            phpSignIn(profile, type);
        }
    });
    // email login end

    function phpSignIn(profile, type) {
        if (type == 'google') {
            if (profile.length > 2) {
                formData = {
                    type: type,
                    credential: profile
                }
            }
        } else if (type == 'facebook') {
            if (profile.id.length > 2) {
                var full_name = profile.first_name + ' ' + profile.last_name;
                var picture = profile.picture.data.url;
                formData = {
                    type: type,
                    full_name: full_name,
                    gmail: profile.email,
                    facebook_id: profile.id,
                    image: picture
                }
            }
        } else if (type == 'email') {
            if (profile.gmail.length > 2) {
                formData = profile
            }
        }else if(type=='instagram'){
            formData = {
                    type: type,
                    credential: profile
                }
        }

        $.ajax({
            url: SITE_URL + "/ajax/ajax.php",
            type: "POST",
            data: formData,
            success: function(data) {
                if (type == 'facebook') {
                    FB.logout();
                } else if (type == 'google') {
                    google.accounts.id.disableAutoSelect();
                }

                if (data == 'logged_in') {
                    window.location.href = SITE_URL + '/users/dailygoals.php';
                } else if (data == 'sent') {
                    $('#email_check_part').hide();
                    $('#email_verification_part').show();
                } else {
                    if (type == 'email') {
                        if (data == 'Password does not match!') {
                            $("#error_success_msg_password").text(data);
                            $("#error_success_msg_password").removeAttr('class').addClass(
                                "msg msg_error mb-3 w-100");
                            $("#error_success_msg_password").show();
                        } else {
                            $("#error_success_msg_email").text(data);
                            $("#error_success_msg_email").removeAttr('class').addClass(
                                "msg msg_error mb-3 w-100");
                            $("#error_success_msg_email").show();
                        }
                    } else {
                        $("#error_success_msg").text(data);
                        $("#error_success_msg").removeAttr('class').addClass(
                            "msg msg_error mb-3 w-100");
                        $("#error_success_msg_modal").modal('show');
                    }
                }
            }
        });
    }

    $('#email_verification_login').click(function() {
        var email = $('#reg_email').val();
        var password = $('#reg_password').val();
        var dob = $('#reg_age').val();
        var code = $('#code').val();
        if (email.length !== 0) {
            if (code.length !== 0) {
                $.ajax({
                    url: SITE_URL + "/ajax/ajax.php",
                    type: "POST",
                    data: {
                        email_verification_login: 'email_verification_login',
                        email: email,
                        password: password,
                        dob: dob,
                        code: code
                    },
                    success: function(data) {
                        if (data == 'logged_in') {
                            window.location.href = SITE_URL + '/users/dailygoals.php';
                        } else {
                            $("#error_success_msg_verification").text(data);
                            $("#error_success_msg_verification").removeAttr('class').addClass(
                                "msg msg_error mb-3");
                        }
                    }
                });
            } else {
                $("#error_success_msg_verification").text('Verification code is required!');
                $("#error_success_msg_verification").removeAttr('class').addClass("msg msg_error mb-3");
            }
        } else {
            $("#error_success_msg_verification").text('Email is required!');
            $("#error_success_msg_verification").removeAttr('class').addClass("msg msg_error mb-3");
        }
    });
</script>
</body>

</html>