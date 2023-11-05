<?php
require_once "inc/header.php";

require_once base_path('/users/services/Email.php');

use Users\services\Email;
?>

<?php
$userPromises = $common->first('promises', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_promises'])) {
    $promise1 = $fm->validation($_POST['promise_1']);
    $promise2 = $fm->validation($_POST['promise_2']);
    $promise3 = $fm->validation($_POST['promise_3']);
    $promise4 = $fm->validation($_POST['promise_4']);
    $promise5 = $fm->validation($_POST['promise_5']);

    try {
        if ($userPromises) {
            $common->update(
                'promises',
                ['promise_1' => $promise1, 'promise_2' => $promise2, 'promise_3' => $promise3, 'promise_4' => $promise4, 'promise_5' => $promise5],
                'id = :id',
                ['id' => $userPromises['id']]
            );
        } else {
            $common->insert(
                'promises',
                ['user_id' => $user_infos['id'], 'promise_1' => $promise1, 'promise_2' => $promise2, 'promise_3' => $promise3, 'promise_4' => $promise4, 'promise_5' => $promise5]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Promises saved successfully!');


    header("Location: " . SITE_URL . "/users/promises.php");
    return;
}

if (isset($_POST['send_email'])) {
    $promise1 = $fm->validation($_POST['promise_1']);
    $promise2 = $fm->validation($_POST['promise_2']);
    $promise3 = $fm->validation($_POST['promise_3']);
    $promise4 = $fm->validation($_POST['promise_4']);
    $promise5 = $fm->validation($_POST['promise_5']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/promises.php');
        return;
    }

    if (empty($promise1) && empty($promise2) && empty($promise3) && empty($promise4) && empty($promise5)) {
        setError('Promises can not be empty');
        redirect('users/promises.php');
        return;
    }

    if (!empty($promise1)) {
        $promises = "<h4>".translate('Promesa #1 que me Hago a mí Mismo/a').":</h4><div>".html_entity_decode($promise1)."</div>";
    }
    if (!empty($promise2)) {
        $promises .= "<h4>".translate('Promesa #2 que me Hago a mí Mismo/a').":</h4><div>".html_entity_decode($promise2)."</div>";
    }
    if (!empty($promise3)) {
        $promises .= "<h4>".translate('Promesa #3 que me Hago a mí Mismo/a').":</h4><div>".html_entity_decode($promise3)."</div>";
    }
    if (!empty($promise4)) {
        $promises .= "<h4>".translate('Promesa #4 que me Hago a mí Mismo/a').":</h4><div>".html_entity_decode($promise4)."</div>";
    }
    if (!empty($promise5)) {
        $promises .= "<h4>".translate('Promesa #5 que me Hago a mí Mismo/a').":</h4><div>".html_entity_decode($promise5)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>".translate('Promesas Inquebrantables que Me Hago a Mí Mismo/a')."/a</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$promises}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send(translate('Promesas Inquebrantables que Me Hago a Mí Mismo/a'), $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/promises.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;"><?=translate('Promesas Inquebrantables que Me Hago a Mí Mismo/a'); ?></h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="promise_1"><?=translate('Promesa #1 que me Hago a mí Mismo/a'); ?>:</label>
                <textarea name="promise_1" id="promise_1" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userPromises['promise_1'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="promise_2"><?=translate('Promesa #2 que me Hago a mí Mismo/a'); ?>:</label>
                <textarea name="promise_2" id="promise_2" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userPromises['promise_2'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="promise_3"><?=translate('Promesa #3 que me Hago a mí Mismo/a'); ?>:</label>
                <textarea name="promise_3" id="promise_3" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userPromises['promise_3'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="promise_4"><?=translate('Promesa #4 que me Hago a mí Mismo/a'); ?>:</label>
                <textarea name="promise_4" id="promise_4" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userPromises['promise_4'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="promise_5"><?=translate('Promesa #5 que me Hago a mí Mismo/a'); ?>:</label>
                <textarea name="promise_5" id="promise_5" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userPromises['promise_5'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button"><?=translate('Email'); ?></button>
            <button class="btn btn-info letter" type="submit" name="save_promises"><?=translate('Save'); ?></button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="<?=translate('Guardar pdf'); ?>" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_promises"><i class="fa fa-save fa-lg"></i></button>
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
                                <input style="width:100%;" type="email" class="form-control" name="to_email" id="toEmail" placeHolder="<?=translate('Enter Email Address'); ?>">
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