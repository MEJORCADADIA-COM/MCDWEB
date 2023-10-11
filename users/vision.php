<?php
$plan=isset($_GET['plan'])? intval($_GET['plan']):3;
if($plan==3){
    $p_title='Mi Visión a 3 Años';
    $p_heading1='Describe la VISION más Espectacular que Puedas Imaginar para Próximos 3 Años:';
    $p_heading2='Hoja de Ruta; Pasos y Estrategias para Hacer Realidad esta Visión:';
    $p_heading3='Posibles Desafios y Cómo los Superarás:';
    $p_heading4='Victorias y Progreso:';
}elseif($plan==5){
    $p_title='Mi Visión a 5 Años';
    $p_heading1='Describe la VISION más Espectacular que Puedas Imaginar para Próximos 5 Años:';
    $p_heading2='Hoja de Ruta; Pasos y Estrategias para Hacer Realidad esta Visión:';
    $p_heading3='Posibles Desafios y Cómo los Superarás:';
    $p_heading4='Victorias y Progreso:';
}else{
    $p_title='Mi Visión a 10 Años';
    $p_heading1='Describe la VISION más Espectacular que Puedas Imaginar para Próximos 10 Años:';
    $p_heading2='Hola de Ruta; Pasos y Estrategias para Hacer Realidad esta Visión:';
    $p_heading3='Posibles Desafios y Cómo los Superarás:';
    $p_heading4='Victorias y Progreso:';
}
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;


?>

<?php
$vision_type=$plan."_year";
$userVisions = $common->first('user_visions', 'user_id = :user_id AND vision_type=:vision_type', ['user_id' => $user_infos['id'],'vision_type'=>$vision_type]);

if (isset($_POST['save_vision'])) {
    
    $section1 = $fm->validation($_POST['section1']);
    $section2 = $fm->validation($_POST['section2']);
    $section3 = $fm->validation($_POST['section3']);
    $section4 = $fm->validation($_POST['section4']);

    try {
        if ($userVisions) {
            $common->update(
                'user_visions',
                ['section1' => $section1, 'section2' => $section2, 'section3' => $section3, 'section4' => $section4],
                'id = :id',
                ['id' => $userVisions['id']]
            );
        } else {
            $common->insert(
                'user_visions',
                ['user_id' => $user_infos['id'], 'section1' => $section1, 'section2' => $section2, 'section3' => $section3, 'section4' => $section4,'vision_type'=>$vision_type]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Visions saved successfully!');


    header("Location: " . SITE_URL . "/users/vision.php?plan=".$plan);
    return;
}

if (isset($_POST['send_email'])) {
    $section1 = $fm->validation($_POST['section1']);
    $section2 = $fm->validation($_POST['section2']);
    $section3 = $fm->validation($_POST['section3']);
    $section4 = $fm->validation($_POST['section4']);

    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/vision.php');
        return;
    }

    if (empty($section1) && empty($section2) && empty($section3) && empty($section4)) {
        setError('Visions can not be empty');
        redirect('users/vision.php?plan='.$plan);
        return;
    }
    if (!empty($section1)) {
        $visions = "<h4>".$p_heading1."</h4><div>".html_entity_decode($section1)."</div>";
    }
    if (!empty($section2)) {
        $visions = "<h4>".$p_heading2."</h4><div>".html_entity_decode($section2)."</div>";
    }
    if (!empty($section3)) {
        $visions .= "<h4>".$p_heading3."</h4><div>".html_entity_decode($section3)."</div>";
    }
    if (!empty($section4)) {
        $visions .= "<h4>".$p_heading4."</h4><div>".html_entity_decode($section4)."</div>";
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


    redirect("users/vision.php?plan=".$plan);
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;"><?=$p_title;?></h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-5">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="section1"><?=$p_heading1;?></label>
                <textarea name="section1" id="section1" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['section1'] ?? '' ?></textarea>
            </div>
            <div class="mb-5">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="section2"><?=$p_heading2;?></label>
                <textarea name="section2" id="section2" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['section2'] ?? '' ?></textarea>
            </div>
            <div class="mb-5">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="section3"><?=$p_heading3;?></label>
                <textarea name="section3" id="section3" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['section3'] ?? '' ?></textarea>
            </div>
            <div class="mb-5">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="section4"><?=$p_heading4;?></label>
                <textarea name="section4" id="section4" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $userVisions['section4'] ?? '' ?></textarea>
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
<div id="popovertip" data-page="vision" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Sin una Visión que te Inspire y Empodere, tu Vida se desvanece y tus sueños también. Crea la Visión más Espectacular y Atrevida que Puedas Imaginar para tu Vida y que desafíe todo tu ser. Trabaja sin descanso en hacerla Realidad. ¡Si Puedes!"></div>
<?php require_once "inc/footer.php"; ?>