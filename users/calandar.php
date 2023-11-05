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
$type = 'commitment';
$date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : '';
$goalId = !empty($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$selectedDate = empty($date) ? $today : $date;
$selectedMonth = date('m', strtotime($selectedDate));
$selectedYear = date('Y', strtotime($selectedDate));

$ym = date('Y-m', strtotime($selectedDate));




?>
<?php if ($timezoneOffset == '') : ?>
  <script>
    var browserTime = new Date();
    var timezoneOffset = browserTime.getTimezoneOffset();
    window.location.href = "dailycommitments.php?timezoneoffset=" + timezoneOffset;
  </script>
<?php endif; ?>
<?php

$user_id = $user_infos['id'];
$answers = [];
for ($i = 0; $i < 32; $i++) {
  $answers[$i] = 0;
}
$goalDescription = '';
$row = $common->first("daily_commitments_goals", 'user_id = :user_id AND id = :id', ['user_id' => $user_id, 'id' => $goalId]);
if ($row) {
  $goalDescription = $row['goal'];
}
$resultAns = $common->get(
  'daily_commitments_answers',
  'user_id = :user_id AND goal_id = :goal_id AND MONTH(created_at) = :month',
  ['user_id' => $user_id, 'goal_id' => $goalId, 'month' => $selectedMonth],
  ['answer', 'DAY(created_at) as d']
);
if ($resultAns && count($resultAns) > 0) {
  foreach ($resultAns as $row) {
    $answers[$row['d']] = $row['answer'];
  }
}
// Check format
$timestamp = strtotime($ym . '-01');  // the first day of the month

$day_count = date('t', $timestamp);

// 1:Mon 2:Tue 3: Wed ... 7:Sun
$str = date('N', $timestamp);

// Array for calendar
$weeks = [];
$week = '';

// Add empty cell(s)
$week .= str_repeat('<td></td>', $str - 1);

for ($day = 1; $day <= $day_count; $day++, $str++) {

  $date = $ym . '-' . $day;
  $checked = ($answers[$day] == 1) ? 'checked' : '';
  if ($today == $date) {
    $week .= '<td class="today ' . $checked . '">';
  } else {
    $week .= '<td class="' . $checked . '">';
  }
  $week .= '<span>' . $day . '</span></td>';

  // Sunday OR last day of the month
  if ($str % 7 == 0 || $day == $day_count) {

    if ($day == $day_count && $str % 7 != 0) {
      $week .= str_repeat('<td></td>', 7 - $str % 7);
    }
    $weeks[] = '<tr>' . $week . '</tr>';
    $week = '';
  }
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

  }

  @media screen and (min-width: 600px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
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

  }

  @media screen and (min-width: 992px) {
    .tox-notifications-container {
      display: none !important;
    }

    .letter {
      float: right;
      margin: 15px 10px 15px 10px;
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

  }

  @media screen and (max-width: 767px) {
    h2.maintitle {
      font-size: 1.3rem;
    }

    .projects-header h2 {
      font-size: 1.1rem;
    }
  }

  .goals-area ol li {
    font-size: 1.4rem;
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

  @media (min-width: 1200px) {

    .h2,
    h2 {
      font-size: 1.7rem;
    }
  }

  .calendar td.day {
    padding: 20px 20px;
  }

  .calendar thead {
    background-color: #fef200;
    border-bottom: 1px solid #e4c566;
  }

  .calendar tbody {
    background-color: #FFF;
  }

  .calendar td,
  .calendar th {
    text-align: center;
    padding: 15px 0;
  }

  .calendar th {
    font-weight: bold;
    font-size: 1rem;
  }

  .calendar td span {
    display: block;
    width: 40px;
    margin: 0 auto;
    height: 40px;
    vertical-align: middle;
    line-height: 40px;
  }

  .calendar td.checked span {
    border: 1px solid #447a20;
    background: #74be41;
    border-radius: 50%;
    color: #FFF;
  }

  table {
    border-collapse: collapse;
    border-style: hidden;
    /* hide standard table (collapsed) border */
    /* this draws the table border  */
  }

  td {
    border: 1px solid #ccc;
  }

  /* top-left border-radius */
  table tr:first-child th:first-child {
    border-top-left-radius: 6px;
  }

  /* top-right border-radius */
  table tr:first-child th:last-child {
    border-top-right-radius: 6px;
  }

  /* bottom-left border-radius */
  table tr:last-child td:first-child {
    border-bottom-left-radius: 6px;
  }

  /* bottom-right border-radius */
  table tr:last-child td:last-child {
    border-bottom-right-radius: 6px;
  }

  .projects-header {
    padding: 0;
  }
</style>

<?php



?>
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 ">
<?php require_once 'inc/secondaryNav.php'; ?>
  <div class="projects mb-4" style="background-color: #ed008c;">
    <div class="projects-inner" style="padding:10px 20px;">
      <header class="projects-header">

        <?php setlocale(LC_ALL, $locales[$userLanguage]);
        $string = date('d/m/Y', strtotime($selectedDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
        <div class="row mb-3">
          <div class="col-sm-12 col-12" style="text-align:left;"><button class="btn btn-warning" onclick="history.back()"> <?=translate('Atrás');?></button></div>
        </div>
        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/calandar.php?type=commitment&id=<?= $goalId; ?>&date=<?= date('Y-m-d', strtotime('-1 month', strtotime($selectedDate))); ?>" ;><i class="fa fa-arrow-left"></i></a></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;"><?= utf8_encode(strftime("%B %Y", $dateObj->getTimestamp())); ?></h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/calandar.php?type=commitment&id=<?= $goalId; ?>&date=<?= date('Y-m-d', strtotime('+1 month', strtotime($selectedDate))); ?>"><i class="fa fa-arrow-right"></i></a></div>
        </div>

      </header>
      <div class="mt-1 mb-2" style="color:#FFF; font-size:1rem;"><?= $goalDescription; ?></div>
      <table class="table table-borderded calendar">
        <thead>
          <tr>
            <th>L</th>
            <th>M</th>
            <th>M</th>
            <th>J</th>
            <th>V</th>
            <th>S</th>
            <th>D</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($weeks as $week) {
            echo $week;
          }
          ?>
        </tbody>
      </table>
      <?php



      ?>


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

  function CreateNewCommitments() {
    $wrapper = $('#new-goal-creation-container');
    var validated = hasFilledNewGoals('newgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newgoals");
    var addedGoals = $("ol#daily-goal-list").children().length;
    var totalAllowed = 22;
    remainingTopGoals = totalAllowed - addedGoals;
    console.log(remainingTopGoals, addedGoals);
    if (validated) {
      if (remainingTopGoals > 0) {
        $wrapper.append("<div class='form-group'><textarea placeholder='Escribe detalles' class='form-input form-control newgoals' name='newgoals[]'/></textarea></div>");
        $('#save-new-goals-btn').show();
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
          EmailSendDailyGoal: 'EmailSendDailyCommitment',
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
</script>
<?php require_once "inc/footer.php"; ?>