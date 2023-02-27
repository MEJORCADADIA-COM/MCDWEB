<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;

?>

<?php
$userVisions = $common->first('visions', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_vision'])) {
    $vision3Years = $fm->validation($_POST['next_3_years_vision']);
    $vision5Years = $fm->validation($_POST['next_5_years_vision']);
    $vision10Years = $fm->validation($_POST['next_10_years_vision']);

    try {
        if ($userVisions) {
            $common->update(
                'visions',
                ['next_3_years_vision' => $vision3Years, 'next_5_years_vision' => $vision5Years, 'next_10_years_vision' => $vision10Years],
                'id = :id',
                ['id' => $userVisions['id']]
            );
        } else {
            $common->insert(
                'visions',
                ['user_id' => $user_infos['id'], 'next_3_years_vision' => $vision3Years, 'next_5_years_vision' => $vision5Years, 'next_10_years_vision' => $vision10Years]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Visions saved successfully!');


    header("Location: " . SITE_URL . "/users/vision.php");
    return;
}

if (isset($_POST['send_email'])) {
    $vision3Years = $fm->validation($_POST['next_3_years_vision']);
    $vision5Years = $fm->validation($_POST['next_5_years_vision']);
    $vision10Years = $fm->validation($_POST['next_10_years_vision']);

    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/vision.php');
        return;
    }

    if (empty($vision3Years) && empty($vision5Years) && empty($vision10Years)) {
        setError('Visions can not be empty');
        redirect('users/vision.php');
        return;
    }

    if (!empty($vision3Years)) {
        $visions = "<h4>Describe en detalle tu Visión para los Próximos 3 Años:</h4><div>".html_entity_decode($vision3Years)."</div>";
    }
    if (!empty($vision5Years)) {
        $visions .= "<h4>Describe en detalle tu Visión para los Próximos 5 Años:</h4><div>".html_entity_decode($vision5Years)."</div>";
    }
    if (!empty($vision10Years)) {
        $visions .= "<h4>Describe en detalle tu Visión para los Próximos 10 Años:</h4><div>".html_entity_decode($vision10Years)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>Creando un Futuro Extraordinario</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$visions}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send('Creando un Futuro Extraordinario', $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/vision.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Creando un Futuro Extraordinario:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="next-3-years-vision">Describe en detalle tu Visión para los Próximos 3 Años:</label>
                <textarea name="next_3_years_vision" id="next-3-years-vision" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['next_3_years_vision'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="next-5-years-vision">Describe en detalle tu Visión para los Próximos 5 Años:</label>
                <textarea name="next_5_years_vision" id="next-3-years-vision" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['next_5_years_vision'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="next-10-years-vision">Describe en detalle tu Visión para los Próximos 10 Años:</label>
                <textarea name="next_10_years_vision" id="next-10-years-vision" class="tinymce-editor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['next_10_years_vision'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>
            <button class="btn btn-info letter" type="submit" name="save_vision">Guardar</button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_vision"><i class="fa fa-save fa-lg"></i></button>
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
                                <input style="width:100%;" type="email" class="form-control" name="to_email" id="to_email" placeHolder="Enter Email Address">
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