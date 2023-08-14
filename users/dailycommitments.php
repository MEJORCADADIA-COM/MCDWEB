<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php require_once "inc/header.php"; ?>
<?php
if (isset($_GET['timezoneoffset'])) {
  $_SESSION['timezoneOffset'] = $_GET['timezoneoffset'];
  header('Location: dailycommitments.php');
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
$selectedDate = empty($date) ? $today : $date;

$isPastDate = false;
if ($selectedDate < $today) {
  $goalDate = $selectedDate;
  $isPastDate = true;
} else {
  $goalDate = $today;
}


?>
<?php if ($timezoneOffset == '') : ?>
  <script>
    var browserTime = new Date();
    var timezoneOffset = browserTime.getTimezoneOffset();
    window.location.href = "dailycommitments.php?timezoneoffset=" + timezoneOffset;
  </script>
<?php endif; ?>
<?php

$user_id = Session::get('user_id');
$dailyEvolution = '';
$goals = [];


$result = $common->get(
  "daily_commitments_goals",
  'user_id = :user_id AND created_at <= :created_at AND (deleted_at IS NULL OR deleted_at > :deleted_at)',
  ['user_id' => $user_id, 'created_at' => $goalDate, 'deleted_at' => $goalDate]
);
if ($result) {
  foreach ($result as $row) {
    $ansRow = $common->first(
      "daily_commitments_answers",
      'user_id = :user_id AND goal_id = :goal_id AND created_at = :created_at',
      ['user_id' => $user_id, 'goal_id' => $row['id'], 'created_at' => $selectedDate]
    );
    if ($ansRow) {
      $row['answer'] = $ansRow['answer'];
    } else {
      $row['answer'] = 0;
    }
    $goals[] = $row;
  }
}
$description = '';
$row = $common->first("daily_commitments_description", 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $selectedDate]);
if ($row) {
  $description = $row['description'];
}

?>

<script>
  var SITE_URL = '<?= SITE_URL; ?>';
  var selectedDate = '<?= $selectedDate; ?>';
</script>
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<style>
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
      position: fixed;
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
    position: fixed;
    z-index: 1111;
    border-radius: 35px 0px 0px 35px;
  }

  .goals-area ol li {
    font-size: 1rem;
    color: #FFF;
    margin-bottom: 10px;
    padding-right: 4rem;
    position: relative;
  }

  .goals-area ol li label {
    display: inline;
  }

  .goals-area ol li input {
    width: 1.5rem;
    height: 1.5rem;
    position: absolute;
    right: 0;
    top: 25%;
  }

  .goals-area ol li.hidden {
    display: none;
  }

  #new-goal-creation-container .form-group {
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

  .chart-btn {
    margin-left: 10px;
    position: absolute;
    right: 2rem;
    padding: 0rem 0.3rem;
    top: 25%;
  }

  @media print {
    .goals-area ol li.hidden {
      display: list-item;
    }

    .chart-btn {
      display: none;
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

  #life-goals-area:not(.edit) {}

  .admin-dashbord {
    background: #ed008c;
  }

  .projects {
    border: none;
  }

  #goals-area {
    padding: 30px 20px;
  }

  @media (min-width: 1200px) {

    .h2,
    h2 {
      font-size: 1.7rem;
    }
  }

  @media screen and (max-width: 767px) {
    h2.maintitle {
      font-size: 1rem;
    }

    .projects-header h2 {
      font-size: 1.1rem;
    }

    .goals-area ol li {
      padding-right: 2rem;
      min-height: 56px;
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
  }
</style>

<?php



?>
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3">
  <div class="projects mb-4" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        <?php if ($type != 'lifetime') : ?>
          <div class="row" style="margin-bottom:15px;">
            <div class="col-sm-9"></div>
            <div class="col-sm-3">
              <div class="input-group date daily-datepicker datepicker" id="datepicker">
                <?php $df = 'd-m-Y';

                ?>
                <input type="text" class="form-control" value="<?= date($df, strtotime($selectedDate)); ?>" id="date" readonly />
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($selectedDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>

        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/dailycommitments.php?date=<?= date('Y-m-d', strtotime('-1 day', strtotime($selectedDate))); ?>" ;><i class="fa fa-arrow-left"></i></a></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;"><?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?></h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/dailycommitments.php?date=<?= date('Y-m-d', strtotime('+1 day', strtotime($selectedDate))); ?>"><i class="fa fa-arrow-right"></i></a></div>
        </div>

      </header>


      <form class="form" id="goalsFrom">
        <div style="background-color: #fef200; padding: 10px; display:flex;">
          <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden;">Me Comprometo 100% a Hacer Esto:
            <?php if ($isPastDate == false) : ?>
              <button type="button" data-target="goals-area" class="btn btn-xs btn-info screenonly pull-right" id="editBtn1">Editar</button>
            <?php endif; ?>
          </h2>
        </div>
        <div class="cardd mb-4" id="section-1">

          <div class="goals-area" id="goals-area" style="display:block;">
            <ol id="daily-goal-list" class="goal-list">
              <?php foreach ($goals as $key => $item) :  ?>
                <li class="<?= ($key > 9) ? 'hidden more' : ''; ?>" id="top-goal-list-item-<?= $item['id']; ?>">
                  <label id="top-list-label-<?= $item['id']; ?>">

                    <span id="goalText-<?= $item['id']; ?>" class="goalText"><?= $item['goal']; ?> </span>
                    <a href="calandar.php?type=commitment&id=<?= $item['id']; ?>&date=<?= $selectedDate; ?>" class="btn btn-info chart-btn"><i class="fa fa-calendar"></i></a>
                    <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" class="input-goals" name="answers[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['answer'] == 1) echo 'checked'; ?>>
                    <a class="edit-actions edit-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                    <a class="edit-actions delete-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                  </label>
                </li>
              <?php endforeach; ?>
            </ol>
            <?php if (count($goals) > 10) : ?>
              <div class="screenonly" style="text-align:center;"><button id="morelessToggleBtn" type="button" class="btn btn-primary">Mostrar más</button></div>
            <?php endif; ?>
            <div class="form-group" id="new-goal-creation-container"></div>
            <?php if ($isPastDate == false) : ?>
              <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-top-goal-btn-wrapper">
                <button type="button" id="save-new-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewGoals()"><i class="fa fa-save"></i> Guarda Nuevo Compromiso</button>
                <button type="button" class="button btn btn-info" onClick="CreateNewCommitments()"><i class="fa fa-book"></i> Agrega Compromiso</button>
              </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="cardd mb-4" id="section-2" style="padding:0 5px;">
          <h5 class="card-header" style="color:#FFF; font-size:1.5rem; margin:5px 0;">Evaluación y Mejoramiento:</h5>
          <div class="card-body">
            <div class="form-group">
              <div class="description-area">
                <div class="print-description" id="print-evaluation"><?= $description; ?></div>
                <textarea id="dailyEvolution" class="LetterApplication" name="dailyEvolution"><?= $description; ?></textarea>
              </div>
            </div>
          </div>
        </div>


        <div style="display: none;" id="show">
          <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
            <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>
          </div>
        </div>
        <div class="form-group screenonly">
          <div class="button-wrapper" style="margin:30px 0;">
            <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>

            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />

            <!-- Floating Save Start -->
            <?php if ($today <= $selectedDate) : ?>
              <input class="btn btn-info letter" type="button" id="saveBtn" name="saveBtn" value="Guardar" />
              <div>
                <button class="btn btn-primary rounded-circle fixed-save-btn text-white" type="button" id="floatingSaveBtn" name="saveBtn"><i class="fa fa-save"></i></button>
              </div>
            <?php endif; ?>
            <!-- Floating Save End -->

          </div>
        </div>
      </form>



    </div>
  </div>
  <div class="clearfix;"></div>
</main>
<!-- Modal -->

<div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalToggleLabel">Send Email</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Receiver Email Address</label>
          <input style="width:100%;" type="email" class="form-control" name="toemail" id="toEmail" placeHolder="Enter Email Address">
        </div>
      </div>
      <div class="modal-footer">
        <div id="modal-msg"></div>
        <button class="btn btn-primary" type="button" id="sendBtn" name="sendBtn">Send Email</button>
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
  $('#show').css('display', 'none');
  document.querySelectorAll( '.LetterApplication' ).forEach( ( node, index ) => {  
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
  

  var goalstobeadded = 0;
  var newgoalsInput = [];


  function SaveNewLifeGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newlifegoals");
    var validated = hasFilledNewGoals('newlifegoals');
    if (newgoalsInput.length > 0) {
      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          SaveNewDailyLifeGoals: 'SaveNewDailyLifeGoals',
          selectedDate: selectedDate,
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-life-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);


              $("#daily-life-goal-list").append('<li class="" id="life-goal-list-item-' + prop + '"><label class="form-label" id="life-list-label-' + prop + '"><span class="lifeGoalText" id="lifeGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="lifeAchieved[' + prop + ']" class="input-lifegoals" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-type="life" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-type="life" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-life-goals-btn').hide();
          }
        }
      });
    }
  }

  function SaveNewGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newgoals");
    var validated = hasFilledNewGoals('newgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'SaveNewDailyCommitments',
          selectedDate: selectedDate,
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);
              $("#daily-goal-list").append('<li class="" id="top-goal-list-item-' + prop + '"><label class="form-label" id="top-list-label-' + prop + '"><span id="topGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="achieved[' + prop + ']" class="input-topgoals" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-type="top" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
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
  var createdNewBox = 0;

  function CreateNewCommitments() {
    $wrapper = $('#new-goal-creation-container');
    var validated = hasFilledNewGoals('newgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newgoals");
    var addedGoals = $("ol#daily-goal-list").children().length;
    var totalAllowed = 20;
    remainingTopGoals = totalAllowed - addedGoals - createdNewBox;
    console.log(remainingTopGoals, addedGoals);
    if (validated) {
      if (remainingTopGoals > 0) {
        $wrapper.append("<div class='form-group'><textarea placeholder='Escribe detalles' class='form-input form-control newgoals' name='newgoals[]'/></textarea></div>");
        $('#save-new-goals-btn').show();
        createdNewBox++;
      } else {
        showToast('error', 'Maximum goal limit reached.');
      }
    } else {
      showToast('error', 'Please fill in the already added box first.');
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

    var goalId = $(this).data('id');

    var goalTextElem = $('#goalText-' + goalId);


    goalText = goalTextElem.text();
    console.log('goalText', goalText);
    if ($(this).find('.fa').hasClass('fa-pencil')) {
      $(this).find('.fa').removeClass('fa-pencil');
      $(this).find('.fa').addClass('fa-save');
      $(this).addClass('save');
      goalTextElem.hide();
      var containterItemId = 'top-list-label-' + goalId;
      $("#" + containterItemId).append('<textarea id="edittextarea-' + goalId + '">' + goalText + '</textarea>');
    } else {
      $(this).removeClass('save');
      var checkedboxClass = '.input-goals';
      var checked = $(this).find(checkedboxClass).is(':checked');
      $(this).find('.fa').addClass('fa-pencil');
      $(this).find('.fa').removeClass('fa-save');
      goalTextElem.show();
      var textareaElem = $('#edittextarea-' + goalId);
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
          action: "UpdateDailyCommitment",
          selectedDate: selectedDate,
          goalText: goalText,
          achieved: achieved,
          goalId: goalId,
          edit: 1,
        },
        success: function(data) {
          showToast('success', 'Update Successfully.');


        }
      });

    }


  });
  $(document).on('click', '.delete-goal-btn', function(e) {
    e.preventDefault();
    var result = confirm("Está Seguro que quiere Eliminar?");
    if (result) {
      var goalId = $(this).data('id');
      var sectionType = 'top';
      console.log('goalId', goalId, sectionType);
      var goalIds = [];
      goalIds.push(goalId);
      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'UpdateDailyCommitment',
          type: sectionType,
          selectedDate: selectedDate,
          goalIds: goalIds,
          delete: 1,
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

      $("#daily-goal-list textarea").each(function() {
        $(this).remove();
      });
      $("#daily-goal-list span.goalText").show();
      $("#daily-goal-list .edit-goal-btn .fa-save").addClass('fa-pencil').removeClass('fa-save');

    }
    var target = $(this).data('target');
    $('#' + target).toggleClass('edit');

  });

  $('input.input-goals').change(function() {
    var checked = $(this).is(':checked');
    var goalId = $(this).val();
    console.log('goalId', goalId, checked);
    var achieved = 0;
    if (checked) achieved = 1;
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        action: 'UpdateDailyCommitmentAnswer',
        selectedDate: selectedDate,
        answer: achieved,
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
  $('input.input-lifegoals').change(function() {
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
        selectedDate: selectedDate,
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

  function UpdateDailyGoals() {
    var dailyEvolution = window.editors['dailyEvolution'].getData();

    $("#print-evaluation").html(dailyEvolution);

    var goalsData = [];



    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        action: 'UpdateDailyCommitments',
        dailyEvolution: dailyEvolution,
        selectedDate: selectedDate,
      },
      success: function(data) {
        showToast('success', 'Update Successfully.');
      }
    });


  }


  $('#saveBtn').click(function() {
    UpdateDailyGoals();
  });
  $('#floatingSaveBtn').click(function() {
    UpdateDailyGoals();
  });
  $('#savePrintBtn').click(function() {
    var dailyEvolution = window.editors['dailyEvolution'].getData();
    $("#print-evaluation").html(dailyEvolution);

    window.print();
  });
  $('#sendBtn').click(function() {
    var self = $(this);
    var btnText = $(this).text();
    $("#modal-msg").html('');
    $("#toEmail").parent().removeClass('has-errors');
    var toEmail = $("#toEmail").val();
    console.log('toEmail', toEmail);
    var dailyEvolution = window.editors['dailyEvolution'].getData();
    $("#print-evaluation").html(dailyEvolution);

    if (toEmail && toEmail != '') {
      $(this).text('Sending...');

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'EmailSendDailyCommitment',
          selectedDate: selectedDate,
          dailyEvolution: dailyEvolution,
          toEmail: toEmail,
        },
        success: function(data) {
          $("#sendBtn").text(btnText);
          //console.log(data);
          if (data == 'Insert') {
            $("#modal-msg").html('<label class="danger">Email Sent Successfully</label>')
            setTimeout(() => {
              $("#modal-msg").html('');
            }, 1000);
            $('#exampleModalToggle').modal('hide');
            $('#show').css('display', 'block');
            $('#error_success_msg_verification').css('color', '#000000');
            $('#error_success_msg_verification').css('background-color', '#ddffff');
            $('#success_msg_verification_text').html('Email Sent Successfully');
            setTimeout(() => {
              $('#show').css('display', 'none');
            }, 3000);

          } else {
            $("#modal-msg").html('<label class="danger">' + data + '</label>')

            setTimeout(() => {
              $("#modal-msg").html('');
            }, 3000);
          }
        }
      });
    } else {
      $("#toEmail").parent().addClass('has-errors');
    }

  });
  $('#morelessToggleBtn').click(function() {
    console.log($(this).text());
    $("#daily-goal-list li.more").toggleClass("hidden");
    if ($(this).text() == 'Mostrar más') {
      $(this).text('Mostrar Menos');
    } else {
      $(this).text('Mostrar más');
    }
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
        window.location.href = SITE_URL + "/users/dailycommitments.php?date=" + e.format('yyyy-mm-dd');



      });
  });
</script>
<div id="popovertip" data-page="dailycommitments" data-bs-custom-class="mejor-info-popover" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Escribe las Acciones que estás Comprometido/a a Realizar Hoy y todos los Días. La Regularidad y Consistencia es tan Importante como la Genialidad. Si puedes."></div>
<?php require_once "inc/footer.php"; ?>