<?php
/*Just for your server-side code*/
//header('Content-Type: text/html; charset=ISO-8859-1');

?>
<?php require_once "inc/header.php"; ?>
<?php
$type = 'weekly';
if (isset($_REQUEST['type'])) {
  $type = trim($_REQUEST['type']);
}
function getStartAndEndDate($week, $year)
{
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret['week_start'] = $dto->format('Y-m-d');
  // $ret['week_start'] = $dto->format('F d l , Y');
  $dto->modify('+6 days');
  $ret['week_end'] = $dto->format('Y-m-d');
  //$ret['week_end'] = $dto->format('F d l , Y');
  return $ret;
}


function localDate($cdate,$format="%A, %d %B, %Y")
{
  setlocale(LC_ALL, "es_ES");
  $string = date('d/m/Y', strtotime($cdate));
  $dateObj = DateTime::createFromFormat("d/m/Y", $string);
  return utf8_encode(strftime($format, $dateObj->getTimestamp()));
}
$today = date('Y-m-d');


$date = !empty($_REQUEST['date']) ? $_REQUEST['date'] : '';

$currentDate = empty($date) ? $today : $date;
$currentDate = date('Y-m-d', strtotime($currentDate));

$currentYear = date('Y', strtotime($currentDate));
$currentMonth = date('m', strtotime($currentDate));
$currentWeekNumber = date('W', strtotime($currentDate));
$selectedYear = !empty($_REQUEST['year']) ? (int)$_REQUEST['year'] : $currentYear;
$selectedWeekNumber = !empty($_REQUEST['week']) ? (int)$_REQUEST['week'] : $currentWeekNumber;
$selectedMonth = !empty($_REQUEST['month']) ? (int)$_REQUEST['month'] : $currentMonth;

$currentQuarter = floor(($selectedMonth - 1) / 3) + 1;
$selectedQuarter = !empty($_REQUEST['quarter']) ? (int)$_REQUEST['quarter'] : $currentQuarter;

$week_array = getStartAndEndDate($selectedWeekNumber, $selectedYear);

$nextWeekString = '';
$previousWeekString = '';

$week_previous_year = $selectedYear;
$week_previous_number = $selectedWeekNumber - 1;
$week_next_number = $selectedWeekNumber + 1;
$week_next_year = $selectedYear;

if ($selectedWeekNumber == 52) {
  $nextWeekString = 'week=1&year=' . ($selectedYear + 1);
  $previousWeekString = 'week=' . ($selectedWeekNumber - 1) . '&year=' . $selectedYear;
  $week_next_year = $selectedYear + 1;
  $week_next_number = 1;
} else if ($selectedWeekNumber == 1) {
  $nextWeekString = 'week=2&year=' . $selectedYear;
  $previousWeekString = 'week=52&year=' . ($selectedYear - 1);
  $week_previous_number = 52;
  $week_previous_year = $selectedYear - 1;
} else {
  $nextWeekString = 'week=' . ($selectedWeekNumber + 1) . '&year=' . $selectedYear;
  $previousWeekString = 'week=' . ($selectedWeekNumber - 1) . '&year=' . $selectedYear;
}

$nextQuarter = '';
$nextQuarterYear = '';
$prevQuarter = '';
$prevQuarterYear = '';

$goals_heading = '';
$evaluation_heading = '';
$priority_heading='';
if ($type == 'weekly') {

  $start_date = $week_array['week_start'];
  $end_date = $week_array['week_end'];

  $goals_heading = 'Objetivos y Prioridades esta Semana';
  $evaluation_heading = 'Evaluación/Progreso. Cosas para Mejorar';
  $priority_heading='3-Acciones o Resultados Más Importantes esta Semana';
} elseif ($type == 'monthly') {
  $start_date = $selectedYear . '-' . $selectedMonth . '-01';
  $end_date = date('Y-m-t', strtotime($start_date));
  $goals_heading = 'Objetivos y Prioridades ESTE Mes';
  $evaluation_heading = 'Evaluación/Progreso. Cosas para Mejorar';
  $priority_heading='3-Acciones o Resultados Más Importantes este Mes:';
} elseif ($type == 'yearly') {
  $start_date = $selectedYear . '-01-01';
  $end_date = $selectedYear . '-12-31';
  $goals_heading = 'Objetivos y Sueños este Año';
  $evaluation_heading = 'Evaluación/Progreso. Cosas para Mejorar';
  $priority_heading='3-Acciones o Resultados Más Importantes este Año:';
} elseif ($type == 'lifetime') {
  $start_date = '1900-01-01';
  $end_date = '2200-12-31';
  $goals_heading = 'Objetivos, Prioridades y Sueños para tu Vida';
  $evaluation_heading = 'Evaluación/Progreso. Cosas para Mejorar';
} elseif ($type == 'quarterly') {
  $priority_heading='3-Acciones o Resultados Más Importantes este Trimestre';
  $nextQuarterYear = $selectedYear;
  if ($selectedQuarter == 1) {
    $start_date = $selectedYear . '-01-01';
    $end_date = $selectedYear . '-03-31';
    $nextQuarter = 2;
    $prevQuarter = 4;
    $prevQuarterYear = $selectedYear - 1;
    $nextQuarterYear = $selectedYear;
  } elseif ($selectedQuarter == 2) {
    $start_date = $selectedYear . '-04-01';
    $end_date = $selectedYear . '-06-30';
    $nextQuarter = 3;
    $nextQuarterYear = $selectedYear;
    $prevQuarter = 1;
    $prevQuarterYear = $selectedYear;
  } elseif ($selectedQuarter == 3) {
    $start_date = $selectedYear . '-07-01';
    $end_date = $selectedYear . '-09-30';
    $nextQuarter = 4;
    $prevQuarter = 2;
    $nextQuarterYear = $selectedYear;
    $prevQuarterYear = $selectedYear;
  } elseif ($selectedQuarter == 4) {
    $start_date = $selectedYear . '-10-01';
    $end_date = $selectedYear . '-12-31';
    $nextQuarter = 1;
    $prevQuarter = 3;
    $prevQuarterYear = $selectedYear;
    $nextQuarterYear = $selectedYear + 1;
  }


  $goals_heading = 'Objetivos y Prioridades este Trimestre';
  $evaluation_heading = 'Evaluación/Progreso. Cosas para Mejorar';
}




?>

<?php

$user_id = Session::get('user_id');
$table_name = 'supergoals';
$goals = $common->get(
  "supergoals",
  'user_id = :user_id AND type = :type AND DATE(start_date) >= :start_date AND DATE(end_date) <= :end_date',
  ['user_id' => $user_id, 'type' => $type, 'start_date' => $start_date, 'end_date' => $end_date]
);
$priorityGoals = $common->get(
  "supergoals_priorites",
  'user_id = :user_id AND type = :type AND DATE(start_date) >= :start_date AND DATE(end_date) <= :end_date',
  ['user_id' => $user_id, 'type' => $type, 'start_date' => $start_date, 'end_date' => $end_date]
);
$dreamWallImages = $common->get(
  "dreamwall_images",
  'user_id = :user_id',
  ['user_id' => $user_id]
);

//if(isset($_GET['test'])){
//  $result=$common->db->select("SELECT * FROM supergoals WHERE id='226'");
//  $row = $result -> fetch_assoc();
//  print_r($row);
//}
//if($result){
//  while ($row = $result -> fetch_assoc()) {
//    $goals[]=$row;
//  }
//}else{
/*
  $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND end_date<='".$start_date."' ORDER BY end_date DESC LIMIT 0,1");
  if($result){
    $row = $result -> fetch_assoc();
    $previous_start_date=$row['start_date'];
    $previous_end_date=$row['end_date'];
    $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$previous_start_date."' AND end_date<='".$previous_end_date."'");
    if($result){
      while ($row = $result -> fetch_assoc()) {
        
        $row['achieved']=0;
        $goals[]=$row;  
      }
    }
  }
  */
//}

$evaluation = '';
$planning = '';
$row = $common->first(
  "supergoals_evaluation",
  'user_id = :user_id AND type = :type AND start_date >= :start_date AND end_date <= :end_date',
  ['user_id' => $user_id, 'type' => $type, 'start_date' => $start_date, 'end_date' => $end_date]
);
if ($row) {
  $evaluation = $row['description'];
  $planning = $row['planning'];
}

?>
<script>
  var SITE_URL = '<?= SITE_URL; ?>';
  var goalType = '<?= $type; ?>';
  var goalCounts = '<?= count($goals); ?>';
  var importantGoalsCount='<?=count($priorityGoals);?>';
  var startDate = '<?= $start_date; ?>';
  var endDate = '<?= $end_date; ?>';
  var remainingImportantGoals=3-importantGoalsCount;
</script>
<link rel="stylesheet" href="<?=SITE_URL; ?>/users/assets/uikit-lightbox.css" />
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
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

    fixed-save-btn {
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
    font-size: 1.5rem;
  }

  .projects-header p {
    font-size: 1.1rem;
  }

  .goal-list textarea {
    width: 100%;
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

  .datestr {
    text-transform: capitalize;
  }
  #goals-area,.goals-area {
      padding: 20px 0px;
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

  .admin-dashbord {
    background: #ed008c;
  }

  .projects {
    border: none;
  }
  .v7-media-box{
    position:relative;
  }
  .v7-media-box img{
    height:106px; 
    border-radius: 10px;
    max-width:100%;
  }
  .v7-media-box .file-actions{
  position:absolute; top:0; right:0;
}
.v7-media-box.file-added .upload-file-box{
    display:none;
  }
  .upload-file-box{   
    cursor:pointer;
  }
  .v7-media-box .media-thumb-wrapper{
    width:106px;
    height:106px;
    text-align:center;
    background:#000;
    border-radius: 10px;
    position: relative;
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
    color: 000;
    display: inline-block;
    border: 1px dotted #aeacac;
    padding: 2.2rem;
    border-radius: 10px;
    background: #f7f7f7;
    WIDTH:106PX;
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
  
</style>

<?php



?>
<main role="main" class="col-md-9 ml-sm-auto col-lg-10">
  <?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        <?php if ($type != 'lifetime') : ?>
          <div class="row" style="margin-bottom:15px;">
            <div class="col-sm-9"></div>
            <div class="col-sm-3">
              <div class="input-group date datepicker" id="datepicker">
                <?php $df = 'd-m-Y';
                if ($type == 'yearly') {
                  $df = 'Y';
                }
                ?>
                <input type="text" class="form-control" value="<?= date($df, strtotime($start_date)); ?>" id="date" readonly />
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($type == 'weekly') : ?>

          <div class="row">
            <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=weekly&<?= $previousWeekString; ?>" ;><i class="fa fa-arrow-left"></i></a></div>
            <div class="col-sm-6 col-6" style="text-align:center;">
              <h2 class="">Semana</h2>
            </div>
            <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=weekly&<?= $nextWeekString; ?>"><i class="fa fa-arrow-right"></i></a></div>
          </div>
          <p><label>Semana # :</label> <span><?= $selectedWeekNumber; ?></span></p>
          <p><label>De :</label> <span class="datestr"><?= localDate($start_date); ?></span></p>
          <p><label>Hasta :</label> <span class="datestr"><?= localDate($end_date); ?></span></p>
        <?php elseif ($type == 'monthly') : ?>
          <div class="row">
            <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly&month=<?= date("m", strtotime("-1 month", strtotime($start_date))) ?>&year=<?= date("Y", strtotime("-1 month", strtotime($start_date))) ?>" ;><i class="fa fa-arrow-left"></i></a></div>
            <div class="col-sm-6 col-6" style="text-align:center;">
              <h2 class="" style="text-transform:uppercase;"><?= localDate($end_date,"%B"); ?></h2>
            </div>
            <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=monthly&&month=<?= date("m", strtotime("+1 month", strtotime($start_date))) ?>&year=<?= date("Y", strtotime("+1 month", strtotime($start_date))) ?>"><i class="fa fa-arrow-right"></i></a></div>
          </div>
          <p><label>Mes:</label> <span><?= $selectedMonth; ?></span></p>
          <p><label>De :</label> <span class="datestr"><?= localDate($start_date); ?></span></p>
          <p><label>Hasta :</label> <span class="datestr"><?= localDate($end_date);; ?></span></p>
        <?php elseif ($type == 'yearly') : ?>
          <div class="row">
            <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly&year=<?= $selectedYear - 1; ?>" ;><i class="fa fa-arrow-left"></i></a></div>
            <div class="col-sm-6 col-6" style="text-align:center;">
              <h2 class=""><?=$selectedYear;?></h2>
            </div>
            <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=yearly&year=<?= $selectedYear + 1; ?>"><i class="fa fa-arrow-right"></i></a></div>
          </div>
          <p><label>Año:</label> <span><?= $selectedYear; ?></span></p>
          <p><label>De :</label> <span class="datestr"><?= localDate($start_date); ?></span></p>
          <p><label>Hasta :</label> <span class="datestr"><?= localDate($end_date); ?></span></p>
        <?php elseif ($type == 'quarterly') : ?>
          <div class="row">
            <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly&quarter=<?= $prevQuarter; ?>&year=<?= $prevQuarterYear; ?>" ;><i class="fa fa-arrow-left"></i></a></div>
            <div class="col-sm-6 col-6" style="text-align:center;">
              <h2 class="">Trimestral</h2>
            </div>
            <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?= SITE_URL; ?>/users/supergoals.php?type=quarterly&quarter=<?= $nextQuarter; ?>&year=<?= $nextQuarterYear; ?>"><i class="fa fa-arrow-right"></i></a></div>
          </div>
          <p><label>Trimestral:</label> <span><?= $selectedQuarter; ?></span></p>
          <p><label>De :</label> <span class="datestr"><?= localDate($start_date);; ?></span></p>
          <p><label>Hasta :</label> <span class="datestr"><?= localDate($end_date);; ?></span></p>

        <?php elseif ($type == 'lifetime') : ?>
          <div class="row">
            <div class="col-sm-12" style="text-align:center;">
              <h2 class="">De por Vida</h2>
            </div>
          </div>
        <?php endif; ?>
      </header>




      <div class="mt-3" style="background-color: #fef200; padding: 10px">
        <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; "><?= $goals_heading; ?>

          <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn">Editar</button>

        </h2>
      </div>
      <form class="form" id="goalsFrom">

        <div class="goals-area mb-4" id="goals-area" style="display:block;">

          <ol id="goal-list" class="goal-list">
            <?php foreach ($goals as $key => $item) :  ?>
              <li class="<?= ($key > 9) ? 'hidden more' : ''; ?>" id="goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                <label id="list-label-<?= $item['id']; ?>">

                  <span style="font-size: 1rem;" id="goalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                  <input class="me-1 input-goals" data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" name="achieved[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['achieved'] == 1) echo 'checked'; ?>>
                  <a class="edit-actions edit-goal-btn" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                  <a class="edit-actions delete-goal-btn" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </label>
              </li>
            <?php endforeach; ?>
          </ol>
          <?php if (count($goals) > 10) : ?>
            <div class="screenonly" style="text-align:center;"><button id="morelessToggleBtn" type="button" class="btn btn-primary">Mostrar más</button></div>
          <?php endif; ?>

          <div class="form-group" id="new-goal-creation-container"></div>
          <?php if ($today < $end_date) : ?>
            <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-goal-btn-wrapper">
              <button type="button" id="save-new-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewGoals('<?= $type; ?>')"><i class="fa fa-save"></i> Save New Goals</button>
              <button type="button" class="button btn btn-info" onClick="CreateGoal('<?= $type; ?>')"><i class="fa fa-book"></i> Agrega Objetivo</button>
            </div>
          <?php endif; ?>
      </div>

          <?php if(!empty($priority_heading)): ?>
             <div class="mt-5" style="background-color: #fef200; padding: 10px; margin-top:30px;">
                <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; "><?= $priority_heading; ?>
                  <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn1">Editar</button>
                </h2>
            </div>
            <div class="goals-area mb-4" id="priority-goals-area" style="display:block;">

              <ol id="priority-goal-list" class="goal-list">
                <?php foreach ($priorityGoals as $key => $item) :  ?>
                  <li id="priority-goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                    <label id="list-label-<?= $item['id']; ?>">

                      <span style="font-size: 1rem;" id="goalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                      <input class="me-1 input-goals priority" data-id="<?= $item['id']; ?>" value="<?= $item['id']; ?>" name="achieved[<?= $item['id']; ?>]" type="checkbox" <?php if ($item['achieved'] == 1) echo 'checked'; ?>>
                      <a class="edit-actions edit-goal-btn priority" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                      <a class="edit-actions delete-goal-btn priority" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </label>
                  </li>
                <?php endforeach; ?>
              </ol>
              <div class="form-group" id="new-priority-goal-creation-container"></div>
              <?php if (count($priorityGoals) <3) : ?>
                <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-goal-btn-wrapper">
                  <button type="button" id="save-new-priority-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewPriorityGoals('<?= $type; ?>')"><i class="fa fa-save"></i> Guarda Resultado</button>
                  <button type="button" class="button btn btn-info" onClick="CreatePriorityGoal('<?= $type; ?>')"><i class="fa fa-book"></i> Resultado</button>
                </div>
                <?php endif; ?>
                
              
            </div>
          <?php endif; ?>
          <div class="form-group mx-1 mt-5">
            <div class="description-area">

              
              <div class="d-flex justify-content-between my-3">
              <label style="color:#FFF; font-size:1.1rem;">Planificación Exitosa</label>
              </div>
              <textarea id="planning" class="LetterApplication" name="planning"><?= $planning; ?></textarea>
            </div>
          </div>

          <div class="form-group mx-1 mt-5 mb-3 pt-2">
            <div class="description-area">

              
              <div class="d-flex justify-content-between my-2">
              <label style="color:#FFF; font-size:1.1rem;"><?= $evaluation_heading ?></label>
                <a href="<?= SITE_URL; ?>/users/supergoalsSummary.php?type=<?=$type;?>" class="bg-primary py-1 px-2 rounded border border-primary text-white text-decoration-none">Más <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>

              </div>
              <div class="print-description" id="print-evaluation"><?= $evaluation; ?></div>
              <textarea id="LetterApplication" class="LetterApplication" name="LetterApplication"><?= $evaluation; ?></textarea>
            </div>
          </div>
         
          <div style="display: none;" id="show">
            <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
              <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>
            </div>
          </div>
          <div class="form-group screenonly">
            <div class="button-wrapper" style="margin:30px 0; overflow:hidden;">
              <button class="btn btn-info letter" type="button" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Email</button>

              <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />

             
                <input class="btn btn-info letter" type="button" id="saveBtn" name="saveBtn" value="Guardar" />
                <div>
                  <button class="btn btn-primary rounded-circle fixed-save-btn text-white" type="button" id="floatingSaveBtn" name="saveBtn"><i class="fa fa-save"></i></button>
                </div>
              

            </div>
          </div>
        
      </form>
    </div>
  </div>
  <div class="clearfix;"></div>
  <div class="clearfix;"></div>
          <div class="cardd mb-5" id="media-section" style="padding:0 5px; margin-left:5px; margin-right:5px;">
          <label style="color:#FFF; font-size:1.1rem; margin:5px 0;">DreamWall</label>
            <div class="card-body">
              <div class="d-flex flex-wrap bd-highlight mb-3 " uk-lightbox="animation: slide">
                <?php for($i=0; $i<10; $i++): ?>
                  <div class="p-1 bd-highlight v7-media-box <?=(count($dreamWallImages)>0 && isset($dreamWallImages[$i]))? 'file-added':''; ?>" id="mediabox<?=$i;?>">
                        <?php if(count($dreamWallImages)>0 && isset($dreamWallImages[$i])): ?>
                          <div class="media-thumb-wrapper" data-file="<?=$dreamWallImages[$i]['id'];?>" id="fileid-<?=$dreamWallImages[$i]['id'];?>">
                          <a href="<?=$dreamWallImages[$i]['url'];?>" id="lightbox-thumb-item-<?=$i;?>" data-index="<?=$i;?>"> <img class="rounded-3"  src="<?=$dreamWallImages[$i]['thumb'];?>"></a>
                          <div class="file-actions">
                          <div class="dropdown">
                            <button class="btn btn-light btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                            </button>
                            <ul class="dropdown-menu">
                              <li><div class="dropdown-item file_delete" href="#">Delete</div></li>
                            </ul>
                          </div>
                         </div>
                        </div> 
                        <?php endif; ?>
                        <div class="upload-box-image upload-file-box" data-type="image">                        
                          <input type="file" name="imagefile[]" id="imageFile<?=$i;?>" class="inputfile" accept="image/png, image/gif, image/jpeg"  />
                          <label for="imageFile<?=$i;?>"><i class="fa fa-camera"></i></label>
                        </div>
                    </div>
                 
                <?php endfor; ?>
                </div>
              <div>
              <div class="card-footer text-right" style="overflow:hidden; text-align:right;"><a class="btn btn-sm button btn-info" href="<?= SITE_URL; ?>/users/dream-wall.php">DreamWall<i class="fa fa-angle-double-right" aria-hidden="true"></i></a></div>
            
            <div>
</main>
<!-- Modal Starts-->

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
  var prioritytobeadded = 0;
  var newgoalsInput = [];

  function SaveNewGoals(type) {
    var newgoalsinput = document.querySelectorAll("textarea.newgoals");
    var validated = hasFilledNewGoals();
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          saveNewGoals: 'saveNewGoals',
          type: type,
          startDate: startDate,
          endDate: endDate,
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


              $("#goal-list").append('<li class="" style="font-size: 1rem;" id="goal-list-item-' + prop + '"><label class="form-label" id="list-label-' + prop + '"><span id="goalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="achieved[' + prop + ']" class="input-goals me-1" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-goals-btn').hide();
          }
        }
      });
    }
  }
  function SaveNewPriorityGoals(type) {
    var newgoalsinput = document.querySelectorAll("textarea.newimpgoals");
    var validated = hasFilledNewGoals('newimpgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          saveNewGoals: 'SaveNewPriorityGoals',
          type: type,
          startDate: startDate,
          endDate: endDate,
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-priority-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);


              $("#priority-goal-list").append('<li class="" style="font-size: 1rem;" id="priority-goal-list-item-' + prop + '"><label class="form-label" id="list-label-' + prop + '"><span id="goalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <input name="achieved[' + prop + ']" class="input-goals me-1 priority" type="checkbox" data-id="' + prop + '" value="' + prop + '"><a class="edit-actions edit-goal-btn priority" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-priority-goals-btn').hide();
          }
        }
      });
    }
  }
  
  function hasFilledNewGoals(classname='newgoals') {
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
  

  function CreateGoal(type) {

    $wrapper = $('#new-goal-creation-container');
    var validated = hasFilledNewGoals();
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newgoals");
    if (validated) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newgoals' name='newgoals[]'/></textarea></div>");
      goalstobeadded++;
      if (goalstobeadded > 0) {
        $('#save-new-goals-btn').show();
      }
    }


  }
  function CreatePriorityGoal(type) {

    $wrapper = $('#new-priority-goal-creation-container');
    var validated = hasFilledNewGoals();
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newimpgoals");
    if (validated && remainingImportantGoals > 0) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newimpgoals' name='newimpgoals[]'/></textarea></div>");
      remainingImportantGoals--;
      $('#save-new-priority-goals-btn').show();

    } else {
      showToast('error', 'You can only add maximum 3 goals.');
    }
   


  }
  
  $(document).on('click', '.edit-goal-btn', function(e) {
    e.preventDefault();
    var haspriorityClass=$(this).hasClass('priority');
    var goalId = $(this).data('id');
    console.log('goalId', goalId);
    var goalText = $('#goalText-' + goalId).text();
    console.log('goalText', goalText);
    if ($(this).find('.fa').hasClass('fa-pencil')) {
      $(this).find('.fa').removeClass('fa-pencil');
      $(this).find('.fa').addClass('fa-save');
      $('#goalText-' + goalId).hide();
      $("#list-label-" + goalId).append('<textarea id="edittextarea-' + goalId + '">' + goalText + '</textarea>');
    } else {
      var checked = $(this).find('.input-goals').is(':checked');
     
      $(this).find('.fa').addClass('fa-pencil');
      $(this).find('.fa').removeClass('fa-save');
      $('#goalText-' + goalId).show();
      var goalText = $('#edittextarea-' + goalId).val();
      $('#goalText-' + goalId).text(goalText);
      $('#edittextarea-' + goalId).remove();
      var achieved = 0;
      if (checked) {
        achieved = 1;
      } else {
        achieved = 0;
      }
      
      console.log('haspriorityClass',haspriorityClass);
      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          UpdateSuperGoal: haspriorityClass? 'UpdateSuperPriorityGoal':'UpdateSuperGoal',
          type: goalType,
          startDate: startDate,
          endDate: endDate,
          goalText: goalText,
          achieved: achieved,
          goalId: goalId,
        },
        success: function(data) {
          console.log('data', data);
          showToast('success', 'Update Successfully.');
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
    var goalId = $(this).data('id');
    var haspriorityClass=$(this).hasClass('priority');
    console.log('goalId', goalId);
    var goalIds = [];
    goalIds.push(goalId);
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        DeleteGoals: haspriorityClass? 'DeletePriorityGoal':'DeleteGoals',
        type: goalType,
        startDate: startDate,
        endDate: endDate,
        goalIds: goalIds,
      },
      success: function(data) {
        console.log('data', data, goalIds);
        for (let index = 0; index < goalIds.length; index++) {
          var gid = goalIds[index];
          if(haspriorityClass){
            $("#priority-goal-list-item-" + gid).remove();
          }else{
            $("#goal-list-item-" + gid).remove();
          }
          
        }

        if (data == 'Deleted') {
          showToast('success', 'Update Successfully.');
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
  $('#editBtn1').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');
    }
    $('#priority-goals-area').toggleClass('edit');
  });
  $('#editBtn').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');
    }
    $('#goals-area').toggleClass('edit');
  });

  $(document).on('change', 'input.input-goals', function() {
    var checked = $(this).is(':checked');
    var goalId = $(this).val();
    var goalText = $("#goalText-" + goalId).text();
    console.log('goalId', goalId, checked, goalText);
    var achieved = 0;
    if (checked) achieved = 1;
    var actionName='UpdateSuperGoal';
    if($(this).hasClass('priority')){
      actionName='UpdateSuperProrityGoal';
    }
    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateSuperGoal: actionName,
        type: goalType,
        startDate: startDate,
        endDate: endDate,
        goalText: goalText,
        achieved: achieved,
        goalId: goalId,
      },
      success: function(data) {
        console.log('data', data);
        showToast('success', 'Update Successfully.');
        if (data == 'Update') {
          $('#show').css('display', 'block');
          $('#error_success_msg_verification').css('color', '#me-');
          $('#error_success_msg_verification').css('background-color', '#ddffff');
          $('#success_msg_verification_text').html('Update Successfully');
          setTimeout(() => {
            $('#show').css('display', 'none');
          }, 3000);

        }
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
  function UpdateGoals() {
    var LetterApplication = window.editors['LetterApplication'].getData();
    var planning = window.editors['planning'].getData();
    $("#print-evaluation").html(LetterApplication);
    var goalsData = [];
    $('input.input-goals').each(function() {
      var checked = $(this).is(':checked');
      var goalId = $(this).data('id');
      var goalText = $("#goalText-" + goalId).text();
      var golaItem = {};
      golaItem.id = goalId;
      golaItem.checked = (checked == true) ? 1 : 0;
      golaItem.text = goalText;
      goalsData.push(golaItem);
    });

    $.ajax({
      url: SITE_URL + "/users/ajax/ajax.php",
      type: "POST",
      data: {
        UpdateSuperGoals: 'UpdateSuperGoals',
        type: goalType,
        description: LetterApplication,
        planning:planning,
        goalsData: goalsData,
        startDate: startDate,
        endDate: endDate,
      },
      success: function(data) {
        showToast('success', 'Update Successfully.');
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


  $('#morelessToggleBtn').click(function() {
    console.log($(this).text());
    $("#goal-list li.more").toggleClass("hidden");
    if ($(this).text() == 'Mostrar más') {
      $(this).text('Mostrar Menos');
    } else {
      $(this).text('Mostrar más');
    }
  });
  $('#saveBtn').click(function() {
    UpdateGoals();
  });
  $('#floatingSaveBtn').click(function() {
    UpdateGoals();
  });
  $('#savePrintBtn').click(function() {
    var LetterApplication = window.editors['LetterApplication'].getData();
    $("#print-evaluation").html(LetterApplication);
    window.print();
  });
  $('#sendBtn').click(function() {
    var self = $(this);
    var btnText = $(this).text();
    $("#modal-msg").html('');
    $("#toEmail").parent().removeClass('has-errors');
    var toEmail = $("#toEmail").val();
    console.log('toEmail', toEmail);

    if (toEmail && toEmail != '') {
      $(this).text('Sending...');
      var LetterApplication = window.editors['LetterApplication'].getData();
      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          EmailSendSuperGoal: 'EmailSendSuperGoal',
          type: goalType,
          description: LetterApplication,
          startDate: startDate,
          endDate: endDate,
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
    if (typeof goalType !== 'undefined') {
      var calOptions = {
        format: 'dd-mm-yyyy',
        autoclose: true,
        calendarWeeks: true,
        todayHighlight: true,
        weekStart: 1
      };
      if (goalType == 'yearly') {
        calOptions = {
          format: "yyyy",
          viewMode: "years",
          minViewMode: "years"
        }
      }
      $('.datepicker').datepicker(calOptions)
        .on('changeDate', function(e) {
          console.log('changeDate', e.date, e.format('yyyy-mm-dd'));
          if (goalType == 'yearly') {
            window.location.href = SITE_URL + "/users/supergoals.php?type=" + goalType + "&year=" + e.format('yyyy');
          } else {
            window.location.href = SITE_URL + "/users/supergoals.php?type=" + goalType + "&date=" + e.format('yyyy-mm-dd');
          }


        });
    }
  });
  function createFilePreviewEle(id, url, type,$wrapper){
    console.log('createFilePreviewEle',id, url, type,$wrapper);
    
        let filePreview ='';
        if(type=='image/jpeg' || type=='image/png' || type.startsWith("image")){
          filePreview= `<img alt="preview" class="files_img rounded-3" src="${url}"/>`;
        }
        if(type=='audio/mpeg' || type=='audio/x-m4a' || type.startsWith("audio")){
          filePreview= `<span class="preview-thumb"><i class="fa fa-file-audio-o "></i> </span>`;
        }
        if(type=='video/mp4' || type.startsWith("video")){
          filePreview= `<span class="preview-thumb"><i class="fa fa-file-video-o"></i> </span>`;
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
        form.append("file", uploaderFile.file);
        form.append("action", 'UploadDreamWallImage');
       // form.append("date", currentDate);        
        return form;
  }
  function handleFileUpload(files,$fileWrapperElem){
    let addFiles = [];
    for (let i = 0; i < files.length; i++) {
            let file = files[i]
            let type = file.type;
            let url = BLOB_UTILS.createBlobUrl(file)
            let id = uuid()
            let $previewCard = createFilePreviewEle(id, url, type,$fileWrapperElem)
            
            addFiles.push({
                id: id,
                type: type,
                name: file.name,
                url: type,
                file: file,
                $ele: $previewCard
            })
        }
        addFiles.forEach(file => {
          $.ajax({
            url: SITE_URL+'/users/ajax/ajax.php',
            contentType: false,
            processData: false,
            method: "POST",
            data: paramsBuilder(file),
            success: function (json) {
              
              var response=JSON.parse(json);  
              console.log('success response',response);          
              if(response.success){
                $fileWrapperElem.find('.jquery-uploader-preview-progress').hide();                
                
                 // $fileWrapperElem.find('img').attr('src',response.url);
                 $fileWrapperElem.find('img').remove();
                  let $audioElm=$(`<a href="${response.file_url}" > <img class="rounded-3"  src="${response.thumb_url}"></a>`);
                  $fileWrapperElem.find('.media-thumb-wrapper').prepend($audioElm);
              }else{
                console.log(response);
                showToast('danger', response.msg);
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
        
   
  }
  var inputs = document.querySelectorAll( '.inputfile' );
  Array.prototype.forEach.call( inputs, function( input )  {
    input.addEventListener( 'change', function( e ){      
      var $elem=e.target; 
      var $wrapper=this.closest('.v7-media-box');
      var $fileWrapperElem=$("#"+$wrapper.id);
     handleFileUpload(this.files,$fileWrapperElem)
      
    });
});
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
          action: 'DeleteDreamWallImage',
          id: fileId,
        },
        success: function(data) {
          console.log('data', data);
          $fileElm.remove();
          $parentElem.removeClass('file-added');
        }
      });
    
  });
</script>
<?php if($type=='weekly'): ?>
  <div id="popovertip" data-page="supergoals_weekly" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Escribe las Acciones o Resultados más importantes que quieres lograr esta Semana. No dejes Pasar esta Semana sin Hacer algo Extraordinario para tí o para otras Personas. Si puedes."></div>
<?php elseif($type=='monthly'): ?>
  <div id="popovertip" data-page="supergoals_montly" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Escribe las Acciones o Resultados más importantes que quieres lograr este Mes. Haz este Mes lo Mejor Posible. Tus logros Más Extraordinarios son Posibles. ¡Tu Puedes!"></div>
<?php elseif($type=='quarterly'): ?>
  <div id="popovertip" data-page="supergoals_quarterly" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Transforma y Revoluciona tu Vida en los Próximos 90 Días. Decide qué es lo más Importante y Trabaja cada día en ello. Si Puedes."></div>
<?php elseif($type=='yearly'): ?>
  <div id="popovertip" data-page="supergoals_yearly" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Todo es 'imposible' hasta que alguien lo hace Posible. Destroza tus limites y logra este Año tus Objetivos más Extraordinarios. Toma responsabilidad de tu mundo. Trabaja duro e inteligéntemente. ¡Tu Puedes!"></div>
<?php elseif($type=='lifetime'): ?>
  <div id="popovertip" data-page="supergoals_lifetime" data-bs-custom-class="mejor-info-popover bs-popover-bottom" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Escribe 100 Sueños, Deseos y Objetivos para el Resto de tu Vida. Ten el Valor y el Coraje de Soñar en Grande. ¡Con el Nivel Adecuado de Acción, Dedicación, Perseverancia, Creatividad y Determinación, No Hay Nada que No Puedas Hacer Realidad!"></div>
  <?php endif; ?>

<?php require_once "inc/footer.php"; ?>