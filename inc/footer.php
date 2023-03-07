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

<script src="https://accounts.google.com/gsi/client" async defer></script>

<script>
    $(document).ready(function() {
        $('#login-modal').modal('show');
    });
    // google api login start
    function handleCredentialResponse(response) {
        console.log('handleCredentialResponse', response);
        let type = 'google';
        let profile = response.credential;
        phpSignIn(profile, type);
    }

    window.onload = function() {
        google.accounts.id.initialize({
            client_id: "51609443177-jb3b6pl4onl6h54pnq11isn07bqhr563.apps.googleusercontent.com",
            callback: handleCredentialResponse
        });
        google.accounts.id.renderButton(
            document.getElementById("buttonDiv"), {
                theme: "outline",
                size: "large",
                width: "270",
                shape: "circle"
            } // customization attributes
        );
        google.accounts.id.renderButton(
            document.getElementById("registrationButtonDiv"), {
                theme: "outline",
                size: "large",
                width: "270",
                shape: "circle"
            } // customization attributes
        );
        google.accounts.id.prompt(); // also display the One Tap dialog
    }
    // google api login end


    // facebook api login start
    window.fbAsyncInit = function() {
        // FB JavaScript SDK configuration and setup
        FB.init({
            appId: '1072087170094337', // FB App ID
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
    function fbLogin() {
        FB.login(function(response) {
            if (response.authResponse) {
                getFbUserData();
            }
        }, {
            scope: 'email'
        });
    }

    // Fetch the user profile data from facebook
    function getFbUserData() {
        FB.api('/me', {
                locale: 'en_US',
                fields: 'id,first_name,last_name,email,picture'
            },
            function(response) {
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
        var age = $('#reg_age').val();
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
        if (age.length == 0) {
            $("#error_success_msg_reg_age").text('Age is required!');
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
                    email: email
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
        var age = $('#reg_age').val();
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
                        age: age,
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