<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;
?>

<?php
$lifeTasks = $common->first('life_tasks', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_life_tasks'])) {
    $lifeExpectations = $fm->validation($_POST['life_expectations']);
    $feelHaveToDo = $fm->validation($_POST['feel_have_to_do']);
    $thingsToDoBeforeDeath = $fm->validation($_POST['things_to_do_before_death']);
    $bestWayToServeHumanity = $fm->validation($_POST['best_way_to_serve_humanity']);
    $highestUseOfTime = $fm->validation($_POST['highest_use_of_time']);

    try {
        if ($lifeTasks) {
            $common->update(
                'life_tasks',
                ['life_expectations' => $lifeExpectations, 'feel_have_to_do' => $feelHaveToDo, 'things_to_do_before_death' => $thingsToDoBeforeDeath, 'best_way_to_serve_humanity' => $bestWayToServeHumanity, 'highest_use_of_time' => $highestUseOfTime],
                'id = :id',
                ['id' => $lifeTasks['id']]
            );
        } else {
            $common->insert(
                'life_tasks',
                ['user_id' => $user_infos['id'], 'life_expectations' => $lifeExpectations, 'feel_have_to_do' => $feelHaveToDo, 'things_to_do_before_death' => $thingsToDoBeforeDeath, 'best_way_to_serve_humanity' => $bestWayToServeHumanity, 'highest_use_of_time' => $highestUseOfTime]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Life Tasks saved successfully!');


    header("Location: " . SITE_URL . "/users/lifeTasks.php");
    return;
}

if (isset($_POST['send_email'])) {
    $lifeExpectations = $fm->validation($_POST['life_expectations']);
    $feelHaveToDo = $fm->validation($_POST['feel_have_to_do']);
    $thingsToDoBeforeDeath = $fm->validation($_POST['things_to_do_before_death']);
    $bestWayToServeHumanity = $fm->validation($_POST['best_way_to_serve_humanity']);
    $highestUseOfTime = $fm->validation($_POST['highest_use_of_time']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/lifeTasks.php');
        return;
    }

    if (empty($lifeExpectations) && empty($feelHaveToDo) && empty($thingsToDoBeforeDeath) && empty($bestWayToServeHumanity) && empty($highestUseOfTime)) {
        setError('Life tasks can not be empty');
        redirect('users/lifeTasks.php');
        return;
    }

    if (!empty($lifeExpectations)) {
        $lifeTasks = "<h4>¿Qué es lo que Vida Espera de Mí? ¿Qué espera que Haga o no Haga?:</h4><div>".html_entity_decode($lifeExpectations)."</div>";
    }
    if (!empty($feelHaveToDo)) {
        $lifeTasks .= "<h4>¿Qué Siento que tengo que Hacer o llevar a Cabo en Mi Vida?:</h4><div>".html_entity_decode($feelHaveToDo)."</div>";
    }
    if (!empty($thingsToDoBeforeDeath)) {
        $lifeTasks .= "<h4>¿Qué cosas, tareas o acciones debo de Hacer antes de Morirme?:</h4><div>".html_entity_decode($thingsToDoBeforeDeath)."</div>";
    }
    if (!empty($bestWayToServeHumanity)) {
        $lifeTasks .= "<h4>¿Cuál/es es la Mejor Manera de Servir a la Humanidad en el tiempo que me queda?:</h4><div>".html_entity_decode($bestWayToServeHumanity)."</div>";
    }
    if (!empty($highestUseOfTime)) {
        $lifeTasks .= "<h4>¿Cuál es el Mejor y el Más Alto uso de Mi Tiempo?:</h4><div>".html_entity_decode($highestUseOfTime)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>Mi tarea de Vida</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$lifeTasks}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send('Mi tarea de Vida', $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/lifeTasks.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Mi tarea de Vida:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="life_expectations">¿Qué es lo que Vida Espera de Mí? ¿Qué espera que Haga o no Haga?:</label>
                <textarea name="life_expectations" id="life_expectations" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $lifeTasks['life_expectations'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="feel_have_to_do">¿Qué Siento que tengo que Hacer o llevar a Cabo en Mi Vida?:</label>
                <textarea name="feel_have_to_do" id="feel_have_to_do" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $lifeTasks['feel_have_to_do'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="things_to_do_before_death">¿Qué cosas, tareas o acciones debo de Hacer antes de Morirme?:</label>
                <textarea name="things_to_do_before_death" id="things_to_do_before_death" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $lifeTasks['things_to_do_before_death'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="best_way_to_serve_humanity">¿Cuál/es es la Mejor Manera de Servir a la Humanidad en el tiempo que me queda?:</label>
                <textarea name="best_way_to_serve_humanity" id="best_way_to_serve_humanity" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $lifeTasks['best_way_to_serve_humanity'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1" style="margin: 5px 0px; font-size:1rem;" for="highest_use_of_time">¿Cuál es el Mejor y el Más Alto uso de Mi Tiempo?:</label>
                <textarea name="highest_use_of_time" id="highest_use_of_time" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $lifeTasks['highest_use_of_time'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>
            <button class="btn btn-info letter" type="submit" name="save_life_tasks">Save</button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_life_tasks"><i class="fa fa-save fa-lg"></i></button>
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