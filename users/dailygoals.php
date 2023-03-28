<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
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
$row = $common->first('dailygaols', 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $currentDate]);
if ($row) {
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

if ($toRemember) {
  $toRememberTags = $common->leftJoin(
    'to_remember_user_tag',
    'user_tags',
    'to_remember_user_tag.user_tag_id = user_tags.id',
    'to_remember_user_tag.to_remember_id = :to_remember_id',
    ['to_remember_id' => $toRemember['id']],
    ['tag','to_remember_user_tag.id'],
    'to_remember_user_tag.id'
  );
}

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
  var currentDate = '<?= $currentDate; ?>';
  var remainingTopGoals = 7 - topGoalsCounts;
  var remainingLifeGoals = 7 - lifeGoalsCounts;
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
            <div class="cardd mb-5" id="section-2" style="padding:0 5px;">
              <h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Mini Resumen de Hoy:</h5>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-evaluation"><?= $dailyEvolution; ?></div>
                    <textarea id="dailyEvolution" rows="5" class="LetterApplication editor ckeditor" name="dailyEvolution"><?= $dailyEvolution; ?></textarea>
                  </div>
                </div>
              </div>
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
                <h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Momentos para Recordar:</h5>
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

            <div class="row px-1 mb-5">
              <h5 class="" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Escribre 3 Etiquetas para facilitar la busqueda
                :</h5>
              <?php for ($i = 0; $i < 3; $i++) : ?>
                <div class="col-md-4 my-2">
                  <input type="text" class="form-control to-remember-tags" placeholder="Tag" name="remember_tag_1" value="<?= $toRememberTags[$i]['tag'] ?? '' ?>">
                </div>
              <?php endfor; ?>
            </div>

            <div class="cardd mb-5" id="section-3" style="padding:0 5px;">
              <h5 class="card-header" style="color:#FFF; margin:5px 0; font-size: 1rem;">¿Cómo Puedo Mejorar?: </h5>

              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <div class="print-description" id="print-improvements"><?= $dailyImprovements; ?></div>
                    <textarea id="dailyImprovements" class="LetterApplication" name="dailyImprovements"><?= $dailyImprovements; ?></textarea>
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

                <!-- Floating Save Start -->
                <?php //if ($today <= $currentDate) : ?>
                  <input class="btn btn-info letter" type="button" id="saveBtn" name="saveBtn" value="Guardar" />
                <?php // endif; ?>
                <!-- Floating Save End -->

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
             // console.log( 'The data has changed!',newEditor.getData(),newEditor.sourceElement.classList );
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
    if (sectionType == 'top') {
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
<?php require_once "inc/footer.php"; ?>