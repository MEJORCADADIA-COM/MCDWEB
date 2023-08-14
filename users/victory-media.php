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
$dailyV7Gallery=[];
foreach($dailyV7Files as $file){
    $file_date=date('Y-m-d',strtotime($file['created_at']));
    $dailyV7Gallery[$file_date][]=$file;
}
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
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<style>
  .media-gallery-row video{
    max-height:120px;
  }
  audio,video{min-width:200px;}
  @media screen and (max-width: 767px) {
    audio,video{width:100%;}
  }
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10">

<?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        
        
        <div class="row">
        <div class="col-12" style="text-align:center;">
          <a  class="btn btn-warning btn-sm pull-left" href="<?=SITE_URL;?>/users/dailygoals.php?date=<?=$currentDate;?>">Back</a>
            <h2 style="text-transform: capitalize;">
            <?php if($type=='image'): ?>
                Gallery/Images
            <?php elseif($type=='audio'): ?>
                Gallery/Audios
            <?php elseif($type=='video'): ?>
                Gallery/Videos
            <?php endif; ?>
            </h2>
          </div>
          
        </div>

      </header>
      <div class="media-items media-gallery" style="min-height:500px;">
        <?php foreach ($dailyV7Gallery as $day => $files): ?>
        <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($day));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
           
            <div class="media-date-item mb-3 media-gallery-row">
                <h4 style="background-color: #fef200; margin:0; padding:8px 10px; margin-bottom:10px;"><?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?></h4>
                <div class="d-flex flex-row bd-highlight m-3">
                    
                    <?php foreach ($files as $key => $file): ?>
                    <div class="p-1 w-100 bd-highlight">
                            <?php if($file['type']=='audio'): ?>
                                <audio controls src="<?=$file['url'];?>">
                                <a href="<?=$file['url'];?>">
                                    Download audio
                                </a> 
                                </audio>
                            <?php elseif($file['type']=='video'):  $poster=''; 
                            if(!empty($file['thumb'])){ $poster='poster="'.$file['thumb'].'"'; }
                            ?>
                                <video class="" controls preload="metadata" <?=$poster;?> >
                                    <source src="<?=$file['url'];?>#t=0.2" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                    </div>
                    <?php endforeach;?>
                </div>
            </div>
        <?php endforeach;?>
       </div>
    

      
    </div>
  </div>

  <div class="clearfix;"></div>
</main>
<!-- Modal -->

<!-- Lightbox (made with Bootstrap modal and carousel) -->
<!-- Modal -->
<div class="modal fade p-0" id="mediaLightBoxModal" tabindex="-1" aria-labelledby="mediaLightBoxModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-md-down modal-xl modal-dialog-centered modal-dialog-scrollable ">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">        
        <button type="button" class="btn-close bg-white border border-warning" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">       
        <img src="" class="img-fluid">
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
var mediaLightBoxModal = document.getElementById('mediaLightBoxModal')
  mediaLightBoxModal.addEventListener('show.bs.modal', function (event) {
  // Button that triggered the modal
  var button = event.relatedTarget
  var imgsrc=button.getAttribute('href');
  console.log('imgsrc',imgsrc);
  var modalBodyInput = mediaLightBoxModal.querySelector('.modal-body img');
  modalBodyInput.src = imgsrc;
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