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
    window.location.href = "dailygoals.php?timezoneoffset=" + timezoneOffset;
  </script>
<?php endif; ?>
<?php

$user_id = Session::get('user_id');
$dailyEvolution = '';
$dailyImprovements = '';

$dailyTopGoals = [];
$dailyLifeGoals = [];
$dailyImportantGoals = [];

$dailyV7Files=[];

$dailyV7Files = $common->get('uploaded_files', 'user_id = :user_id AND DATE(created_at) = :created_at', ['user_id' => $user_id, 'created_at' => $currentDate]);

$dailyV7Images=[];
$dailyV7Audios=[];
$dailyV7Videos=[];

foreach ($dailyV7Files as $file) {
 if($file['type']=='audio'){
  $dailyV7Audios[]=$file;
 }
 if($file['type']=='video'){
  $dailyV7Videos[]=$file;
 }
 if($file['type']=='image'){
  $dailyV7Images[]=$file;
 }
}


$row = $common->first('dailygaols', 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $currentDate]);
$dailyEvolutionRow=null;
if ($row) {
  $dailyEvolutionRow=$row;
  $dailyEvolution = $row['evolution'];
  $dailyImprovements = $row['improvements'];
}

$dailyVictory = $common->first(
  'daily_victories',
  'user_id = :user_id AND date = :date',
  ['user_id' => $user_id, 'date' => $currentDate]
);

if ($dailyVictory) {
  $dailyVictoryTags = $common->leftJoin(
    'daily_victory_user_tag',
    'user_tags',
    'daily_victory_user_tag.user_tag_id = user_tags.id',
    'daily_victory_user_tag.daily_victory_id = :victory_id',
    ['victory_id' => $dailyVictory['id']],
    ['tag','daily_victory_user_tag.id'],
    'daily_victory_user_tag.id'
  );
  
}

$toRemember = $common->first(
  'to_remember',
  'user_id = :user_id AND date = :date',
  ['user_id' => $user_id, 'date' => $currentDate]
);
$evolutionTags=[];
if ($dailyEvolution) {
  $evolutionTags = $common->leftJoin(
    'evolution_user_tag',
    'user_tags',
    'evolution_user_tag.user_tag_id = user_tags.id',
    'evolution_user_tag.evolution_id = :evolution_id',
    ['evolution_id' => $dailyEvolutionRow['id']],
    ['tag','evolution_user_tag.id'],
    'evolution_user_tag.id'
  );
}
$dailyImportantGoals = $common->get('daily_important_goals', "user_id = :user_id AND created_at = :created_at", ['user_id' => $user_id, 'created_at' => $currentDate]);

$dailyTopGoals = $common->get('daily_top_goals', "user_id = :user_id AND created_at = :created_at", ['user_id' => $user_id, 'created_at' => $currentDate]);

$selectedDate = $currentDate;

$isPastDate = false;


if ($selectedDate < $today) {
  $goalDate = $selectedDate;
  $isPastDate = true;
} else {
  $goalDate = $today;
}
$isPastDate=false;

$dailyLifeGoals = $common->get("dailylifegoals", "user_id = :user_id AND created_at <= :created_at", ['user_id' => $user_id, 'created_at' => $goalDate]);
if ($dailyLifeGoals) {
  foreach ($dailyLifeGoals as &$row) {
    $goalCheck = $common->first(
      table: "dailylifegoals_marked",
      cond: "goal_id = :goal_id AND user_id = :user_id AND created_at <= :created_at",
      params: ['goal_id' => $row['id'], 'user_id' => $user_id, 'created_at' => $currentDate],
      orderBy: 'id',
      order: 'DESC'
    );
    if ($goalCheck) {
      $row['achieved'] = $goalCheck['checked'];
    } else {
      $row['achieved'] = 0;
    }
  }
}

?>


<script>
  var SITE_URL = '<?= SITE_URL; ?>';
  var topGoalsCounts = '<?= count($dailyTopGoals); ?>';
  var lifeGoalsCounts = '<?= count($dailyLifeGoals); ?>';
  var importantGoalsCount='<?=count($dailyImportantGoals);?>';
  var currentDate = '<?= $currentDate; ?>';
  var remainingTopGoals = 7 - topGoalsCounts;
  var remainingLifeGoals = 7 - lifeGoalsCounts;
  var remainingImportantGoals=1-importantGoalsCount;
</script>
<link rel="stylesheet" href="<?=SITE_URL; ?>/users/assets/uikit-lightbox.css" />
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
<script src="<?=SITE_URL; ?>/users/dist/recorder.js"></script>

<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit-icons.min.js"></script>

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
      bottom: 35px;
      right: 5px;
    }

    .appointment-list>li .edit-actions,
    .to-be-done-list>li .edit-actions,
    .income-expense-list>li .edit-actions,
    .note-list>li .edit-actions {
      display: inline;
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

    .appointment-list>li .edit-actions,
    .to-be-done-list>li .edit-actions,
    .income-expense-list>li .edit-actions,
    .note-list>li .edit-actions {
      display: inline;
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

    .appointment-list>li .edit-actions,
    .to-be-done-list>li .edit-actions,
    .income-expense-list>li .edit-actions,
    .note-list>li .edit-actions {
      display: none;
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

  .appointment-list>li:hover .edit-actions,
  .to-be-done-list>li:hover .edit-actions,
  .income-expense-list>li:hover .edit-actions,
  .note-list>li:hover .edit-actions {
    display: inline;
  }

  .text-bottom-border,
  .text-bottom-border:focus {
    border: none;
    border-bottom: 1px solid white;
    outline: none;
  } 
  
  .v7-media-box{
    position:relative;
  }
  .v7-media-box.file-added .upload-file-box{
    display:none;
  }
  .upload-file-box{   
    cursor:pointer;
  }



  .v7-media-box img{
    height:120px; width:120px;
    border-radius: 10px;
  }
  .v7-media-box video{max-width:100%; width:180px; height:150px;}

.v7-media-box audio, .v7-media-box video{max-width:100%; max-height:180px;}
.file-actions{
  position:absolute; top:0; right:0;
}
#audioMediaBox.has-audio-file .file-actions{
  position:absolute; top:13px; right:0px;
}
.media-thumb-wrapper{position: relative;}
.jquery-uploader-preview-progress{
  position: absolute;
    width: 64px;
    height: 64px;
    top: calc(50% - 32px);
    left: calc(50% - 32px);
    background: #d2cdcd99;
    border-radius: 50%;
    text-align: center;
    padding: 14px;
    border: 1px solid #656565;
}
 .progress-loading .fa{
   
     font-size:1.5rem;
    
}
.media-thumb-wrapper .preview-thumb{
  border: 1px dotted #aeacac;
    padding: 2.2rem; 
    border-radius: 10px;
    background: #f7f7f7;
    height: 104px;
    display: block;
    width: 104px;
}
.media-thumb-wrapper .preview-thumb .fa {
    cursor: pointer;
    font-size: 2rem;
}
@media screen and (max-width: 767px) {
  .upload-file-box > label{
    margin: 0 auto;
  }
}
.inputfile {
	width: 0.1px;
	height: 0.1px;
	opacity: 0;
	overflow: hidden;
	position: absolute;
	z-index: -1;
}
.inputfile + label {
    font-size: 1.25em;
    font-weight: 700;
    color: #000;
    display: inline-block;
    border: 1px dotted #aeacac;
    padding: 2.2rem;
    border-radius: 10px;
    background: #f7f7f7;
}
.inputfile + label .fa {
    cursor: pointer;
    font-size: 2rem;
}

.inputfile:focus + label,
.inputfile + label:hover {
    background-color: #e2e2e2;
}
.inputfile + label {
	cursor: pointer; /* "hand" cursor */
}
.inputfile:focus + label {
	outline: 1px dotted #000;
	outline: -webkit-focus-ring-color auto 5px;
}
.inputfile + label * {
	pointer-events: none;
}
#audio-recorder .elapsed-time{
  font-size:2.2rem;
}
#audio-recorder .max-duration-label{
  font-size:1rem;
  margin-bottom:2rem;
}
.audio-player-preview audio{
  display:block;
  margin-bottom:1rem;
}
.audio-player-preview a.btn{
  margin:0.375rem 0.75rem;
}
                        .custom-audio-picker button{
                          font-size: 1.25em;
                          font-weight: 700;
                          color: 000;
                          display: inline-block;
                          border: 1px dotted #aeacac;
                          padding: 2.2rem;
                          border-radius: 10px;
                          background: #f7f7f7;
                        }
                        
                        .custom-audio-picker button:hover, .custom-audio-picker button.btn.show, .custom-audio-picker button.btn:active{
                          background:#e2e2e2;
                        }
                        .custom-audio-picker button .fa {
                              cursor: pointer;
                              font-size: 2rem;
                              color:#000;
                          }
                          .custom-audio-picker .dropdown-menu .inputfile + label, .custom-audio-picker .dropdown-menu .dropdown-item{
                            border:none; 
                            width:100%;
                            color: #000;
                            background-color: transparent;
                            padding: 0.25rem 0.75rem;
                            font-weight: 400;
                            font-size: 1rem;
                          }
                          .custom-audio-picker .dropdown-menu .fa{
                            font-size:1rem;
                            width:16px;
                          }
                        
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3">

  <?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        <?php if ($type != 'lifetime') : ?>
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
        <?php endif; ?>
        <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($currentDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/dailygoals.php?date=<?= date('Y-m-d', strtotime('-1 day', strtotime($currentDate))); ?>" ;><i class="fa fa-arrow-left"></i></a></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;"><?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?></h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/dailygoals.php?date=<?= date('Y-m-d', strtotime('+1 day', strtotime($currentDate))); ?>"><i class="fa fa-arrow-right"></i></a></div>
        </div>

      </header>
    
        <div>
          <button class="btn btn-primary rounded-circle fixed-save-btn text-white" type="button" id="floatingSaveBtn" name="saveBtn"><i class="fa fa-save fa-lg"></i></button>
        </div>
     

      <!-- Slider Start -->
      <div class="d-flex w-100 slider">
        <div class="pt-5" id="slide-1">
          <form class="form" id="goalsFrom">
            <div class="mt-5" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; ">7-Objetivos y Prioridades Hoy:
                <?php if ($isPastDate == false) : ?>
                  <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn1">Editar</button>
                <?php endif; ?>
              </h2>
            </div>
            <div class="cardd mb-4" id="section-1">

              <div class="goals-area" id="top-goals-area" style="display:block; ">
                <ol id="daily-top-goal-list" class="goal-list">
                  <?php foreach ($dailyTopGoals as $key => $item) :  ?>
                    <li class="<?= ($key > 9) ? 'hidden more' : ''; ?>" id="top-goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                      <label id="top-list-label-<?= $item['id']; ?>">

                        <span style="font-size: 1rem;" id="topGoalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                        <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" class="input-topgoals" name="topAchieved[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['achieved'] == 1) echo 'checked'; ?>>
                        <a class="edit-actions edit-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                        <a class="edit-actions delete-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                      </label>
                    </li>
                  <?php endforeach; ?>
                </ol>

                <div class="form-group" id="new-top-goal-creation-container"></div>
                <?php if (count($dailyTopGoals) < 7) : ?>
                  <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-top-goal-btn-wrapper">

                    <button type="button" id="save-new-top-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewTopGoals()"><i class="fa fa-save"></i> Guarda Nuevo Objetivo</button>

                    <button type="button" class="button btn btn-info" onClick="CreateDailyTopGoal()"><i class="fa fa-book"></i> Agrega Objetivo</button>

                  </div>
                <?php endif; ?>
              </div>
            </div>
            <div class="mt-5" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; ">La Acción o Resultado Más Importante Hoy:
                <?php if ($isPastDate == false) : ?>
                  <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="impEditBtn">Editar</button>
                <?php endif; ?>
              </h2>
            </div>
            <div class="cardd mb-4" id="section-1">

            <div class="goals-area" id="important-goals-area" style="display:block; ">
              <ol id="daily-important-goal-list" class="goal-list">
                <?php foreach ($dailyImportantGoals as $key => $item) :  ?>
                  <li class="<?= ($key > 9) ? 'hidden more' : ''; ?>" id="important-goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                    <label id="important-list-label-<?= $item['id']; ?>">

                      <span style="font-size: 1rem;" id="importantGoalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                      <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" class="input-importantgoals" name="importantAchieved[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['achieved'] == 1) echo 'checked'; ?>>
                      <a class="edit-actions edit-goal-btn" data-type="important" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                      <a class="edit-actions delete-goal-btn" data-type="important" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </label>
                  </li>
                <?php endforeach; ?>
              </ol>

              <div class="form-group" id="new-important-goal-creation-container"></div>
              <?php if (count($dailyImportantGoals) < 1) : ?>
                <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-top-goal-btn-wrapper">

                  <button type="button" id="save-new-important-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewImpGoals()"><i class="fa fa-save"></i> Guarda Resultado</button>

                  <button type="button" class="button btn btn-info" onClick="CreateDailyImportantGoal()"><i class="fa fa-book"></i> Resultado</button>

                </div>
              <?php endif; ?>
            </div>
          </div>
            <div class="cardd mb-5" id="section-2" style="padding:0 5px;">
            <div class="d-flex justify-content-between my-1">
              <h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Mini Resumen de Hoy:</h5>
              <a href="<?= SITE_URL; ?>/users/evolutions.php" class="bg-primary py-1 px-2 rounded border border-primary text-white text-decoration-none">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-evaluation"><?= $dailyEvolution; ?></div>
                    <textarea id="dailyEvolution" rows="5" class="LetterApplication editor ckeditor" name="dailyEvolution"><?= $dailyEvolution; ?></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="row px-1 mb-5">
              <h5 class="" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Escribre 3 Etiquetas para facilitar la busqueda
                :</h5>
              <?php for ($i = 0; $i < 3; $i++) : ?>
                <div class="col-md-4 my-2">
                  <input type="text" class="form-control evaluation-tags" placeholder="Tag" name="evaluation_tag_1" value="<?= $evolutionTags[$i]['tag'] ?? '' ?>">
                </div>
              <?php endfor; ?>
            </div>

            <div class="cardd my-5" id="section-2" style="padding:0 5px;">
              <div class="d-flex justify-content-between my-1">
                <h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Mi Mayor Victoria Hoy:</h5>
                <a href="<?= SITE_URL; ?>/users/dailyVictories.php" class="bg-primary py-1 px-2 rounded border border-primary text-white text-decoration-none">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-daily-victory"><?= $dailyVictory['daily_victory'] ?? ''; ?></div>
                    <textarea id="daily_victory" class="LetterApplication editor ckeditor" name="daily_victory"><?= $dailyVictory['daily_victory'] ?? ''; ?></textarea>
                  </div>
                </div>
              </div>
            </div>

            <div class="row px-1 mb-3">
              <h5 class="" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Escribre 3 Etiquetas para facilitar la busqueda
                :</h5>
              <?php for ($i = 0; $i < 3; $i++) : ?>
                <div class="col-md-4 my-2">
                  <input type="text" class="form-control daily-victory-tags" placeholder="Tag" name="tag_1" value="<?= $dailyVictoryTags[$i]['tag'] ?? '' ?>">
                </div>
              <?php endfor; ?>
            </div>


            <div class="cardd my-5" id="section-2" style="padding:0 5px;">
              <div class="d-flex justify-content-between my-1">
                <h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Notas:</h5>
                <a href="<?= SITE_URL; ?>/users/toRemember.php" class="bg-primary py-1 px-2 rounded border border-primary text-white text-decoration-none">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-to-remember"><?= $toRemember['to_remember'] ?? ''; ?></div>
                    <textarea id="to_remember" class="LetterApplication" name="to_remember"><?= $toRemember['to_remember'] ?? ''; ?></textarea>
                  </div>
                </div>
              </div>
            </div>

           

            <div class="cardd mb-5" id="section-3" style="padding:0 5px;">
             
              <div class="d-flex justify-content-between my-1">
              <h5 class="card-header" style="color:#FFF; margin:5px 0; font-size: 1rem;">¿Cómo Puedo Mejorar?: </h5>
                <a href="<?= SITE_URL; ?>/users/improvements.php" class="bg-primary py-1 px-2 rounded border border-primary text-white text-decoration-none">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-improvements"><?= $dailyImprovements; ?></div>
                    <textarea id="dailyImprovements" class="LetterApplication" name="dailyImprovements"><?= $dailyImprovements; ?></textarea>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="cardd mb-5" id="media-section" style="padding:0 5px; margin-left:5px; margin-right:5px;">
              <div class="card-body">
                
           
              
              <div class="d-flex justify-content-end my-1">              
                <a class="btn btn-sm button btn-info pull-right" href="<?= SITE_URL; ?>/users/victory-images.php?date=<?=$currentDate;?>">Más<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

              </div>
              <div class="d-flex flex-wrap bd-highlight mb-3" uk-lightbox="animation: slide">
                <?php for($i=0; $i<7;$i++): $loopImage=null;
                if(!empty($dailyV7Images) && isset($dailyV7Images[$i])){
                  $loopImage=$dailyV7Images[$i];
                }
                ?>
                  
                  <div class="p-1 bd-highlight v7-media-box <?=$loopImage!=null? 'file-added':''; ?>" id="mediabox<?=$i+1;?>" >
                      <?php if($loopImage!=null):  ?>                      
                        <div class="media-thumb-wrapper" data-file="<?=$loopImage['id'];?>" id="fileid-<?=$loopImage['id'];?>">
                          <a href="<?=$loopImage['url'];?>" id="lightbox-thumb-item-<?=$i;?>" data-index="<?=$i;?>" > <img class="rounded-3"  src="<?=$loopImage['thumb'];?>"></a>
                          <div class="file-actions">
                          <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><div class="dropdown-item file_delete">Delete</div></li>
                            </ul>
                          </div>
                          
                        </div>
                        </div>                      
                      <?php endif; ?>
                      <div class="upload-box-image upload-file-box" data-type="image">                        
                        <input type="file" name="image<?=$i+1;?>File" id="image<?=$i+1;?>File" class="inputfile" accept="image/png, image/gif, image/jpeg"  />
                        <label for="image<?=$i+1;?>File"><i class="fa fa-camera"></i></label>
                      </div>
                     
                </div>
                <?php endfor; ?>
                
                
               
              </div>
              <hr>
              <div class="d-flex bd-highlight mb-3">
                <div class="p-1 bd-highlight">

                <div class="v7-media-box <?=count($dailyV7Audios)>0? 'file-added has-audio-file':''; ?>" id="audioMediaBox">
                      <?php if(!empty($dailyV7Audios) && count($dailyV7Audios)>0):  ?>                      
                        <div class="media-thumb-wrapper" data-file="<?=$dailyV7Audios[0]['id'];?>" id="fileid-<?=$dailyV7Audios[0]['id'];?>">
                        <audio controls src="<?=$dailyV7Audios[0]['url'];?>">
                              <a href="<?=$dailyV7Audios[0]['url'];?>">
                                  Download audio
                              </a> </audio>
                              <div class="file-actions">
                              <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item file_delete" href="#">Delete</a></li>
                            </ul>
                          </div>
                        </div>
                        </div>                      
                      <?php endif; ?>
                      
                      <!--  <div class="upload-box-image upload-file-box" data-type="audio">
                        <input type="file" name="audio1File" id="audio1File" class="inputfile" accept="audio/mp3,audio/x-m4a,audio/*;capture=microphone" />
                        <label for="audio1File"><i class="fa fa-file-audio-o"></i></label>
                      </div>   -->
                      
                      <div class="dropdown custom-audio-picker upload-box-image upload-file-box" data-type="audio">                        
                        <button class="btn btn-secondary " type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-file-audio-o"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li>
                          <input type="file" name="audio12File" id="audio12File" class="inputfile dropdown-item" accept="audio/mp3,audio/x-m4a,audio/*;capture=microphone" />
                            <label for="audio12File"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Upload</label>
                          </li>
                          <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#audioRecorderModal"><i class="fa fa-microphone" aria-hidden="true"></i> Record</a></li>
                        </ul>
                      </div>
                      <!--     -->
                    </div> 
                </div>
                <div class="ms-auto p-1 bd-highlight">
                <a class="btn btn-sm button btn-info pull-right" href="<?= SITE_URL; ?>/users/victory-media.php?type=audio&date=<?=$currentDate;?>">Más<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

                </div>
              </div>
               <hr>
               <div class="d-flex bd-highlight mb-3">
                  <div class="p-1 bd-highlight">

                  <div class="v7-media-box <?=count($dailyV7Videos)>0? 'file-added':''; ?>" id="videoMediabox4">
                      <?php if(!empty($dailyV7Videos) && count($dailyV7Videos)>0):  
                      $poster=''; 
                        if(!empty($dailyV7Videos[0]['thumb'])){ $poster='poster="'.$dailyV7Videos[0]['thumb'].'"'; } ?>                      
                        <div class="media-thumb-wrapper" data-file="<?=$dailyV7Videos[0]['id'];?>" id="fileid-<?=$dailyV7Videos[0]['id'];?>">
                        <video controls preload="metadata" <?=$poster;?>>
                        <source src="<?=$dailyV7Videos[0]['url'];?>#t=0.2" type="video/mp4">
                        Your browser does not support the video tag.
                      </video>
                      <div class="file-actions">
                      <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item file_delete" href="#">Delete</a></li>
                            </ul>
                          </div>
                        </div>
                        </div>                      
                      <?php endif; ?>
                      <div class="upload-box-image upload-file-box" data-type="video">                        
                        <input type="file" name="video1File" id="video1File" class="inputfile" accept="video/*" />
                        <label for="video1File"><i class="fa fa-file-video-o"></i></label>
                      </div>
                      
                    </div> 
                  </div>
                  <div class="ms-auto p-1 bd-highlight">
                  <a class="btn btn-sm button btn-info pull-right" href="<?= SITE_URL; ?>/users/victory-media.php?type=video&date=<?=$currentDate;?>">Más<i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

                  </div>
                </div>

               
              </div>
            </div>
            <div class="py-2 py-3" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden;">Tus 7-Objetivos y Prioridades Más Importantes para tu Vida:
                <?php if ($isPastDate == false) : ?>
                  <button type="button" class="btn btn-sm btn-info screenonly pull-right" id="editBtn2">Editar</button>
                <?php endif; ?>
              </h2>
            </div>
            <div class="cardd" id="section-4">

              <div class="goals-area" id="life-goals-area" style="display:block;">
                <ol style="font-size: 1rem;" id="daily-life-goal-list" class="goal-list">
                  <?php foreach ($dailyLifeGoals as $key => $item) :  ?>
                    <li class="<?= ($key > 9) ? 'hidden more' : ''; ?>" id="life-goal-list-item-<?= $item['id']; ?>">
                      <label id="life-list-label-<?= $item['id']; ?>">

                        <span class="lifeGoalText" id="lifeGoalText-<?= $item['id']; ?>"><?= nl2br($item['goal']); ?> </span>
                        <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" class="input-lifegoals" name="lifeAchieved[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['achieved'] == 1) echo 'checked'; ?>>
                        <a class="edit-actions edit-goal-btn" data-type="life" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                        <a class="edit-actions delete-goal-btn" data-type="life" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                      </label>
                    </li>
                  <?php endforeach; ?>
                </ol>

                <div class="form-group" id="new-life-goal-creation-container"></div>
                <?php if ($isPastDate == false) : ?>
                  <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-life-goal-btn-wrapper">
                    <button type="button" id="save-new-life-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewLifeGoals()"><i class="fa fa-save"></i> Guarda Nuevo Objetivo</button>
                    <button type="button" class="button btn btn-info" onClick="CreateDailyLifeGoal()"><i class="fa fa-book"></i> Agrega Objetivo</button>
                  </div>
                <?php endif; ?>
              </div>
            </div>


            <?php
            $boxes = [
              "box1" => ['id' => 1, 'title' => 'SuperAfirmacion', 'subtitle' => 'Afirmación para tu Mejor Versión', 'body' => ''],
              "box2" => ['id' => 2, 'title' => 'VisualFit', 'subtitle' => 'Imagenes Exitosas de ti', 'body' => ''],
              "box3" => ['id' => 3, 'title' => 'SuperImagen', 'subtitle' => '1-Imagen de 1 Gran Exito', 'body' => ''],
              "box4" => ['id' => 4, 'title' => 'SuperMotivación', 'subtitle' => 'Lo qué Más te Motiva', 'body' => ''],
              "box5" => ['id' => 5, 'title' => 'SuperInspiration', 'subtitle' => 'Ideas Que te Inspiran', 'body' => ''],
              "box6" => ['id' => 6, 'title' => 'SuperCreencias', 'subtitle' => 'Creencias que Más te Empoderen', 'body' => ''],
              "box7" => ['id' => 7, 'title' => 'SuperPreguntas', 'subtitle' => '¿Cómo Puedo Mejorar Ahora?', 'body' => ''],
              "box8" => ['id' => 8, 'title' => 'SuperEntusiasmo', 'subtitle' => 'Que Estoy Más Entusiasmado Ahora?', 'body' => ''],
              "box9" => ['id' => 9, 'title' => 'SuperAcuerdos', 'subtitle' => 'Acuerdos y Promesas', 'body' => ''],
              "box10" => ['id' => 10, 'title' => 'SUPERVISION Ahora', 'subtitle' => 'La Visión Más Espectacular', 'body' => ''],

            ];

            $result = $common->get("victory7boxes", "user_id = :user_id AND created_at <= :created_at", ['user_id' => $user_id, 'created_at' => $goalDate]);
            if ($result) {
              foreach ($result as $row) {
                $boxes["box" . $row['box']]['body'] = $row['body'];
              }
            }
            //print_r($boxes);

            ?>

            <div id="section_box_wrapper" class="section_box_wrapper">
              <?php foreach ($boxes as $k => $bitem) : ?>
                <div class="section_box" id="section_box_<?= $bitem['id'] ?>">
                  <div class="section_header">
                    <h2><?= $bitem['title'] ?></h2> <small><?= $bitem['subtitle'] ?></small>
                  </div>
                  <div class="section_content"><textarea name="box[<?= $bitem['id'] ?>]" data-box="<?= $bitem['id'] ?>" id="boxitem-<?= $bitem['id'] ?>" class="LetterApplication boxitem"><?= $bitem['body'] ?></textarea></div>
                </div>
              <?php endforeach; ?>


            </div>
            <div class="load-btn-wrapper mt-5 mb-5 text-center">
              <button class="btn btn-lg btn-warning" id="btnLoadMoreTenSections">Mostrar más</button>
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

                  <input class="btn btn-info letter" type="button" id="saveBtn" name="saveBtn" value="Guardar" />
                

              </div>
            </div>
          </form>
        </div>


        <?php
        $extendedDailygoals = $common->first('extended_dailygoals', 'user_id = :user_id AND date = :date', ['user_id' => $user_id, 'date' => $currentDate]);
        $appointments = [];
        $toBeDone = [];
        $incomeExpenses = [];
        $notes = [];
        if ($extendedDailygoals) {
          $appointments = json_decode($extendedDailygoals['appointments'], true) ?? [];
          $toBeDone = json_decode($extendedDailygoals['to_be_done_today'], true) ?? [];
          $incomeExpenses = json_decode($extendedDailygoals['income_expenses'], true) ?? [];
          $notes = json_decode($extendedDailygoals['notes'], true) ?? [];
        }
        ?>
        <!--        extended sections start-->
        <div class="pt-5" id="slide-2">
          <div class="cardd mb-4">
            <div class="mt-5 mb-3" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Citas y Eventos:</h2>
            </div>
            <?php include_once 'inc/appointments.php'; ?>
            <div class="mt-5 mb-3" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Para Hacer Hoy:</h2>
            </div>
            <?php include_once 'inc/toBeDone.php'; ?>
            <div class="mt-5 mb-3" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Ingresos y gastos:</h2>
            </div>
            <?php include_once 'inc/incomeExpense.php'; ?>
            <div class="mt-5 mb-3" style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle text-black " style="padding:0; margin:0; width:100%; overflow:hidden;">Notas:</h2>
            </div>
            <?php include_once 'inc/notes.php'; ?>
          </div>
        </div>
        <!--        extended sections end-->
        <!-- Silder Navigation -->
        <button class="bg-primary py-1 px-3 rounded border border-primary text-white" id="prev-btn"><i class="fa fa-angle-double-left" aria-hidden="true"></i></button>
        <button class="bg-primary py-1 px-2 rounded border border-primary text-white" id="next-btn">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></button>
      </div>
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



<div class="modal fade p-0" id="audioRecorderModal" tabindex="-1" aria-labelledby="audioRecorderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content bg-dark">
      <div class="modal-header border-0">        
        <button type="button" class="btn-close bg-white border border-warning" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="min-height:350px;">  
      <div class="d-flex align-items-center justify-content-center"  style=" height:100%; ">
      <div class="audio-recording-container text-white text-center" id="audio-recorder">
          <div class="recording-info-wrapper" id="recording-info"> 
            <div class="recording-elapsed-time">
              <p class="elapsed-time"></p>
              <p class="max-duration-label">Max duration 2 minutes</p>
            </div> 
            <div id="recordingsList"></div>
            <div class="controls">
              <button disabled class="rounded-circle border border-3 btn btn-danger stop-recording-button " id="stopButton"> <i class="fa fa-circle" aria-hidden="true"></i></button>
              <button disabled class="rounded-circle border border-3 btn btn-danger cancel-recording-button " id="pauseButton"><i class=" fa fa-pause" aria-hidden="true"></i></button>
            </div>
          </div>
          <button style="margin:2rem auto;" class="rounded-circle border border-3 btn btn-danger start-recording-button" id="recordButton"><i class=" fa fa-microphone" aria-hidden="true"></i></button>
          
        </div>
      </div>     
        
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
      newEditor.model.document.on( 'change:data', (e) => {
        if (newEditor.sourceElement.classList.contains('boxitem')) {
            if (newEditor.sourceElement.dataset.box) {
            let box = newEditor.sourceElement.dataset.box;
            let body = newEditor.getData();
            $.ajax({
              url: SITE_URL + "/users/ajax/ajax.php",
              type: "POST",
              data: {
                action: 'SaveVictory7Box',
                box: box,
                currentDate: currentDate,
                body: body
              },
              success: function(data) {
                var jsonObj = JSON.parse(data);
                console.log('data', data, jsonObj);

              }
            });
          }
        }
       
      });
      if(node.id){
        window.editors[ node.id ] = newEditor;
      }else{
        window.editors[ index ] = newEditor	;
      }
			
		} );
} );
 
 
  let currentCount = 0;
  const slide1 = document.getElementById('slide-1');
  const slide2 = document.getElementById('slide-2');
  document.getElementById('next-btn').addEventListener('click', (e) => {
    if (currentCount < 1) {
      slide1.style.transform = 'translateX(-100%)';
      slide2.style.transform = 'translateX(-100%)';
      $('#next-btn').hide();
      $('#prev-btn').show();
      currentCount++;
    }
  });
  document.getElementById('prev-btn').addEventListener('click', (e) => {
    if (currentCount > 0) {
      slide2.style.transform = 'translateX(0)';
      slide1.style.transform = 'translateX(0)';
      $('#next-btn').show();
      $('#prev-btn').hide();
      currentCount--;
    }
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
          currentDate: currentDate,
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


              $("#daily-life-goal-list").append('<li style="font-size: 1rem;" id="life-goal-list-item-' + prop + '"><label class="form-label" id="life-list-label-' + prop + '"><span class="lifeGoalText" id="lifeGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="lifeAchieved[' + prop + ']" class="input-lifegoals" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-type="life" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-type="life" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-life-goals-btn').hide();
          }
        }
      });
    }
  }
  
  function SaveNewImpGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newimpgoals");
    var validated = hasFilledNewGoals('newimpgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          SaveNewDailyImportantGoals: 'SaveNewDailyImportantGoals',
          currentDate: currentDate,
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-important-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);


              $("#daily-important-goal-list").append('<li class="" id="important-goal-list-item-' + prop + '"><label class="form-label" id="important-list-label-' + prop + '"><span id="importantGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="achieved[' + prop + ']" class="input-importantgoals" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-type="important" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-important-goals-btn').hide();
          }
        }
      });
    }
  }
  function SaveNewTopGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    var validated = hasFilledNewGoals('newtopgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          SaveNewDailyTopGoals: 'SaveNewDailyTopGoals',
          currentDate: currentDate,
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


              $("#daily-top-goal-list").append('<li class="" id="top-goal-list-item-' + prop + '"><label class="form-label" id="top-list-label-' + prop + '"><span id="topGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="achieved[' + prop + ']" class="input-topgoals" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-type="top" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
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
  function CreateDailyImportantGoal() {
    $wrapper = $('#new-important-goal-creation-container');
    var validated = hasFilledNewGoals('newimpgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newimpgoals");
    if (validated && remainingImportantGoals > 0) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newimpgoals' name='newimpgoals[]'/></textarea></div>");
      remainingImportantGoals--;
      $('#save-new-important-goals-btn').show();

    } else {
      showToast('error', 'You can only add maximum 1 goals.');
    }
  }
  function CreateDailyTopGoal() {
    $wrapper = $('#new-top-goal-creation-container');
    var validated = hasFilledNewGoals('newtopgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    if (validated && remainingTopGoals > 0) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newtopgoals' name='newtopgoals[]'/></textarea></div>");
      remainingTopGoals--;
      $('#save-new-top-goals-btn').show();

    } else {
      showToast('error', 'You can only add maximum 7 goals.');
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
  $(document).on('click','.file-actions .file_delete',function(e){
    console.log('de;ete');
    e.preventDefault();
    $parentElem=$(this).parents('.v7-media-box');
    $fileElm=$parentElem.find('.media-thumb-wrapper');
    let fileId=$fileElm.data('file');
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
          $fileElm.remove();
          $parentElem.removeClass('file-added');
        }
      });
    
  });
  $(document).on('click', '#btnLoadMoreTenSections', function(e) {
    e.preventDefault();
    if ($(this).text() == 'Mostrar más') {
      $(this).text('Mostrar Menos');
      $("#section_box_wrapper").show();
    } else {
      $(this).text('Mostrar más');
      $("#section_box_wrapper").hide();
    }

  });

  $(document).on('change keyup paste', '#section_box_wrapper textarea', function(e) {
    console.log($(this));
  });


  $(document).on('click', '.edit-goal-btn', function(e) {
    e.preventDefault();
    var sectionType = $(this).data('type');
    var goalId = $(this).data('id');
    console.log('goalId', goalId, sectionType);
    var goalTextElem;
    var actionName = '';
    if (sectionType == 'important') {
      var goalTextElem = $('#importantGoalText-' + goalId);
      actionName = 'UpdateDailyImportantGoal';
    }
    else if (sectionType == 'top') {
      var goalTextElem = $('#topGoalText-' + goalId);
      actionName = 'UpdateDailyTopGoal';
    } else {
      var goalTextElem = $('#lifeGoalText-' + goalId);
      actionName = 'UpdateDailyLifeGoal';
    }
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
          UpdateDailyGoal: actionName,
          currentDate: currentDate,
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
          DeleteDailyGoals: 'DeleteDailyGoals',
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
  $('#impEditBtn').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');
    }
    $('#important-goals-area').toggleClass('edit');
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
  $(document).on('change', 'input.input-importantgoals', function() {
    var checked = $(this).is(':checked');
    var goalId = $(this).val();
    var goalText = $("#importantGoalText-" + goalId).text();
    console.log('goalId', goalId, checked, goalText);
    var achieved = 0;
    if (checked) achieved = 1;
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateDailyGoal: 'UpdateDailyImportantGoal',
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
        UpdateDailyGoal: 'UpdateDailyTopGoal',
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
    const evaluationTags = [];
    document.querySelectorAll('.evaluation-tags').forEach(tag => tag.value.trim() !== '' ? evaluationTags.push(tag.value) : '');


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
        evaluationTags: evaluationTags
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
  var videoThumbnail='';
  function dataURLtoFile(dataurl, filename) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
      bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while (n--) {
      u8arr[n] = bstr.charCodeAt(n);
    }
    return new File([u8arr], filename, { type: mime });
  }
  
  
  function createFilePreviewEle(id, url, type,$wrapper){
    console.log('createFilePreviewEle'+type,type,id, url, type,$wrapper);
    
        let filePreview ='';
        if(type=='image/jpeg' || type=='image/png' || type.startsWith("image")){
          filePreview= `<img alt="preview" class="files_img rounded-3" src="${url}"/>`;
        }
        if(type=='audio/mpeg' || type=='audio/x-m4a' || type.startsWith("audio")){
          filePreview= `<span class="preview-thumb"><i class="fa fa-file-audio-o "></i> </span>`;
        }
        if(type=='video/mp4' || type.startsWith("video")){
          //filePreview= `<img alt="preview" class="files_img rounded-3" src="${url}"/>`;
          filePreview= `<video id="video-preview-player" alt="preview" class="files_img rounded-3" src="${url}"/></video>`;
          //filePreview= `<span class="preview-thumb"><i class="fa fa-file-video-o"></i> </span>`;
          
        }  
       
        console.log('filePreview',filePreview);
        let $previewCard = $(
            `<div class="media-thumb-wrapper" id="${id}">
                    ${filePreview}
                        <div class="file-actions">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item file_delete" href="#">Delete</a></li>
                            </ul>
                          </div>
                        </div>
                        <div class="jquery-uploader-preview-progress">
                            <div class="progress-mask"></div>
                            <div class="progress-loading">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                        </div>
                 </div>`);
        $wrapper.prepend($previewCard);
        $wrapper.addClass("file-added");
        
        
        return $previewCard
  }
  function uuid() {
    let s = [];
    let hexDigits = "0123456789abcdef";
    for (let i = 0; i < 36; i++) {
        s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4";
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);
    s[8] = s[13] = s[18] = s[23] = "-";
    return s.join("");
}
  const BLOB_UTILS = function () {
    const windowURL = window.URL || window.webkitURL;
    /**
     * blob缓存
     * @type {Map<String, Blob>}
     */
    let dict = new Map()
    return {
        // 创建blob url
        createBlobUrl: function (blob) {
            let blobUrl = windowURL.createObjectURL(blob)
            dict.set(blobUrl, blob)
            return blobUrl
        },
        // 销毁 blob 对象
        revokeBlobUrl: function (url) {
            windowURL.revokeObjectURL(url)
            dict.delete(url)
        },
        //根据 url 获取 blob对象
        getBlobFromUrl: function (url) {
            return dict.get(url)
        }
    }
}();

function paramsBuilder(uploaderFile,upload_type) {
        
        let form = new FormData();
        
        form.append("action", 'UploadV7File');
        form.append("type", upload_type);
        form.append("date", currentDate); 
        form.append("thumb", videoThumbnail); 
        if(upload_type=='video' && compressedVideoBlob!=null){
          form.append("file", compressedVideoBlob);  
          form.append("compressed", 1);         
        }else{
          form.append("file", uploaderFile.file);
        }
             
        return form;
  }
  var compressedVideoBlob=null;
  async function getVideoBlobTHumb(){
    scaleFactor=0.5;
    var _VIDEO = document.querySelector("#video-preview-player");
    let w = _VIDEO.videoWidth * scaleFactor;
    let h = _VIDEO.videoHeight * scaleFactor;
    const maxWidth = 640;
    const maxHeight = 480;
    let newWidth, newHeight;
    if (_VIDEO.videoWidth > _VIDEO.videoHeight) {
      newWidth = Math.min(maxWidth, _VIDEO.videoWidth);
      newHeight = (newWidth * _VIDEO.videoHeight) / _VIDEO.videoWidth;
    } else {
      newHeight = Math.min(maxHeight, _VIDEO.videoHeight);
      newWidth = (newHeight * _VIDEO.videoWidth) / _VIDEO.videoHeight;
    }
    let canvas = document.createElement('canvas');
    canvas.width = newWidth;
    canvas.height = newHeight;
    console.log('getVideoBlobTHumb',_VIDEO.videoWidth,_VIDEO.videoHeight,newWidth,newHeight,_VIDEO.duration);
    let ctx = canvas.getContext('2d');
    ctx.drawImage(_VIDEO, 0, 0, newWidth, newHeight);
    let dataURI = canvas.toDataURL('image/jpeg');
   videoThumbnail=dataURLtoFile(dataURI, `${+new Date()}_thumb.jpg`);

   
   
    //return dataURI;
  }
   
  function handleFileUpload(files,upload_type,$fileWrapperElem,thumbURI=''){
    let addFiles = [];
    for (let i = 0; i < files.length; i++) {
            let file = files[i]
            let type = file.type;
            let url = BLOB_UTILS.createBlobUrl(file);
           /* if(upload_type=='video'){
              url = thumbURI;
            } */
            let id = uuid();
            if(upload_type=='audio' && type==''){
              type='audio/wav';
            }
            let $previewCard = createFilePreviewEle(id, url, type,$fileWrapperElem);
           
            
            
            
            addFiles.push({
                id: id,
                type: type,
                name: file.name,
                url: type,
                file: file,
                $ele: $previewCard
            })
        }
        var waitTime=100;
        if(upload_type=='video'){
          waitTime=2000;
        }
        setTimeout(() => {
          if(upload_type=='video'){
            getVideoBlobTHumb();
          }
          
          addFiles.forEach(file => {
          $.ajax({
            url: SITE_URL+'/users/ajax/ajax.php?action=UploadV7File&date='+currentDate,
            contentType: false,
            processData: false,
            method: "POST",
            data: paramsBuilder(file,upload_type),
            success: function (json) {
              console.log('success response',json);
              let response=JSON.parse(json);            
              if(response.success){
                $fileWrapperElem.find('.jquery-uploader-preview-progress').hide();                
                $fileWrapperElem.find('.preview-thumb').remove();
                if(response.type=='audio'){
                  let $audioElm=$(`<audio controls="" src="${response.url}">
                              <a href="${response.url}">
                                  Download audio
                              </a> </audio>`);
                  $fileWrapperElem.find('.media-thumb-wrapper').prepend($audioElm);
                  $fileWrapperElem.addClass("has-audio-file");
                }else if(response.type=='video'){
                  $fileWrapperElem.find('video').remove();
                  let $audioElm=$(`<video controls="" preload="metadata">
                  <source src="${response.url}#t=0.2" type="video/mp4">
                        Your browser does not support the video tag.</video>`);
                 $fileWrapperElem.find('.media-thumb-wrapper').prepend($audioElm);
                }else{
                 // $fileWrapperElem.find('img').attr('src',response.url);
                 $fileWrapperElem.find('img').remove();
                  let $audioElm=$(`<a href="${response.file_url}" > <img class="rounded-3"  src="${response.url}"></a>`);
                  $fileWrapperElem.find('.media-thumb-wrapper').prepend($audioElm);
                }
                
              }
             
            },
            error: function (response) {
                console.error("上传异常", response)
               
            },
            xhr: function () {
                let xhr = new XMLHttpRequest();
                //使用XMLHttpRequest.upload监听上传过程，注册progress事件，打印回调函数中的event事件
                xhr.upload.addEventListener('progress', function (e) {
                    let progressRate = (e.loaded / e.total) * 100;
                    console.log('success progressCallback',progressRate);
                    $fileWrapperElem.find('.progress-mask').innerHTML=Math.ceil(progressRate)+'%';                    
                    
                })
                return xhr;
            }
        })
        });
        }, waitTime);
        

        
   
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
  
  var inputs = document.querySelectorAll( '.inputfile' );
  Array.prototype.forEach.call( inputs, function( input )  {
    input.addEventListener( 'change', function( e ){      
      var $elem=e.target; 
      var $wrapper=this.closest('.v7-media-box');
      var $fileWrapperElem=$("#"+$wrapper.id);
      let upload_type=this.closest('.upload-file-box').getAttribute('data-type'); 
      if(upload_type=='audio' || upload_type=='video'){
        let media;
        let maxAllowed=60;
        let allowedMsg='';
        const inputFile=this.files[0];
        if (inputFile.type.startsWith('audio/')) {
          media = document.createElement('audio');
          maxAllowed=120;
          allowedMsg="This audio is ";
        } else if (inputFile.type.startsWith('video/')) {
          media = document.createElement('video');
          maxAllowed=60;
        }
        media.src = URL.createObjectURL(inputFile);
        media.addEventListener('loadedmetadata', () => {
          const duration = media.duration;
          
          
          if(duration>maxAllowed){
            var mins=duration/60;
            if(upload_type=='audio'){
              showToast('error', "Only maximum 2 minutes of audio is allowed. This audio has duration "+getDurationTIme(duration));
            }else if(upload_type=='video'){
              showToast('error', "Only maximum 1 minute of video is allowed. This video has duration "+getDurationTIme(duration));
            }
            
          }else{
                      
            handleFileUpload(this.files,upload_type,$fileWrapperElem);
           
            
          }
        });

      }else{
        handleFileUpload(this.files,upload_type,$fileWrapperElem);
      }
      
      
    });
  });
  
  function checkAddAssociate(){
    alert('checkAddAssociate');
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
  $('#sendBtn').click(function() {
    var self = $(this);
    var btnText = $(this).text();
    $("#modal-msg").html('');
    $("#toEmail").parent().removeClass('has-errors');
    var toEmail = $("#toEmail").val();
    console.log('toEmail', toEmail);
    var dailyEvolution = window.editors['dailyEvolution'].getData(); 
    $("#print-evaluation").html(dailyEvolution);
    var dailyImprovements = window.editors['dailyImprovements'].getData();
    $("#print-improvements").html(dailyImprovements);
    if (toEmail && toEmail != '') {
      $(this).text('Sending...');

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          EmailSendDailyGoal: 'EmailSendDailyGoal',
          currentDate: currentDate,
          dailyEvolution: dailyEvolution,
          dailyImprovements: dailyImprovements,
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



  $(function() {
    $('.daily-datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true,
        weekStart: 1
      })
      .on('changeDate', function(e) {
        console.log('changeDate', e.date, e.format('yyyy-mm-dd'));
        window.location.href = SITE_URL + "/users/dailygoals.php?date=" + e.format('yyyy-mm-dd');



      });
  });
</script>
<script src="<?=SITE_URL; ?>/users/dist/audio-recorder.js"></script>
<script type="application/javascript">
  
</script>
<div id="popovertip2" class="popovertip"  data-days="3" data-page="dailygoalsbackup" data-bs-custom-class="mejor-info-popover white-popover bs-popover-bottom 1st popovertip2" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="<p>Te Gustaría hacer un Backup para no Perder tu Info? </p><a class='btn btn-warning mt-2' href='<?=SITE_URL;?>/users/backup.php'>Haz Click Aquí</a>"></div>
<div id="popovertip" class="popovertip" data-page="dailygoals" data-bs-custom-class="mejor-info-popover bs-popover-bottom 2nd" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Cada día es una Oportunidad Inmensa de Vivir y de Servir. Elije las 7 Acciones o Resultados más importantes que quieres lograr. Cada Momento es Irrepetible. Haz el Esfuerzo para que hoy sea Excepcional. ¡Si puedes!"></div>

<?php require_once "inc/footer.php"; ?>