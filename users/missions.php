<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;

?>

<?php
$userMissions = $common->first('missions', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_missions'])) {
    $mission1 = $fm->validation($_POST['mission_1']);
    $mission2 = $fm->validation($_POST['mission_2']);
    $mission3 = $fm->validation($_POST['mission_3']);

    try {
        if ($userMissions) {
            $common->update(
                'missions',
                ['mission_1' => $mission1, 'mission_2' => $mission2, 'mission_3' => $mission3],
                'id = :id',
                ['id' => $userMissions['id']]
            );
        } else {
            $common->insert(
                'missions',
                ['user_id' => $user_infos['id'], 'mission_1' => $mission1, 'mission_2' => $mission2, 'mission_3' => $mission3]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Missions saved successfully!');


    header("Location: " . SITE_URL . "/users/missions.php");
    return;
}

if (isset($_POST['send_email'])) {
    $mission1 = $fm->validation($_POST['mission_1']);
    $mission2 = $fm->validation($_POST['mission_2']);
    $mission3 = $fm->validation($_POST['mission_3']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/missions.php');
        return;
    }

    if (empty($mission1) && empty($mission2) && empty($mission3)) {
        setError('Missions can not be empty');
        redirect('users/missions.php');
        return;
    }

    if (!empty($mission1)) {
        $missions = "<h4>".translate('Misión #1').":</h4><div>".html_entity_decode($mission1)."</div>";
    }
    if (!empty($mission2)) {
        $missions .= "<h4>".translate('Misión')." #2:</h4><div>".html_entity_decode($mission2)."</div>";
    }
    if (!empty($mission3)) {
        $missions .= "<h4>".translate('Misión')." #3:</h4><div>".html_entity_decode($mission3)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>".translate('Misión o misiones para Mi Vida')."</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$missions}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send(translate('Misión o misiones para Mi Vida'), $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    header("Location: " . SITE_URL . "/users/missions.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;"><?=translate('Misión o misiones para Mi Vida'); ?>:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="mission_1"><?=translate('Misión'); ?> #1:</label>
                <textarea name="mission_1" id="mission_1" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userMissions['mission_1'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="mission_2"><?=translate('Misión'); ?> #2:</label>
                <textarea name="mission_2" id="mission_2" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userMissions['mission_2'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="mission_3"><?=translate('Misión'); ?> #3:</label>
                <textarea name="mission_3" id="mission_3" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userMissions['mission_3'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button"><?=translate('Email'); ?></button>
            <button class="btn btn-info letter" type="submit" name="save_missions"><?=translate('Guardar'); ?></button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="<?=translate('Guardar pdf'); ?>" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_missions"><i class="fa fa-save fa-lg"></i></button>
            <!-- Floating Button End -->

            <!-- Modal -->

            <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark" id="exampleModalToggleLabel"><?=translate('Send Email'); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label><?=translate('Receiver Email Address'); ?></label>
                                <input style="width:100%;" type="email" class="form-control" name="to_email" id="toEmail" placeHolder="<?=translate('Enter Email Address'); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="modal-msg"></div>
                            <button class="btn btn-primary" type="submit" id="sendBtn" name="send_email"><?=translate('Send Email'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="<?= SITE_URL ?>/admin/assets/jquery-3.6.0.min.js"></script>

<style>
    .tox-notifications-container {
        display: none !important;
    }

    .letter {
        float: right;
        margin: 15px 10px 15px 10px;
    }
</style>
<script>
document.querySelectorAll( '.ckeditor' ).forEach( ( node, index ) => {  
	ClassicEditor
	.create( node, {} )
	.then( newEditor => {      
      if(node.id){
        window.editors[ node.id ] = newEditor;
      }else{
        window.editors[ index ] = newEditor	;
      }			
	});
});
  

    $('#savePrintBtn').click(function() {
        window.print();
    });
</script>

<?php require_once "inc/footer.php"; ?>