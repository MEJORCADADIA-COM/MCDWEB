<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
//$preHead='<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />';
?>
<?php require_once "inc/header.php"; ?>
<?php
if (isset($_GET['timezoneoffset'])) {
  $_SESSION['timezoneOffset'] = $_GET['timezoneoffset'];
  header('Location: dailygoals.php');
}
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
$type = 'daily';

$date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : '';
$currentDate = empty($date) ? $today : $date;

$currentDate = date('Y-m-d', strtotime($currentDate));

$minute = date("i",$time);
$second = date("s", $time);
$hour = date("H", $time);
$currentDateTime = $currentDate." ".$hour.":".$minute.":".$second;

$currentYear = date('Y', strtotime($currentDate));
$currentMonth = date('m', strtotime($currentDate));
$currentWeekNumber = date('W', strtotime($currentDate));
$selectedYear = !empty($_REQUEST['year']) ? (int)$_REQUEST['year'] : $currentYear;
$selectedWeekNumber = !empty($_REQUEST['week']) ? (int)$_REQUEST['week'] : $currentWeekNumber;
$selectedMonth = !empty($_REQUEST['month']) ? (int)$_REQUEST['month'] : $currentMonth;


?>
<?php if ($timezoneOffset == '') : ?>
  <script>
    var browserTime = new Date();
    var timezoneOffset = browserTime.getTimezoneOffset();
    window.location.href = "superdias.php?timezoneoffset=" + timezoneOffset;
  </script>
<?php endif; ?>
<?php

$user_id = Session::get('user_id');
$dailyTopGoals = [];


$dailyTopGoals = $common->get('daily_superdias', "user_id = :user_id", ['user_id' => $user_id], orderBy: 'created_at',order: 'DESC');

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
  var topGoalsCounts = '<?= count($dailyTopGoals); ?>';

  var currentDate = '<?= $currentDate; ?>';
  var currentDateTime = '<?= $currentDateTime; ?>';
  

</script>

<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>


<style>
  .text-white-80 {
      --bs-text-opacity: 1;
      color: rgba(255,255,255,.8)!important;
  }
  @media screen and (max-width: 480px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
    }

    .maincontonent {
      width: 100%;
      min-height: 100vh;
    }

    .fixed-save-btn {
      bottom: 35px;
      right: 5px;
    }

    
  }

  @media screen and (min-width: 600px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
    }

    .maincontonent {
      width: 100%;
      min-height: 100vh;
    }

    .fixed-save-btn {
      bottom: 40px;
      right: 5px;
    }

   
  }

  @media screen and (min-width: 786px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
    }

    .maincontonent {
      width: 87.9%;
      height: auto;
    }

    .fixed-save-btn {
      bottom: 40px;
      right: 5px;
    }
  }

  @media screen and (min-width: 992px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
    }

    .maincontonent {
      width: 87.9%;
      height: auto;
    }

    .fixed-save-btn {
      bottom: 40px;
      right: 10px;
    }
  }

  @media screen and (min-width: 1200px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
    }

    .maincontonent {
      width: 87.9%;
      height: auto;
    }

    .fixed-save-btn {
      bottom: 40px;
      right: 15px;
    }

    
  }

  .fixed-save-btn {
    z-index: 1111;
    position: fixed;
  }

  .nav-2 {
    z-index: 1000;
  }

  .goals-area ol li {
    font-size: 1rem;
    color: #FFF;
    margin-bottom: 10px;
    padding-right: 2rem;
    position: relative;
  }

  .goals-area ol li label {
    display: inline;
  }

  .goals-area ol li input {
    width: 1.5rem;
    height: 1.5rem;
    position: absolute;
    right: 5px;
    top: 25%;
  }

  .check-items {
    width: 1.5rem;
    height: 1.5rem;
    position: absolute;
    right: 0;
  }

  .goals-area ol li.hidden {
    display: none;
  }

  #new-top-goal-creation-container .form-group,
  #new-life-goal-creation-container .form-group {
    margin-bottom: 20px;
  }

  .prev-arrow i,
  .next-arrow i {
    color: #FFF;
    font-size: 1.8rem;
  }

  .projects-header p {
    font-size: 1.1rem;
  }

  .goal-list textarea {
    width: 100%;
  }

  #section_box_wrapper {
    display: none;
  }

  #section_box_wrapper .section_box {
    margin-bottom: 20px;
  }

  #section_box_wrapper .section_box .section_header {
    background: #fef200;
    padding: 10px;

  }

  #section_box_wrapper .section_box .section_header h2 {
    margin-bottom: 0;
    color: #202020;
  }

  #section_box_wrapper .section_box .section_header small {
    font-size: 1rem;
  }

  #section_box_wrapper .section_box .section_content {
    padding: 5px;
  }

  @media print {
    .goals-area ol li.hidden {
      display: list-item;
    }
  }

  .edit-actions {
    display: none;
  }

  .edit-actions i {
    color: #fef200;
  }

  .goals-area.edit .edit-actions {
    display: inline-block;
  }

  .has-errors input {
    border-color: #F00;
  }

  /* #life-goals-area:not(.edit) {} */

  @media screen and (max-width: 767px) {
    h2.maintitle {
      font-size: 1rem;
    }

    .projects-header h2 {
      font-size: 1.1rem;
    }

    .goals-area ol li {
      padding-right: 2rem;
    }

    #goals-area {
      padding: 20px 0px;
    }

    .chart-btn {
      right: 0;
      top: calc(10% + 30px);
    }

    .goals-area ol li input {
      top: 10%;
    }

    .projects-header {
      padding: 10px 20px;
    }

    #section_box_wrapper .section_box .section_header {
      padding: 5px;
    }

    #section_box_wrapper .section_box .section_content {
      padding: 5px;
    }
  }

  .admin-dashbord {
    background: #ed008c;
  }

  .projects {
    border: none;
  }

  .slider {
    overflow: hidden;
    position: relative;
  }

  .slider>div {
    min-width: 100%;
    transition-duration: .3s;
  }

  #next-btn {
    position: absolute;
    right: 5px;
    top: 5px;
  }

  #prev-btn {
    position: absolute;
    left: 5px;
    top: 5px;
    display: none;
  }



                        
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3">

   <!-- Secondary Nav -->
    <?php require_once 'inc/secondaryNav.php'; ?>
    <!-- Secondary Nav -->

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
       
          <div class="row" style="margin-bottom:15px;">
            <div class="col-sm-9"></div>
            <div class="col-sm-3">
              <div class="input-group date daily-datepicker datepicker" id="datepicker">
                <?php $df = 'd-m-Y';

                ?>
                <input type="text" class="form-control" value="<?= date($df, strtotime($currentDate)); ?>" id="date" readonly />
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
          </div>
     
        <?php setlocale(LC_ALL, $locales[$userLanguage]);
        $string = date('d/m/Y', strtotime($currentDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/superdias.php?date=<?= date('Y-m-d', strtotime('-1 day', strtotime($currentDate))); ?>" ;><i class="fa fa-arrow-left"></i></a></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;"><?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?></h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/superdias.php?date=<?= date('Y-m-d', strtotime('+1 day', strtotime($currentDate))); ?>"><i class="fa fa-arrow-right"></i></a></div>
        </div>

      </header>
    
        
     

      <!-- Slider Start -->
      <div class="d-flex w-100 slider">
        <div class="pt-5" id="slide-1">
          <form class="form" id="goalsFrom">
            <div class="mt-5" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; "><?=translate('SuperDias')?>:
                <?php if ($isPastDate == false) : ?>
                  <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn1"><?=translate('Editar')?></button>
                <?php endif; ?>
              </h2>
            </div>
            <div class="cardd mb-4" id="section-1">
              <div class="goals-area" id="top-goals-area" style="display:block; ">
                  <?php if($selectedDate<=$today): ?>
                  <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-top-goal-btn-wrapper">
                    <button type="button" id="save-new-top-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewGoals()"><i class="fa fa-save"></i> <?=translate('Guarda Nuevo Objetivo')?></button>
                    <button type="button" class="button btn btn-info" onClick="CreateDailyTopGoal()"><i class="fa fa-book"></i> <?=translate('Agrega Objetivo')?></button>
                  </div>
                  <div class="form-group" id="new-top-goal-creation-container"></div>
                  <?php endif; ?>
                  <ol id="daily-top-goal-list" class="goal-list">
                  <?php foreach ($dailyTopGoals as $key => $item) : setlocale(LC_ALL, $locales[$userLanguage]);
        $string = date('d/m/Y', strtotime($item['created_at']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);  ?>
                    <li class="border-bottom py-2" id="top-goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                      <label id="top-list-label-<?= $item['id']; ?>">

                        <p class="text-white-80" style="text-transform: capitalize;"><?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?></p>
                        <span style="font-size: 1rem;" id="topGoalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                        
                        <a class="edit-actions edit-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                        <a class="edit-actions delete-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                      </label>
                    </li>
                  <?php endforeach; ?>
                </ol>
                
              </div>
            </div>
           
           
          </form>
        </div>


        
        
     </div>
    </div>
  </div>

  <div class="clearfix;"></div>
</main>
<!-- Modal -->








<div class="toast-container position-absolute top-0 end-0 p-3">
  <div class="toast" id="toast">

    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>
<script>
  $('#show').css('display', 'none');



 
 
 
  let currentCount = 0;

  var goalstobeadded = 0;
  var newgoalsInput = [];


  function SaveNewGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    var validated = hasFilledNewGoals('newtopgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          SaveNewDailySuperDias: 'SaveNewDailySuperDias',
          currentDate: currentDate,
          currentDateTime: currentDateTime,
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-top-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);


              $("#daily-top-goal-list").prepend('<li class="" id="top-goal-list-item-' + prop + '"><p class="text-white-80" style="text-transform: capitalize;">'+jsonObj.date+'</p><label class="form-label" id="top-list-label-' + prop + '"><span id="topGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <a class="edit-actions edit-goal-btn" data-type="top" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-top-goals-btn').hide();
          }
        }
      });
    }
  }

  function hasFilledNewGoals(classname) {
    var filled = true;
    newgoalsInput = [];
    $newgoalsinputEmpty = document.querySelectorAll("textarea." + classname);
    for (var i = 0; i < $newgoalsinputEmpty.length; ++i) {
      if ($newgoalsinputEmpty[i].value == '') {
        filled = false;
        $newgoalsinputEmpty[i].classList.add('is-invalid');
      } else {
        $newgoalsinputEmpty[i].classList.remove('is-invalid');
        newgoalsInput.push($newgoalsinputEmpty[i].value);
      }
    }
    return filled;
  }

  function CreateDailyTopGoal() {
    $wrapper = $('#new-top-goal-creation-container');
    var validated = hasFilledNewGoals('newtopgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    if (validated) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newtopgoals' name='newtopgoals[]'/></textarea></div>");
     
      $('#save-new-top-goals-btn').show();

    } else {
      showToast('error', 'Please fill');
    }
  }

  function addTextArea(wrapperId, itemClass, name, placeholder, classes) {
    $wrapper = $(`#${wrapperId}`);
    $wrapper.append(`<li class="${itemClass}"><div class="form-group"><textarea class="w-100 text-white mt-2 bg-transparent text-bottom-border ${classes}" name=${name}/></textarea></div></li>`);
  }

  function showButton(buttonId) {
    $(`#${buttonId}`).show();
  }


  function CreateDailyLifeGoal() {
    $wrapper = $('#new-life-goal-creation-container');
    var validated = hasFilledNewGoals('newlifegoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newlifegoals");
    if (validated && remainingLifeGoals > 0) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newlifegoals' name='newlifegoals[]'/></textarea></div>");
      remainingLifeGoals--;
      $('#save-new-life-goals-btn').show();

    } else {
      showToast('error', 'You can only add maximum 7 goals.');
    }
  }

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

  function CreateGoal(type) {
    $wrapper = $('#new-top-goal-creation-container');
    var validated = hasFilledNewGoals('newtopgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    if (validated) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newgoals' name='newgoals[]'/></textarea></div>");
      goalstobeadded++;
      if (goalstobeadded > 0) {
        $('#save-new-top-goals-btn').show();
      }
    }



  }
  
 

  


  $(document).on('click', '.edit-goal-btn', function(e) {
    e.preventDefault();
    var sectionType = $(this).data('type');
    var goalId = $(this).data('id');
    console.log('goalId', goalId, sectionType);
    var goalTextElem;
    var actionName = '';
    var goalTextElem = $('#topGoalText-' + goalId);
    actionName = 'UpdateDailySuperDias';    
    $(this).addClass(sectionType);
    goalText = goalTextElem.text();
    console.log('goalText', goalText);
    if ($(this).find('.fa').hasClass('fa-pencil')) {
      $(this).find('.fa').removeClass('fa-pencil');
      $(this).find('.fa').addClass('fa-save');
      $(this).addClass('save');
      goalTextElem.hide();
      var containterItemId = sectionType + '-list-label-' + goalId;
      $("#" + containterItemId).append('<textarea id="edittextarea-' + sectionType + goalId + '">' + goalText + '</textarea>');
    } else {
      $(this).removeClass('save');
      var checkedboxClass = '.input-' + sectionType + 'goals';
      var checked = $(this).find(checkedboxClass).is(':checked');
      $(this).find('.fa').addClass('fa-pencil');
      $(this).find('.fa').removeClass('fa-save');
      goalTextElem.show();
      var textareaElem = $('#edittextarea-' + sectionType + goalId);
      var goalText = textareaElem.val();
      goalTextElem.text(goalText);
      textareaElem.remove();
      var achieved = 0;
      if (checked) {
        achieved = 1;
      } else {
        achieved = 0;
      }

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          UpdateDailySuperDias: 'UpdateDailySuperDias',
          currentDate: currentDate,
          currentDateTime: currentDateTime,
          goalText: goalText,
          achieved: achieved,
          goalId: goalId,
          edit: 1,
        },
        success: function(data) {
          console.log('data', data);
          if (data == 'Update') {
            $('#show').css('display', 'block');
            $('#error_success_msg_verification').css('color', '#000000');
            $('#error_success_msg_verification').css('background-color', '#ddffff');
            $('#success_msg_verification_text').html('Update Successfully');
            setTimeout(() => {
              $('#show').css('display', 'none');
            }, 3000);

          }
        }
      });
    }

  });
  $(document).on('click', '.delete-goal-btn', function(e) {
    e.preventDefault();
    var result = confirm("Está Seguro que quiere Eliminar?");
    if (result) {
      var goalId = $(this).data('id');
      var sectionType = $(this).data('type');
      console.log('goalId', goalId, sectionType);
      var goalIds = [];
      goalIds.push(goalId);
      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          DeleteDailySuperDias: 'DeleteDailySuperDias',
          type: sectionType,
          currentDate: currentDate,
          goalIds: goalIds,
        },
        success: function(data) {
          console.log('data', data, goalIds);
          for (let index = 0; index < goalIds.length; index++) {
            var gid = goalIds[index];
            var goalList = '#' + sectionType + '-goal-list-item-' + gid;
            console.log(goalList, 'goalList');
            $(goalList).remove();
            if (sectionType == 'life') {
              remainingLifeGoals++;
            }
          }

          if (data == 'Deleted') {
            $('#show').css('display', 'block');
            $('#error_success_msg_verification').css('color', '#000000');
            $('#error_success_msg_verification').css('background-color', '#ddffff');
            $('#success_msg_verification_text').html('Update Successfully');
            setTimeout(() => {
              $('#show').css('display', 'none');
            }, 3000);

          }
        }
      });
    }

  });

  $('#editBtn1').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');
    }
    $('#top-goals-area').toggleClass('edit');
  });
  $('#editBtn2').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');

      $("#daily-life-goal-list textarea").each(function() {
        $(this).remove();
      });
      $("#daily-life-goal-list span.lifeGoalText").show();
      $("#daily-life-goal-list .edit-goal-btn .fa-save").addClass('fa-pencil').removeClass('fa-save');

    }
    $('#life-goals-area').toggleClass('edit');

  });

  $(document).on('change', 'input.input-topgoals', function() {
    var checked = $(this).is(':checked');
    var goalId = $(this).val();
    var goalText = $("#topGoalText-" + goalId).text();
    console.log('goalId', goalId, checked, goalText);
    var achieved = 0;
    if (checked) achieved = 1;
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateDailySuperDias: 'UpdateDailySuperDias',
        currentDate: currentDate,
        goalText: goalText,
        achieved: achieved,
        goalId: goalId,
      },
      success: function(data) {
        console.log('data', data);
        if (data == 'Update') {
          $('#show').css('display', 'block');
          $('#error_success_msg_verification').css('color', '#000000');
          $('#error_success_msg_verification').css('background-color', '#ddffff');
          $('#success_msg_verification_text').html('Update Successfully');
          setTimeout(() => {
            $('#show').css('display', 'none');
          }, 3000);

        }
      }
    });
  });
  $(document).on('change', 'input.input-lifegoals', function() {
    var checked = $(this).is(':checked');
    var goalId = $(this).val();
    var goalText = $("#lifeGoalText-" + goalId).text();
    console.log('goalId', goalId, checked, goalText);
    var achieved = 0;
    if (checked) achieved = 1;
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateDailyLifeGoalChecked: 'UpdateDailyLifeGoalChecked',
        currentDate: currentDate,
        achieved: achieved,
        goalId: goalId,
      },
      success: function(data) {
        console.log('data', data);
        if (data == 'Update') {
          $('#show').css('display', 'block');
          $('#error_success_msg_verification').css('color', '#000000');
          $('#error_success_msg_verification').css('background-color', '#ddffff');
          $('#success_msg_verification_text').html('Update Successfully');
          setTimeout(() => {
            $('#show').css('display', 'none');
          }, 3000);

        }
      }
    });
  });

  function UpdateData() {
    var dailyEvolution = window.editors['dailyEvolution'].getData();
    console.log('dailyEvolution html',dailyEvolution);

    var dailyImprovements = window.editors['dailyImprovements'].getData(); 
    const dailyVictory = window.editors['daily_victory'].getData();
    const toRemember = window.editors['to_remember'].getData();
    const dailyVictoryTags = [];
    document.querySelectorAll('.daily-victory-tags').forEach(tag => tag.value.trim() !== '' ? dailyVictoryTags.push(tag.value) : '');
    const toRememberTags = [];
    document.querySelectorAll('.to-remember-tags').forEach(tag => tag.value.trim() !== '' ? toRememberTags.push(tag.value) : '');


    $("#print-evaluation").html(dailyEvolution);
    $("#print-improvements").html(dailyImprovements);

    var goalsData = [];
    var topGoalsData = [];
    var lifeGoalsData = [];
    $('input.input-topgoals').each(function() {
      var checked = $(this).is(':checked');
      var goalId = $(this).data('id');
      var goalText = $("#topGoalText-" + goalId).text();
      var golaItem = {};
      golaItem.id = goalId;
      golaItem.checked = (checked == true) ? 1 : 0;
      golaItem.text = goalText;
      topGoalsData.push(golaItem);
    });


    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateDailyGoals: 'UpdateDailyGoals',
        dailyEvolution: dailyEvolution,
        dailyImprovements: dailyImprovements,
        topGoalsData: topGoalsData,
        currentDate: currentDate,
        dailyVictory: dailyVictory,
        dailyVictoryTags: dailyVictoryTags,
        toRemember: toRemember,
        toRememberTags: toRememberTags
      },
      success: function(data) {
        data = JSON.parse(data);
        if (data.success) {
          showToast('success', 'Update Successfully.');
        } else {
          showToast('error', data.message);
        }
      }
    });


  }

 

  function padTo2Digits(num) {
        return num.toString().padStart(2, '0');
  }
  function getDurationTIme(totalSeconds){
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = Math.floor(totalSeconds % 60);
    const result = `${padTo2Digits(minutes)}:${padTo2Digits(seconds)}`;
    return result;
  }
 
  
  
  $('#saveBtn').click(function() {
    UpdateData();
  });
  $('#floatingSaveBtn').click(function() {
    UpdateData();
  });
  $('#savePrintBtn').click(function() {

    var dailyEvolution = window.editors['dailyEvolution'].getData();
    $("#print-evaluation").html(dailyEvolution);
    var dailyImprovements = window.editors['dailyImprovements'].getData(); 
    $("#print-improvements").html(dailyImprovements);
    window.print();
  });
  



  $(function() {
    $('.daily-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        weekStart: 1
      })
      .on('changeDate', function(e) {
        console.log('changeDate', e.date, e.format('yyyy-mm-dd'));
        window.location.href = SITE_URL + "/users/superdias.php?date=" + e.format('yyyy-mm-dd');



      });
  });
</script>

<?php require_once "inc/footer.php"; ?>