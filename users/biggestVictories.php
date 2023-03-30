<?php
require_once "inc/header.php";
require_once base_path('/users/services/Email.php');

use Users\services\Email;

$biggestVictories = $common->first('biggest_victories', 'user_id = :user_id', ['user_id' => $user_infos['id']]);

if (isset($_POST['save_victories'])) {
    $victory1 = $fm->validation($_POST['victory_1']);
    $victory2 = $fm->validation($_POST['victory_2']);
    $victory3 = $fm->validation($_POST['victory_3']);
    $victory4 = $fm->validation($_POST['victory_4']);
    $victory5 = $fm->validation($_POST['victory_5']);

    try {
        if ($biggestVictories) {
            $common->update(
                'biggest_victories',
                ['biggest_victory_1' => $victory1, 'biggest_victory_2' => $victory2, 'biggest_victory_3' => $victory3, 'biggest_victory_4' => $victory4, 'biggest_victory_5' => $victory5],
                'id = :id',
                ['id' => $biggestVictories['id']]
            );
        } else {
            $common->insert(
                'biggest_victories',
                ['user_id' => $user_infos['id'], 'biggest_victory_1' => $victory1, 'biggest_victory_2' => $victory2, 'biggest_victory_3' => $victory3, 'biggest_victory_4' => $victory4, 'biggest_victory_5' => $victory5]
            );
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Biggest victories saved successfully!');


    header("Location: " . SITE_URL . "/users/biggestVictories.php");
    return;
}

if (isset($_POST['send_email'])) {
    $victory1 = $fm->validation($_POST['victory_1']);
    $victory2 = $fm->validation($_POST['victory_2']);
    $victory3 = $fm->validation($_POST['victory_3']);
    $victory4 = $fm->validation($_POST['victory_4']);
    $victory5 = $fm->validation($_POST['victory_5']);
    $email = $fm->validation($_POST['to_email']);

    if (empty($email)) {
        setError('Email can not be empty');
        redirect('users/biggestVictories.php');
        return;
    }

    if (empty($victory1) && empty($victory2) && empty($victory3) && empty($victory4) && empty($victory5)) {
        setError('Promises can not be empty');
        redirect('users/biggestVictories.php');
        return;
    }

    if (!empty($victory1)) {
        $victories = "<h4>Victorias #1:</h4><div>".html_entity_decode($victory1)."</div>";
    }
    if (!empty($victory2)) {
        $victories .= "<h4>Victorias #2:</h4><div>".html_entity_decode($victory2)."</div>";
    }
    if (!empty($victory3)) {
        $victories .= "<h4>Victorias #3:</h4><div>".html_entity_decode($victory3)."</div>";
    }
    if (!empty($victory4)) {
        $victories .= "<h4>Victorias #4:</h4><div>".html_entity_decode($victory4)."</div>";
    }
    if (!empty($victory5)) {
        $victories .= "<h4>Victorias #5:</h4><div>".html_entity_decode($victory5)."</div>";
    }

    try {
        $body = "
        <ol>
            <div style='width:600px; background-color:#FFF; margin:0 auto;'>
                <header style='background-color: #74be41;'><img src='https://mejorcadadia.com/users/assets/logo.png'></header>                
                <div style='padding:20px; background-color:#FFF;'>
                    <h2 style='text-transform: capitalize;'>Mis Mayores Victorias</h2>
                    <div class='goals-area' style='margin-top:20px; margin-bottom:40px;'></div>  
                    <div class='description-area' style='margin-top:20px; margin-bottom:40px;'>
                        {$victories}
                    </div>      
                </div>
            </div>
        </ol>
        ";

        if ((new Email())->send('Mis Mayores Victorias', $email, $body)) {
            setSuccess('Email sent successfully');
        } else {
            setError();
        }
    } catch (Exception $e) {
        Session::set('error', 'Something went wrong. Please try again later.');
    }
    Session::set('success', 'Mail sent successfully!');


    redirect("users/biggestVictories.php");
    return;
}
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 ">
    <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->
    <div class="projects py-5" style="background-color: #ed008c;">
        <div class="mb-5" style="background-color: #fef200; padding: 10px">
            <h2 class="toptitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Mis Mayores Victorias:</h2>
        </div>
        <form class="px-1 px-lg-0" action="" method="post">
            <div class="mb-4">
                <label class="px-1 text-white" style="margin: 5px 0px; font-size:1rem;" for="victory_1">Victorias #1:</label>
                <textarea name="victory_1" id="victory_1" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $biggestVictories['biggest_victory_1'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1 text-white" style="margin: 5px 0px; font-size:1rem;" for="victory_2">Victorias #2:</label>
                <textarea name="victory_2" id="victory_2" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $biggestVictories['biggest_victory_2'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1 text-white" style="margin: 5px 0px; font-size:1rem;" for="victory_3">Victorias #3:</label>
                <textarea name="victory_3" id="victory_3" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $biggestVictories['biggest_victory_3'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1 text-white" style="margin: 5px 0px; font-size:1rem;" for="victory_4">Victorias #4:</label>
                <textarea name="victory_4" id="victory_4" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $biggestVictories['biggest_victory_4'] ?? '' ?></textarea>
            </div>
            <div class="mb-4">
                <label class="px-1 text-white" style="margin: 5px 0px; font-size:1rem;" for="victory_5">Victorias #5:</label>
                <textarea name="victory_5" id="victory_5" class="ckeditor form-control w-75 mx-auto form-box shadow-lg border border-light border-opacity-10"><?= $biggestVictories['biggest_victory_5'] ?? '' ?></textarea>
            </div>
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>
            <button class="btn btn-info letter" type="submit" name="save_victories">Guardar</button>
            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
            <!-- Floating Button Start -->
            <button class="btn btn-primary rounded-circle text-white floating-btn" type="submit" name="save_victories"><i class="fa fa-save fa-lg"></i></button>
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