<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;
?>

<?php
$userCommitments = $common->first('commitments', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_commitments'])) {
    $commitment1 = $fm->validation($_POST['commitment_1']);
    $commitment2 = $fm->validation($_POST['commitment_2']);
    $commitment3 = $fm->validation($_POST['commitment_3']);
    $commitment4 = $fm->validation($_POST['commitment_4']);
    $commitment5 = $fm->validation($_POST['commitment_5']);

    try {
        if ($userCommitments) {
            $common->update(
                'commitments',
                ['commitment_1' => $commitment1, 'commitment_2' => $commitment2, 'commitment_3' => $commitment3, 'commitment_4' => $commitment4, 'commitment_5' => $commitment5],
                'id = :id',
                ['id' => $userCommitments['id']]
            );
        } else {
            $common->insert(
                'commitments',
                [
                    'user_id' => $user_infos['id'],
                    'commitment_1' => $commitment1,
                    'commitment_2' => $commitment2,
                    'commitment_3' => $commitment3,
                    'commitment_4' => $commitment4,
                    'commitment_5' => $commitment5
                ]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Commitments saved successfully!');

    header("Location: " . SITE_URL . "/users/commitments.php");
    return;
}

if (isset($_POST['send_email'])) {
    $commitment1 = $fm->validation($_POST['commitment_1']);
    $commitment2 = $fm->validation($_POST['commitment_2']);
    $commitment3 = $fm->validation($_POST['commitment_3']);
    $commitment4 = $fm->validation($_POST['commitment_4']);
    $commitment5 = $fm->validation($_POST['commitment_5']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/commitments.php');
        return;
    }

    if (empty($commitment1) && empty($commitment2) && empty($commitment3) && empty($commitment4) && empty($commitment5)) {
        setError('Commitments can not be empty');
        redirect('users/commitments.php');
        return;
    }

    if (!empty($commitment1)) {
        $commitments = "<h4>Compromiso #1 que Hago conmigo:</h4><div>".html_entity_decode($commitment1)."</div>";
    }
    if (!empty($commitment2)) {
        $commitments .= "<h4>Compromiso #2 que Hago conmigo:</h4><div>".html_entity_decode($commitment2)."</div>";
    }
    if (!empty($commitment3)) {
        $commitments .= "<h4>Compromiso #3 que Hago conmigo:</h4><div>".html_entity_decode($commitment3)."</div>";
    }
    if (!empty($commitment4)) {
        $commitments .= "<h4>Compromiso #4 que Hago conmigo:</h4><div>".html_entity_decode($commitment4)."</div>";
    }
    if (!empty($commitment5)) {
        $commitments .= "<h4>Compromiso #5 que Hago conmigo:</h4><div>".html_entity_decode($commitment5)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>Compromisos Conmigo Mismo/a</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$commitments}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send('Compromisos Conmigo Mismo/a', $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/commitments.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Compromisos Conmigo Mismo/a:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="commitment-1">Compromiso #1 que Hago conmigo:</label>
                <textarea name="commitment_1" id="commitment-1" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userCommitments['commitment_1'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="commitment-2">Compromiso #2 que Hago conmigo:</label>
                <textarea name="commitment_2" id="commitment-2" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userCommitments['commitment_2'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="commitment-3">Compromiso #3 que Hago conmigo:</label>
                <textarea name="commitment_3" id="commitment-3" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userCommitments['commitment_3'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="commitment-4">Compromiso #4 que Hago conmigo:</label>
                <textarea name="commitment_4" id="commitment-4" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userCommitments['commitment_4'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="commitment-5">Compromiso #5 que Hago conmigo:</label>
                <textarea name="commitment_5" id="commitment-5" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userCommitments['commitment_5'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>
            <button class="btn btn-info letter" type="submit" name="save_commitments">Guardar</button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_commitments"><i class="fa fa-save fa-lg"></i></button>
            <!-- Floating Button End -->
            <!-- Modal -->

            <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark" id="exampleModalToggleLabel">Send Email</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Receiver Email Address</label>
                                <input style="width:100%;" type="email" class="form-control" name="to_email" id="toEmail" placeHolder="Enter Email Address">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div id="modal-msg"></div>
                            <button class="btn btn-primary" type="submit" id="sendBtn" name="send_email">Send Email</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="<?= SITE_URL ?>/admin/assets/jquery-3.6.0.min.js"></script>
<script src="<?= SITE_URL ?>/admin/assets/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= SITE_URL ?>/admin/assets/tinymce-jquery.min.js"></script>
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
    tinymce.init({
        selector: '.tinymce-editor',
        height: 600,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'autoresize',
            'autosave', 'codesample', 'directionality', 'emoticons', 'importcss',
            'nonbreaking', 'pagebreak', 'quickbars', 'save', 'template', 'visualchars'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help' +
            'anchor | restoredraft | ' +
            'charmap | code | codesample | ' +
            'ltr rtl | emoticons | fullscreen | ' +
            'image | importcss | insertdatetime | ' +
            'link | numlist bullist | media | nonbreaking | ' +
            'pagebreak | preview | save | searchreplace | ' +
            'table tabledelete | tableprops tablerowprops tablecellprops | ' +
            'tableinsertrowbefore tableinsertrowafter tabledeleterow | ' +
            'tableinsertcolbefore tableinsertcolafter tabledeletecol | ' +
            'template | visualblocks | visualchars | wordcount | undo redo | ' +
            'blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });

    $('#savePrintBtn').click(function() {
        window.print();
    });
</script>

<?php require_once "inc/footer.php"; ?>