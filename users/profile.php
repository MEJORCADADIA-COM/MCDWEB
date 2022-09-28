<?php require_once "inc/header.php"; ?>

<link rel="stylesheet" href="./assets/styleOne.css">

<style>
      @media screen and (max-width: 480px) {
        .maincontonent {
            width: 100%;
          	padding-top: 0%;
            height: 100vh;
        }
        .user_name {
        	font-size: 30px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            margin-top: 10px !important;
        }
        
        .id {
        	font-size: 24px !important;
            font-weight: 400 !important;
            font-family: 'Abel' !important;
            display: block !important;
            padding-top: 10px !important;
        }
        .description {
        	font-size: 22px !important;
            font-weight: 400 !important;
            width: 380px !important;
            max-width: 100% !important;
            text-align: center !important;
    		font-family: 'Abel' !important;
            margin-bottom: 14px !important;
        }
      }
      @media screen and (min-width: 600px) {
        .maincontonent {
            width: 100%;
          	padding-top: 0%;
            height: 100vh;
        }
        .user_name {
        	font-size: 30px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            margin-top: 10px !important;
        }
        
        .id {
        	font-size: 24px !important;
            font-weight: 400 !important;
            font-family: 'Abel' !important;
            display: block !important;
            padding-top: 10px !important;
        }
        .description {
        	font-size: 22px !important;
            font-weight: 400 !important;
            width: 380px !important;
            max-width: 100% !important;
            text-align: center !important;
    		font-family: 'Abel' !important;
            margin-bottom: 14px !important;
        }
      }
      @media screen and (min-width: 786px) {
        .maincontonent {
            width: 83%;
    		padding-top: 0%;
            height: auto;
        }
        .user_name {
        	font-size: 36px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            margin-top: 10px !important;
        }
        
        .id {
        	font-size: 13px !important;
            font-weight: 400 !important;
            font-family: 'Abel' !important;
            display: block !important;
            padding-top: 10px !important;
        }
        .description {
        	font-size: 16px !important;
            font-weight: 400 !important;
            width: 380px !important;
            max-width: 100% !important;
            text-align: center !important;
    		font-family: 'Abel' !important;
            margin-bottom: 0px !important;
        }
      }
      @media screen and (min-width: 992px) {
        .maincontonent {
            width: 83%;
    		padding-top: 0%;
            height: auto;
        }
        .user_name {
        	font-size: 36px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            margin-top: 10px !important;
        }
        
        .id {
        	font-size: 13px !important;
            font-weight: 400 !important;
            font-family: 'Abel' !important;
            display: block !important;
            padding-top: 10px !important;
        }
        .description {
        	font-size: 16px !important;
            font-weight: 400 !important;
            width: 380px !important;
            max-width: 100% !important;
            text-align: center !important;
    		font-family: 'Abel' !important;
          	margin-bottom: 0px !important;
        }
      }
      @media screen and (min-width: 1200px) {
        .maincontonent {
            width: 83%;
    		padding-top: 0%;
            height: auto;
        }
        .user_name {
        	font-size: 36px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            margin-top: 10px !important;
        }
        
        .id {
        	font-size: 13px !important;
            font-weight: 400 !important;
            font-family: 'Abel' !important;
            display: block !important;
            padding-top: 10px !important;
        }
        .description {
        	font-size: 16px;
            font-weight: 400 !important;
            width: 380px !important;
            max-width: 100% !important;
            text-align: center !important;
    		font-family: 'Abel' !important;
          	margin-bottom: 0px !important;
        }
      }

    </style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 maincontonent" style="margin-top: 0rem!important;margin-bottom: 0rem!important;">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
                <div style="padding: 15px">
                  <div id="Profile">
                    <img class="profile_image" src="<?= $user_infos['image'] != NULL ? $user_infos['image'] : 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>" />
                    <h3 style="color: #ffffff;" class="user_name"><?= $user_infos['full_name']; ?></h3>
                    <span style="color: #ffffff;" class="id"><?= $user_infos['gmail']; ?></span>
                    
                    <p style="color: #ffffff;" class="description"><?= $user_infos['description']; ?></p>

                    <a href="https://mejorcadadia.com/users/edit-profile.php" class="profile_edit_btn">Edit profile</a>
                  </div>
                </div>
            </div>
        </div>
</main>

<?php require_once "inc/footer.php"; ?>