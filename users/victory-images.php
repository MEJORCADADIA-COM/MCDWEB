<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');

?>
<?php require_once "inc/header.php"; ?>
<?php

$timezoneOffset = empty($_SESSION['timezoneOffset']) ? '' : $_SESSION['timezoneOffset'];
$time = time();
if (!empty($timezoneOffset)) {
  $timezoneHours = ($timezoneOffset - 240) / 60;
  $timeHourString = '';
  if ($timezoneHours < 0) {

    $timeHourString = '+' . abs($timezoneHours);
  } else {

    $timeHourString = '-' . abs($timezoneHours);
  }
  $time = strtotime($timeHourString . ' hours');
}


$today = date("Y-m-d", $time);
$type = isset($_GET['type'])? trim($_GET['type']):'image';

$date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : '';
$currentDate = empty($date) ? $today : $date;
$currentDate = date('Y-m-d', strtotime($currentDate));
$currentYear = date('Y', strtotime($currentDate));
$currentMonth = date('m', strtotime($currentDate));
$currentWeekNumber = date('W', strtotime($currentDate));
$selectedYear = !empty($_REQUEST['year']) ? (int)$_REQUEST['year'] : $currentYear;
$selectedWeekNumber = !empty($_REQUEST['week']) ? (int)$_REQUEST['week'] : $currentWeekNumber;
$selectedMonth = !empty($_REQUEST['month']) ? (int)$_REQUEST['month'] : $currentMonth;


?>

<?php

$user_id = Session::get('user_id');


$dailyV7Files = $common->get('uploaded_files', 'user_id = :user_id AND type = :type', ['user_id' => $user_id, 'type' => $type],[],'created_at','DESC');

$selectedDate = $currentDate;

$isPastDate = false;


if ($selectedDate < $today) {
  $goalDate = $selectedDate;
  $isPastDate = true;
} else {
  $goalDate = $today;
}
$isPastDate=false;



?>


<script>
  var SITE_URL = '<?= SITE_URL; ?>';
  var currentDate = '<?= $currentDate; ?>';
</script>
<link rel="stylesheet" href="<?=SITE_URL; ?>/users/assets/uikit-lightbox.css" />
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit-icons.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hammerjs@2.0.8/hammer.min.js"></script>

<style>
  .uk-lightbox.uk-open img{
  transition: all .2s ease-in-out;
  transform: scale(1);
}
  .modal-header .modal-title{
    color:#FFF;
  }
  .media-gallery-row video{
    max-height:200px;
  }
  .file-actions{
    position:absolute;
    top:6px; right:6px;
  }
  .v7-media-box{
    width: 114px;
    position: relative;
  }
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10">

<?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        
        
        <div class="row">
          <div class="col-12" style="text-align:center;">
          <a  class="btn btn-warning btn-sm pull-left" href="<?=SITE_URL;?>/users/dailygoals.php?date=<?=$currentDate;?>"><?=translate('Back') ?></a>
            <h2 style="text-transform: capitalize;">
            Gallery/Images
            </h2>
          </div>
          
        </div>

      </header>
      <div class="media-items media-gallery p-2" style="min-height:500px;">
       
       
           
            <div class="media-date-item mb-3 media-gallery-row">
                
                <div class="d-flex flex-wrap bd-highlight mb-3" uk-lightbox="animation: slide">
                    
                    <?php foreach ($dailyV7Files as $key => $file): ?>
                      <?php setlocale(LC_ALL, $locales[$userLanguage]);
        $string = date('d/m/Y', strtotime($file['created_at']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
                    <div class="p-1 bd-highlight v7-media-box"  data-file="<?=$file['id'];?>" style="position:relative;">
                            <?php if($file['type']=='image'): ?>
                                <a href="<?=$file['url'];?>" data-index="<?=$key;?>" id="lightbox-thumb-item-<?=$key;?>" data-caption="<?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?>" > 
                                <img class="img-fluid rounded-3 w-100 shadow-1-strong"  src="<?=$file['thumb'];?>">
                              </a>
                              <div class="file-actions">
                          <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><div class="dropdown-item file_delete" href="#"><?=translate('Delete') ?></div></li>
                            </ul>
                          </div>
                          
                        </div>
                             <?php endif; ?>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        
       </div>
    

      
    </div>
  </div>

  <div class="clearfix;"></div>
</main>
<!-- Modal -->

<!-- Lightbox (made with Bootstrap modal and carousel) -->
<!-- Modal -->
<div class="modal fade p-0 bg-dark " id="mediaLightBoxModal" tabindex="-1" aria-labelledby="mediaLightBoxModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-md-down">
    <div class="modal-content bg-dark border-0">
      <div class="modal-header border-0"> 
       <h5 class="modal-title" id="exampleModalLabel"></h5>      
        <button type="button" class="btn-close bg-white border border-warning" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="text-align:center;">       
        <img src="" data-index="0" class="img-fluid">
        <button class="carousel-control-prev" type="button" data-bs-target="#mediaLightBoxModal" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?=translate('Previous') ?></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#mediaLightBoxModal" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden"><?=translate('Next') ?></span>
          </button>
      </div>
    </div>
  </div>
</div>



<div class="toast-container position-absolute top-0 end-0 p-3">
  <div class="toast" id="toast">

    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>
<script>
 

 let zoomLevel = 1;
  let maxZoomLevel=3;
  
  UIkit.util.on(document, 'itemhide', '.uk-lightbox.uk-open', function (event, lightbox) {
    console.log('Lightbox is displayed! itemhide',lightbox);
    const activeImage = lightbox.slides[lightbox.index].querySelector("img");
    zoomLevel=1;
    activeImage.style.transform = `scale(${zoomLevel})`;
  });
  UIkit.util.on(document, 'itemshown', '.uk-lightbox.uk-open', function (event, lightbox) {
      console.log('Lightbox is displayed! itemshown',lightbox);
      zoomLevel=1;
      const activeImage = lightbox.slides[lightbox.index].querySelector("img");
    
      //console.log('activeImage',lightbox.slides[lightbox.index],activeImage);
      //console.log('Active Image:', activeImage.getAttribute('src'));
      const mc = new Hammer(activeImage);
      mc.get('pinch').set({ enable: true });
      
      mc.on('doubletap', function (e) {        
        if(zoomLevel==3){
          zoomLevel=1;
        }
        if(zoomLevel!=3){
          zoomLevel=3;
        }
        updateZoom();
      });
      mc.on('pinch', function (e) {
        
       // zoomLevel = Math.max(.999, Math.min(last_scale * (e.scale), 4));
       zoomLevel = Math.max(1, Math.min(3, zoomLevel * e.scale)); // Adjust the maximum and minimum zoom levels as needed
      // $('.uk-lightbox-caption').html(zoomLevel+"--"+e.scale);
        updateZoom();
        
      });
      activeImage.addEventListener('wheel', function (e) {
        if (e.deltaY > 0) {
          zoomOut();
        } else {
          zoomIn();
        }
      });
      function zoomIn() {
        zoomLevel += 0.1;
        if(zoomLevel>maxZoomLevel){
          zoomLevel=maxZoomLevel;
        }
        updateZoom();
      }
      function zoomOut() {
        zoomLevel -= 0.1;
        if(zoomLevel<1)
        zoomLevel=1;
        updateZoom();
      }
      function updateZoom() {
        
        console.log('zoomLevel',zoomLevel);
        activeImage.style.transform = `scale(${zoomLevel})`;
      }
  });
  

$(document).on('click','.file-actions .file_delete',function(e){
    console.log('de;ete');
    e.preventDefault();
    $parentElem=$(this).parents('.v7-media-box');
    let fileId=$parentElem.data('file');
    $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'DeleteV7MediaFile',
          currentDate: currentDate,
          id: fileId,
        },
        success: function(data) {
          console.log('data', data);
          $parentElem.remove();
        }
      });
    
  });
function showToast(type = 'success', message = '') {
  
    $('#toast .toast-body').html(message);
    if (type == 'success') {
      $('#toast').addClass('bg-primary text-white');
      $('#toast').removeClass('bg-danger text-white');
    } else {
      $('#toast').removeClass('bg-primary text-white');
      $('#toast').addClass('bg-danger text-white');
    }
    var toastElList = [].slice.call(document.querySelectorAll('.toast'));
    var toastList = toastElList.map(function(toastEl) {
      // Creates an array of toasts (it only initializes them)

      return new bootstrap.Toast(toastEl) // No need for options; use the default options
    });
    toastList.forEach(toast => toast.show()); // This show them
  }

 

  
  

  
  


  $(function() {
   
  });
</script>
<script type="application/javascript">
  
</script>
<?php require_once "inc/footer.php"; ?>