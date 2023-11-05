<?php require_once "inc/header.php"; ?>

<?php
Session::checkSession();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $pin = $fm->validation($_POST['pin']);
    $confirm_pin = $fm->validation($_POST['confirm_pin']);
    if(empty($pin) || empty($confirm_pin)){
        $update_msg = '<div class="alert alert-danger mb-0">Please enter PIN Code.</div>';
    }elseif($pin!=$confirm_pin){
        $update_msg = '<div class="alert alert-danger mb-0">PIN and confirm PIN not matched.</div>';
    }else{
        $result = $common->update(
            table: "users",
                data: ["pin" => $pin],
                cond: "id = :id",
                params: ['id' => $user_id],
                modifiedColumnName: 'updated_at'
        );
        if ($result) {
            //header("Location: " . SITE_URL . "/users/account-pin.php");
            $update_msg = '<div class="alert alert-success mb-0">'.translate('PIN ha sido Actualizado Exitosamente').'.</div>';
        } else {
            $update_msg = '<div class="alert alert-danger mb-0">Something is wrong!</div>';
        }
    }
}


?>
<link rel="stylesheet" href="./assets/styleOne.css">
<style>
    @media screen and (max-width: 480px) {
        .inputform {
            width: 100%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 600px) {
        .inputform {
            width: 100%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto
        }
    }

    @media screen and (min-width: 786px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 992px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }

    @media screen and (min-width: 1200px) {
        .inputform {
            width: 85%;
            padding-top: 8% !important;
            padding-left: 8% !important;
            height: auto;
        }
    }
    
    
    .change-profile-group{
       width:auto;
    }
</style>


<main role="main" class="col-md-12 ml-sm-auto col-lg-12 my-3 text-white">
    <form id="change-profile-form" class="inputform" action="" method="POST" enctype="multipart/form-data">
      
        <div class="change-profile-group">
            <div class="preview_image">
                <img src="<?= $user_infos['image'] != NULL ? $user_infos['image'] : 'https://s3-us-west-2.amazonaws.com/harriscarney/images/150x150.png'; ?>" name="image" alt="profile image" id="profile-preview-image">
                <div class="px-2"><?= $user_infos['full_name'] ?></div>
            </div>
            <div class="alert alert-custom p-0" role="alert">
                <p><?=translate('Por favor ingresa tu PIN para recuperar tu cuenta en caso de que hayas Olvidado tu Contraseña o si has usado una cuenta de redes sociales a la que no puedes acceder. El proceso de verificación de PIN te permite acceder a una cuenta de manera segura. No compartas este PIN con nadie. El PIN es la manera de reestablecer tu cuenta.'); ?></p>
            </div>

        </div>
        
       
        <?php if(!empty($update_msg)) { echo $update_msg; } ?>
        <div class="change-profile-group">
            <label for="pin"><?=translate('PIN');?></label>
            <input class="from-control" id="pin" type="password" name="pin" value="" required>
        </div>
        <div class="change-profile-group">
            <label for="confirm_pin"><?=translate('Confirma tu PIN');?></label>
            <input class="from-control" id="confirm_pin" type="password" name="confirm_pin" value="" required>
        </div>
        <div class="btn-group mt-3">
            <a href="https://mejorcadadia.com/users/profile.php" class="profile_edit_btn bg-danger text-light me-2"><?=translate('Cancelar');?></a>
            <input type="submit" id="submit-new-details" name="update_profile" value="<?=translate('Actualizar');?>">
        </div>

    </form>
</main>

<?php require_once "inc/footer.php"; ?>