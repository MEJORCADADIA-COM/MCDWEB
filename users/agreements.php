<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;
?>

<?php
$userAgreements = $common->first('agreements', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_agreements'])) {
    $agreement1 = $fm->validation($_POST['agreement_1']);
    $agreement2 = $fm->validation($_POST['agreement_2']);
    $agreement3 = $fm->validation($_POST['agreement_3']);
    $agreement4 = $fm->validation($_POST['agreement_4']);
    $agreement5 = $fm->validation($_POST['agreement_5']);

    try {
        if ($userAgreements) {
            $common->update(
                'agreements',
                ['agreement_1' => $agreement1, 'agreement_2' => $agreement2, 'agreement_3' => $agreement3, 'agreement_4' => $agreement4, 'agreement_5' => $agreement5],
                'id = :id',
                ['id' => $userAgreements['id']]
            );
        } else {
            $common->insert(
                'agreements',
                ['user_id' => $user_infos['id'], 'agreement_1' => $agreement1, 'agreement_2' => $agreement2, 'agreement_3' => $agreement3, 'agreement_4' => $agreement4, 'agreement_5' => $agreement5]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Agreements saved successfully!');


    header("Location: " . SITE_URL . "/users/agreements.php");
    return;
}

if (isset($_POST['send_email'])) {
    $agreement1 = $fm->validation($_POST['agreement_1']);
    $agreement2 = $fm->validation($_POST['agreement_2']);
    $agreement3 = $fm->validation($_POST['agreement_3']);
    $agreement4 = $fm->validation($_POST['agreement_4']);
    $agreement5 = $fm->validation($_POST['agreement_5']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/agreements.php');
        return;
    }

    if (empty($agreement1) && empty($agreement2) && empty($agreement3) && empty($agreement4) && empty($agreement5)) {
        setError('Agreements can not be empty');
        redirect('users/agreements.php');
        return;
    }

    if (!empty($agreement1)) {
        $agreements = "<h4>Acuerdo #1 que Hago conmigo:</h4><div>".html_entity_decode($agreement1)."</div>";
    }
    if (!empty($agreement2)) {
        $agreements .= "<h4>Acuerdo #2 que Hago conmigo:</h4><div>".html_entity_decode($agreement2)."</div>";
    }
    if (!empty($agreement3)) {
        $agreements .= "<h4>Acuerdo #3 que Hago conmigo:</h4><div>".html_entity_decode($agreement3)."</div>";
    }
    if (!empty($agreement4)) {
        $agreements .= "<h4>Acuerdo #4 que Hago conmigo:</h4><div>".html_entity_decode($agreement4)."</div>";
    }
    if (!empty($agreement5)) {
        $agreements .= "<h4>Acuerdo #5 que Hago conmigo:</h4><div>".html_entity_decode($agreement5)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>Acuerdos que Hago Conmigo Mismo/a</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$agreements}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send('Acuerdos que Hago Conmigo Mismo/a', $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/agreements.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Acuerdos que Hago Conmigo
                Mismo/a:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="agreement_1">Acuerdo #1 que Hago conmigo:</label>
                <textarea name="agreement_1" id="agreement_1" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userAgreements['agreement_1'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="agreement_2">Acuerdo #2 que Hago conmigo:</label>
                <textarea name="agreement_2" id="agreement_2" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userAgreements['agreement_2'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="agreement_3">Acuerdo #3 que Hago conmigo:</label>
                <textarea name="agreement_3" id="agreement_3" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userAgreements['agreement_3'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="agreement_4">Acuerdo #4 que Hago conmigo:</label>
                <textarea name="agreement_4" id="agreement_3" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userAgreements['agreement_4'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="agreement_5">Acuerdo #5 que Hago conmigo:</label>
                <textarea name="agreement_5" id="agreement_5" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userAgreements['agreement_5'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>
            <button class="btn btn-info letter" type="submit" name="save_agreements">Guardar</button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_agreements"><i class="fa fa-save fa-lg"></i></button>
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