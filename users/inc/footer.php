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
    footer.page-footer ul li{
        padding:10px 0;
    }
    footer.page-footer ul li a{
        font-size:1rem;
    }
</style>
<div class="clearfix" style="float:none; clear:both;"></div>
<footer class="page-footer bg-primary d-block d-md-none g-4 px-3 py-1 pb-2">

    <div class="container">
        <div class="row">
            <div class="col-5">
                <ul class="list-unstyled">
                    <li><a class="text-decoration-none text-white py-1" href="dailygoals.php">Victoria7</a></li>
                    <li><a class="text-decoration-none text-white py-1 <?= $goalType == 'weekly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php">Semanal</a></li>
                    <li><a class="text-decoration-none text-white py-1 <?= $goalType == 'monthly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly">Mensual</a></li>
                    <li><a class="text-decoration-none text-white py-1 <?= $goalType == 'quarterly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly">Trimestral</a></li>
                    <li> <a class="text-decoration-none text-white py-1 <?= $goalType == 'yearly' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly">Anual</a></li>
                    <li><a class="text-decoration-none text-white py-1 <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/supergoals.php?type=lifetime">100 Dreams</a></li>
                    <li><a class="text-decoration-none text-white py-1 <?= $goalType == 'lifetime' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/dream-wall.php">Dream Wall</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="dailycommitments.php">Guerrero D</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="cronovida.php">CronoVida</a></li>
                    <li> <a class="text-decoration-none text-white py-1 <?= $path == 'index.php' ? ' active' : ''; ?>" href="<?= SITE_URL; ?>/users/index.php" id="navbarDropdown">Cartas</a></li>
                    <li>  <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/notebook.php">Escribe Carta</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/mynotes.php">MejorNotes</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/capsules.php">MejorCapsule</a></li>
                </ul>
            </div>
            <div class="col-7">
                <ul class="list-unstyled">
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/missions.php">Mi Missión</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/vision.php?plan=3">Visión 3-Años</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/vision.php>plan=5">Visión 5-Años</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/vision.php?plan=10">Visión 10-Años</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/commitments.php">Mis Compromisos</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/agreements.php">Mis Acuerdos</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/promises.php">Mis Promesas</a></li>
                    <li><a class="text-decoration-none text-white px-1 py-1" href="<?= SITE_URL; ?>/users/lifeTasks.php">Mi tarea de Vida</a></li>
                    <li> <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/dailyVictories.php">Mi Victoria Diaria</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/toRemember.php">Eventos para Recordar</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/biggestVictories.php">Mis Mayores Victorias</a></li>
                    <li> <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/inspirations.php">MejorInspiration</a></li>
                   
                    <li><a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/folder-images.php">Imagenes de Exito</a></li>
                </ul>
            </div>
        </div>
        
    </div>

</footer>
<footer class="bottom-footer page-footer d-block d-md-none g-4 px-3 py-3 pb-2" style="background-color: #57b1ed;">
    <div class="container">
    <div class="row">
            <div class="col-5">
                <ul class="list-unstyled">
                    <li><a class="text-decoration-none text-white py-1" href="dailygoals.php">MejorBlog</a></li>
                    <li> <a class="text-decoration-none text-white py-1" href="<?= SITE_URL; ?>/users/inspirations.php">MejorInspiration</a></li>
                   
                </ul>
            </div>
            <div class="col-7">
                <ul class="list-unstyled">
                    <li> <a class="text-decoration-none text-white py-1" href="#">MejorCadaDía Chef</a></li>
                    <li><a class="text-decoration-none text-white py-1" href="#">MejorCadaDía Hotel</a></li>
                    <li> <a class="text-decoration-none text-white py-1" href="#">MejorFest</a></li>
                   
                </ul>
            </div>
        </div>
    </div>
</footer>
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
  window.addEventListener('load', () => {
  if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js', {
      scope: '/',
    });
  }
});  

setTimeout(() => {
    addToHomeScreen();
}, 1000);

function addToHomeScreen() {
    console.log('addToHomeScreen');
  if ('addEventListener' in document && 'localStorage' in window && 'serviceWorker' in navigator) {
    console.log('addEventListener',window);
    window.addEventListener('load', function () {
      var appInstalled = localStorage.getItem('appInstalled');
      var addIconElement = document.getElementById('install-button');
      console.log('appInstalled',appInstalled);
      if (!appInstalled) {
        var beforeInstallPromptFired = false;
        console.log('appInstalled not installed');
        window.addEventListener('beforeinstallprompt', function (e) {
          e.preventDefault();
          console.log('beforeinstallprompt',e);
         
          beforeInstallPromptFired = true;
          
         
          addIconElement.style.display = 'block';
          
          addIconElement.addEventListener('click', function () {
            console.log('btnclick');
            addIconElement.style.display = 'none';
            
            e.prompt();
            
            e.userChoice.then(function (choiceResult) {
              if (choiceResult.outcome === 'accepted') {
                localStorage.setItem('appInstalled', true);
              }
            });
          });
        });
        
        window.addEventListener('appinstalled', function (e) {
          beforeInstallPromptFired = false;
        });
        
        setTimeout(function () {
          if (!beforeInstallPromptFired) {
            var addIconElement = document.getElementById('install-button');
           
           // addIconElement.style.display = 'none';
          }
        }, 10000);
      }else{
        addIconElement.style.display = 'none';
      }
    });
  }
}
		
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
<style>
     
    .mejor-info-popover{
        max-width:400px;
        min-width:400px;
        border-color:#0b57cf;
        background:#0b57cf;
        position: fixed !important;
        right:50px !important; 
        top:100px !important;
        transform: none !important;
        left: auto !important;
        bottom: auto !important;
        
    }

    .mejor-info-popover.counter1{
        top:220px !important;
    }
    
   
    .popover.mejor-info-popover .popover-arrow{
        bottom: auto !important;
        transform: translate3d(350px, 1px, 0px) !important;

    }
    .mejor-info-popover .popover-body{
        color:#FFF;
    }
    .mejor-info-popover>.popover-arrow::before{

        border-width: 0 0.5rem 0.5rem !important;

    }
    .mejor-info-popover.bs-popover-auto>.popover-arrow::before, .mejor-info-popover.bs-popover-bottom>.popover-arrow::before,
    .mejor-info-popover.bs-popover-auto>.popover-arrow::after, .mejor-info-popover.bs-popover-bottom>.popover-arrow::after{
        border-bottom-color:#0b57cf;
    }
    .mejor-info-popover .close{
        color:#FFF;
        position:absolute;
        right:0;
        top:0;
        text-decoration:none;
        padding: 0 5px;
    }
    .mejor-info-popover .popover-header{
        padding:0;
        border:none;
        background:#0b57cf;
    }
    
    .mejor-info-popover.bs-popover-auto[data-popper-placement^=bottom] .popover-header::before, .mejor-info-popover.bs-popover-bottom .popover-header::before{
        display:none;
    }
    @media (max-width: 575.98px) { 
        .mejor-info-popover{
            width:300px;
            min-width:300px;
        }
        .popover.mejor-info-popover .popover-arrow{
            transform: translate3d(250px, 0px, 0px) !important;
        }
        .mejor-info-popover.counter1{
            top:240px !important;
        }
    }
    .modal.custom-info-modal{

    }
    .modal.custom-info-modal button.btn-close{
        position: absolute;
        right: 4px;
        top: 4px;
    }
    .mejor-info-popover.white-popover{
        background:#FFF;
        border-color:#EEE;
    }
    .mejor-info-popover.white-popover .popover-body{
        color:#000;
    }
    .mejor-info-popover.white-popover.bs-popover-auto>.popover-arrow::before, .mejor-info-popover.white-popover.bs-popover-bottom>.popover-arrow::before,
    .mejor-info-popover.white-popover.bs-popover-auto>.popover-arrow::after, .mejor-info-popover.white-popover.bs-popover-bottom>.popover-arrow::after{
        border-bottom-color:#FFF;
    }
    .mejor-info-popover.white-popover .close{
        color:#000;
    }    
    </style>

<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize popovers
        function setCookie(cname, cvalue, exdays) {
            console.log('setting cookie',cname, cvalue, exdays);
            const d = new Date();
           d.setTime(d.getTime() + (exdays*24*60*60*1000));
           // d.setTime(d.getTime() + (60*1000));
            let expires = "expires="+ d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }
        function getCookie(cname) {
            let name = cname + "=";
            let decodedCookie = decodeURIComponent(document.cookie);
            let ca = decodedCookie.split(';');
            for(let i = 0; i <ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') {
                c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
                }
            }
            return "";
        }
        
       
        
       
        $('[data-bs-toggle="popover"]').popover({container: 'body', title:'<a class="close" href="#">&times;</a>',placement:'top', html: true});
       
        
        
        setTimeout(function() {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popCounter=0;
            
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) { 
                popCounter++;               
                var popoverElemId="#"+popoverTriggerEl.id;
                var popover_page=$(popoverElemId).data('page');
                var popover_days=$(popoverElemId).data('days');
                if(popover_days==undefined){
                    popover_days=1;
                }                
                let pagePoperOverCookie = getCookie(popover_page);                          
                //pagePoperOverCookie='';
                console.log('pagePoperOverCookie',popover_page,pagePoperOverCookie,popover_days);               
                if(pagePoperOverCookie==''){   
                     
                    $(popoverElemId).popover('show'); 
                   // const popoverB = bootstrap.Popover.getInstance(popoverElemId)
                    //console.log(popoverB);                      
                    $(".mejor-info-popover").removeClass('bs-popover-auto');
                    setCookie(popover_page, '1', popover_days);
                }
                

            });
            
                $( ".popover.mejor-info-popover" ).each(function(index) {
                    console.log(index,$( this ));
                    $( this ).addClass("counter"+index );
                });
            
          
            
            /*var popover_page=$('#popovertip').data('page');
            let pagePoperOverCookie = getCookie(popover_page);
            //pagePoperOverCookie='';
            console.log('pagePoperOverCookie',popover_page,pagePoperOverCookie);
            if(pagePoperOverCookie==''){
                
                $('#popovertip').popover('show');
                $(".mejor-info-popover").removeClass('bs-popover-auto');
                setCookie(popover_page, '1', 1);
            }
            showPageModals();  */          
        }, 1000);

        $(document).on('click','a.close',function(e){
            var parElm=$(this).closest('.mejor-info-popover');
            e.preventDefault();
          if(parElm.hasClass('popovertip2')){
            $('#popovertip2').popover('hide');
          }else{
            $('#popovertip').popover('hide');
          }
            
        });

        // You can adjust the delay as needed
    });
    </script>
</body>

</html>