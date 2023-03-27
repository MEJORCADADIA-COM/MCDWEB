<?php
require_once "../inc/inspirationQuote.php";
?>

</div>
</div>
<style>
    @media screen and (max-width: 480px) {
        .footertitle {
            color: #fef200;
            text-align: center;
            margin: 0px;
            font-size: 18px;
        }

        .footertitleleft {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 13px;
        }

        .footertitlerigth {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 13px;
        }
    }

    @media screen and (min-width: 600px) {
        .footertitle {
            color: #fef200;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }

        .footertitleleft {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 13px;
        }

        .footertitlerigth {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 13px;
        }
    }

    @media screen and (min-width: 786px) {
        .footertitle {
            color: #fef200;
            text-align: center;
            margin: 0px;
            font-size: 40px;
        }

        .footertitleleft {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }

        .footertitlerigth {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }
    }

    @media screen and (min-width: 992px) {
        .footertitle {
            color: #fef200;
            text-align: center;
            margin: 0px;
            font-size: 40px;
        }

        .footertitleleft {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }

        .footertitlerigth {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }
    }

    @media screen and (min-width: 1200px) {
        .footertitle {
            color: #fef200;
            text-align: center;
            margin: 0px;
            font-size: 40px;
        }

        .footertitleleft {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }

        .footertitlerigth {
            color: #000000;
            text-align: center;
            margin: 0px;
            font-size: 20px;
        }
    }

    @media print {

        .footertitle,
        .footer-navbar,
        .tox.tox-tinymce-aux,
        .tox-editor-header,
        .tox-statusbar {
            display: none;
        }
    }
</style>
<div class="clearfix" style="float:none; clear:both;"></div>
<!-- Mobile Navbar Start -->
<nav class="navbar d-block d-md-none m-0 p-0">
    <div class="bg-primary row g-4 px-3 py-1 pb-3" style="font-size: 1.1rem;">
        <div class="col-5">
        <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="dailygoals.php">Victoria7</a>
            </div>
            
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php">Semanal</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly">Mensual</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly">Trimestral</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly">Anual</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime">De por Vida</a>
            </div>
        </div>
        <div class="col-7">
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/missions.php">Mi Missión</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/vision.php">Mi Visión</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/commitments.php">Mis Compromisos</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/agreements.php">Mis Acuerdos</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/promises.php">Mis Promesas</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/lifeTasks.php">Mi tarea de Vida</a>
            </div>
        </div>
        <div class="col-5">
        <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="dailycommitments.php">Guerrero D</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="cronovida.php">CronoVida</a>
            </div>
            
            <div class="py-2">
                <a class="text-decoration-none text-white py-1 <?= $path == 'index.php' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/index.php" id="navbarDropdown">Cartas</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/notebook.php">Escribe Carta</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/mynotes.php">MejorNotes</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/capsules.php">MejorCapsule</a>
            </div>
        </div>
        <div class="col-7">
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/dailyVictories.php">Mi Victoria Diaria</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/toRemember.php">Eventos para Recordar</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/biggestVictories.php">Mis Mayores Victorias</a>
            </div>
            <div class="py-2">
                <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/inspirations.php">MejorInspiration</a>
            </div>
        </div>
    </div>
</nav>
<!-- Mobile Navbar End -->
<!-- Inspiration Quote Capsule Start -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap');

    .quote-text {
        font-family: 'Ubuntu', sans-serif;
        font-size: 1.2rem;
        text-align: center;
    }

    .plus-icon {
        position: absolute;
        color: white;
        border-radius: 5px;
    }


    @media screen and (min-width: 1200px) {
        .plus-icon {
            bottom: 5px;
            right: 5px;
            padding: 4px 10px;
        }
    }

    @media screen and (max-width: 480px) {
        .plus-icon {
            bottom: 10px;
            right: 0px;
            padding: 3px 7px;
        }

    }
</style>

<?php
$inspirationQuote = getInspirationQuote();
if (!empty($inspirationQuote)) :
?>
    <div class="inspiration-box d-flex px-1 px-lg-1 justify-content-center navbar quote-text text-white bg-dark position-relative">
        <?= htmlspecialchars_decode(getInspirationQuote()) ?>
        <a class="plus-icon text-white bg-primary text-decoration-none" style="font-size: 0.9rem; font-weight:300;" href="<?= SITE_URL; ?>/users/inspirations.php">
            Más <i class="fa fa-angle-double-right" aria-hidden="true"></i>
        </a>
    </div>
<?php endif; ?>
<!-- Inspiration Quote Capsul End-->


<nav class="footer-navbar navbar navbar-dark flex-md-nowrap pb-2" style="background-color: #f36523;display: flex; justify-content: center; padding: 15px;">
    <h1 class="footertitle">Yes I Can. Yes I Will. It`s Worth it</h1>
</nav>
<nav class="navbar footer-navbar navbar-dark flex-md-nowrap pb-2" style="background-color: #fef200;display: flex; justify-content: space-between; padding: 5px;">
    <h1 class="footertitleleft">Mejorcadadia.com</h1>
    <h1 class="footertitlerigth">All rights reserved 2022</h1>
</nav>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous">
</script>
<script src="<?= SITE_URL; ?>/users/assets/bootstrap-datepicker.min.js"></script>

<?php
if (!$user_infos) :
?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
<?php
endif;
?>
<script>

    const alerts = document.querySelectorAll('.alert-timeout')
    if (alerts.length > 0) {
        setTimeout(() => {
            alerts.forEach((alert) => {
                alert.classList.add('d-none')
            })
        }, 10000)
    }
</script>
<script>
    const createFolderModal = document.getElementById('createFolderModal');
    const createFolderForm = document.getElementById('createFolderForm');
    createFolderForm.addEventListener('submit', event => {
        event.preventDefault();
        var folder_name=document.getElementById('folder_name').value;
        var folder_id=document.getElementById('folder_id').value; 
        console.log(folder_name,folder_id) ;     
        $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'createFolder',
                    folder_name: folder_name,
                    folder_id: folder_id,
                },
                success: function(json) {
                   
                    const obj = JSON.parse(json);
                    console.log(obj);
                    if(obj.success){
                        if(obj.new){
                            $('ul.my-notes-menu').find(' > li.create-folder-nav').before('<li class="nav-item"><a class="nav-link" href="mynotes.php?folder_id='+obj.folder_id+'">'+obj.folder_name+'</a></li>');
                        }else{

                        }
                        document.getElementById('folder_name').value="";
                        document.getElementById('folder_id').value=0;
                        const modal = bootstrap.Modal.getInstance(createFolderModal);
                        modal.hide();
                    }
                }
            });
            
       return false;
        
    });
    
    createFolderModal.addEventListener('show.bs.modal', event => {
    // Button that triggered the modal
    const button = event.relatedTarget
    // Extract info from data-bs-* attributes
    const recipient = button.getAttribute('data-bs-whatever')
    // If necessary, you could initiate an AJAX request here
    // and then do the updating in a callback.
    //
    // Update the modal's content.
    const modalTitle = createFolderModal.querySelector('.modal-title')
    const modalBodyInput = createFolderModal.querySelector('.modal-body input')

    //modalTitle.textContent = `New message to ${recipient}`
    //modalBodyInput.value = recipient
    });
    console.log(window);
    window.onbeforeunload=function(){
        return null;
    };
</script>
</body>

</html>