<?php
    /*Just for your server-side code*/
    //header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php require_once "inc/header.php"; ?>
<?php 
$type='weekly';
if(isset($_REQUEST['type'])){
  $type=trim($_REQUEST['type']);
}
function getStartAndEndDate($week, $year) {
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret['week_start'] = $dto->format('Y-m-d');
 // $ret['week_start'] = $dto->format('F d l , Y');
  $dto->modify('+6 days');
  $ret['week_end'] = $dto->format('Y-m-d');
  //$ret['week_end'] = $dto->format('F d l , Y');
  return $ret;
}


function localDate($cdate){
  setlocale(LC_ALL,"es_ES");
  $string = date('d/m/Y',strtotime($cdate));
  $dateObj = DateTime::createFromFormat("d/m/Y", $string);
  return utf8_encode(strftime("%A, %d %B, %Y",$dateObj->getTimestamp()));
}
$today=date('Y-m-d');


$date=!empty($_REQUEST['date'])? $_REQUEST['date']:'';

$currentDate=empty($date)? $today: $date;
$currentDate=date('Y-m-d',strtotime($currentDate));

$currentYear = date('Y', strtotime($currentDate));
$currentMonth = date('m', strtotime($currentDate));
$currentWeekNumber = date('W', strtotime($currentDate));
$selectedYear=!empty($_REQUEST['year'])? (int)$_REQUEST['year']:$currentYear;
$selectedWeekNumber=!empty($_REQUEST['week'])? (int)$_REQUEST['week']:$currentWeekNumber;
$selectedMonth=!empty($_REQUEST['month'])? (int)$_REQUEST['month']:$currentMonth;

$currentQuarter=floor(($selectedMonth - 1) / 3) + 1;
$selectedQuarter=!empty($_REQUEST['quarter'])? (int)$_REQUEST['quarter']:$currentQuarter;

$week_array = getStartAndEndDate($selectedWeekNumber,$selectedYear);

$nextWeekString='';
$previousWeekString='';

$week_previous_year=$selectedYear;
$week_previous_number=$selectedWeekNumber-1;
$week_next_number=$selectedWeekNumber+1;
$week_next_year=$selectedYear;

if($selectedWeekNumber==52){
  $nextWeekString='week=1&year='.($selectedYear+1);
  $previousWeekString='week='.($selectedWeekNumber-1).'&year='.$selectedYear;
  $week_next_year=$selectedYear+1;
  $week_next_number=1;
  
}else if($selectedWeekNumber==1){
  $nextWeekString='week=2&year='.$selectedYear;
  $previousWeekString='week=52&year='.($selectedYear-1);
  $week_previous_number=52;
  $week_previous_year=$selectedYear-1;

}else{
  $nextWeekString='week='.($selectedWeekNumber+1).'&year='.$selectedYear;
  $previousWeekString='week='.($selectedWeekNumber-1).'&year='.$selectedYear;
}

$nextQuarter='';
$nextQuarterYear='';
$prevQuarter='';
$prevQuarterYear='';

$goals_heading='';
$evaluation_heading='';
if($type=='weekly'){
  
    $start_date=$week_array['week_start'];
     $end_date=$week_array['week_end'];

    $goals_heading='Objetivos y Prioridades esta Semana';
    $evaluation_heading='Evaluación/Progreso. Cosas para Mejorar';
}elseif($type=='monthly'){
  $start_date=$selectedYear.'-'.$selectedMonth.'-01';
  $end_date=date('Y-m-t',strtotime($start_date));
  $goals_heading='Objetivos y Prioridades ESTE Mes';
  $evaluation_heading='Evaluación/Progreso. Cosas para Mejorar';
}elseif($type=='yearly'){
   $start_date = $selectedYear. '-01-01';
   $end_date=$selectedYear. '-12-31';
   $goals_heading='Objetivos y Sueños este Año';
   $evaluation_heading='Evaluación/Progreso. Cosas para Mejorar';
}elseif($type=='lifetime'){
  $start_date = '1900-01-01';
  $end_date='2200-12-31';
  $goals_heading='Objetivos, Prioridades y Sueños para tu Vida';
  $evaluation_heading='Evaluación/Progreso. Cosas para Mejorar';
}elseif($type=='quarterly'){
  $nextQuarterYear=$selectedYear;
  if($selectedQuarter==1){
    $start_date = $selectedYear. '-01-01';
    $end_date=$selectedYear. '-03-31';
    $nextQuarter=2;
    $prevQuarter=4;
    $prevQuarterYear=$selectedYear-1;
    $nextQuarterYear=$selectedYear;
  }elseif($selectedQuarter==2){
    $start_date = $selectedYear. '-04-01';
    $end_date=$selectedYear. '-06-30';
    $nextQuarter=3;
    $nextQuarterYear=$selectedYear;
    $prevQuarter=1;
    $prevQuarterYear=$selectedYear;
  }elseif($selectedQuarter==3){
    $start_date = $selectedYear. '-07-01';
    $end_date=$selectedYear. '-09-30';
    $nextQuarter=4;
    $prevQuarter=2;
    $nextQuarterYear=$selectedYear;
    $prevQuarterYear=$selectedYear;
  }elseif($selectedQuarter==4){
    $start_date = $selectedYear. '-10-01';
    $end_date=$selectedYear. '-12-31';
    $nextQuarter=1;
    $prevQuarter=3;
    $prevQuarterYear=$selectedYear;
    $nextQuarterYear=$selectedYear+1;
  }
  

  $goals_heading='Objetivos y Prioridades este Trimestre';
  $evaluation_heading='Evaluación/Progreso. Cosas para Mejorar';
}




?>

<?php

$user_id = Session::get('user_id');
$table_name='supergoals';
$result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND DATE(start_date)>='".$start_date."' AND DATE(end_date)<='".$end_date."'");
$goals=[];
if(isset($_GET['test'])){
  $result=$common->db->select("SELECT * FROM supergoals WHERE id='226'");
  $row = $result -> fetch_assoc();
  print_r($row);
}


if($result){
  while ($row = $result -> fetch_assoc()) {
    $goals[]=$row;  
  }
}else{
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
}

$evaluation='';
$result=$common->db->select("SELECT * FROM supergoals_evaluation WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$start_date."' AND end_date<='".$end_date."'");
if($result){
  $row = $result -> fetch_assoc();
  
  $evaluation=$row['description'];
}

?>
<script>
      var SITE_URL='<?=SITE_URL; ?>';
      var goalType='<?=$type; ?>';
      var goalCounts='<?=count($goals); ?>';
      
      var startDate='<?=$start_date; ?>';
      var endDate='<?=$end_date; ?>';
      </script>
    <script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
    <script src="https://mejorcadadia.com/users/assets/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://mejorcadadia.com/users/assets/tinymce-jquery.min.js"></script>
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
      }
      .golas-area ol li{
        font-size:1.4rem;
        color:#FFF;
        margin-bottom:10px;
        padding-right:2rem;
        position:relative;
      }
      .golas-area ol li label{
        display:inline;
      }
      .golas-area ol li input{
        width:1.5rem;
        height:1.5rem;
        position: absolute;
    right: 0;
    top: 25%;
      }
      .golas-area ol li.hidden{display:none;}
      #new-goal-creation-container .form-group{margin-bottom:20px;}
      .prev-arrow i,.next-arrow i{color:#FFF; font-size:1.8rem;}
      .projects-header p{font-size:1.1rem;}
      #goal-list textarea{ width:100%;}
      @media print {       
       .golas-area ol li.hidden{display:list-item;}
      }
     .edit-actions{display:none;}
     .edit-actions i{color:#fef200;}
     
      #goals-area.edit .edit-actions{display:inline-block;}
      .has-errors input{border-color:#F00;}
      .datestr{text-transform: capitalize;}
     
      @media screen and (max-width: 767px) {
        h2.maintitle{font-size:1rem;}
        .projects-header h2{font-size:1.1rem;}
        .goals-area ol li{padding-right:2rem; min-height:56px;}
        #goals-area{padding: 20px 0px;}
        .chart-btn{right:0; top:calc(10% + 30px);}
        .goals-area ol li input{top:10%;}
       
        .projects-header{padding:10px 20px;}
      }
      .admin-dashbord{
        background:#ed008c;
      }
      .projects{border:none;}
    </style>

    <?php 
    

    
    ?>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3">
      <div class="projects mb-4" style="background-color: #ed008c;">
        <div class="projects-inner">
        <header class="projects-header" style="">
        <?php if($type!='lifetime'): ?>
          <div class="row" style="margin-bottom:15px;">
          <div class="col-sm-9"></div>
            <div class="col-sm-3">
              <div class="input-group date datepicker" id="datepicker">
              <?php $df='d-m-Y';
              if($type=='yearly'){
                $df='Y';
              }
              ?>
              <input type="text" class="form-control" value="<?=date($df,strtotime($start_date));?>" id="date" readonly/>
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
          </div>
         <?php endif; ?>

          <?php if($type=='weekly'): ?>

            <div class="row">
              <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=weekly&<?=$previousWeekString;?>";><i  class="fa fa-arrow-left"></i></a></div>
              <div class="col-sm-6 col-6" style="text-align:center;"><h2 class="">Semana</h2></div>
              <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=weekly&<?=$nextWeekString;?>"><i class="fa fa-arrow-right"></i></a></div>
            </div>
            <p><label>Semana # :</label> <span><?=$selectedWeekNumber;?></span></p>
            <p><label>De :</label> <span class="datestr"><?=localDate($start_date); ?></span></p>
            <p><label>Hasta :</label> <span class="datestr"><?=localDate($end_date); ?></span></p>
          <?php elseif($type=='monthly'): ?>
            <div class="row">
              <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=monthly&month=<?=date("m", strtotime("-1 month", strtotime($start_date)))?>&year=<?=date("Y", strtotime("-1 month", strtotime($start_date)))?>";><i  class="fa fa-arrow-left"></i></a></div>
              <div class="col-sm-6 col-6" style="text-align:center;"><h2 class="">Mes</h2></div>
              <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=monthly&&month=<?=date("m", strtotime("+1 month", strtotime($start_date)))?>&year=<?=date("Y", strtotime("+1 month", strtotime($start_date)))?>"><i class="fa fa-arrow-right"></i></a></div>
            </div>
            <p><label>Mes:</label> <span><?=$selectedMonth;?></span></p>
            <p><label>De :</label> <span class="datestr"><?=localDate($start_date);?></span></p>
            <p><label>Hasta :</label> <span class="datestr"><?=localDate($end_date); ;?></span></p>
            <?php elseif($type=='yearly'): ?>
            <div class="row">
              <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=yearly&year=<?=$selectedYear-1;?>";><i  class="fa fa-arrow-left"></i></a></div>
              <div class="col-sm-6 col-6" style="text-align:center;"><h2 class="">AÑO</h2></div>
              <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=yearly&year=<?=$selectedYear+1;?>"><i class="fa fa-arrow-right"></i></a></div>
            </div>
            <p><label>Año:</label> <span><?=$selectedYear;?></span></p>
            <p><label>De :</label> <span class="datestr"><?=localDate($start_date);?></span></p>
            <p><label>Hasta :</label> <span class="datestr"><?=localDate($end_date);?></span></p>
            <?php elseif($type=='quarterly'): ?>
            <div class="row">
              <div class="col-sm-3 col-3" style="text-align:left;"><a class="prev-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=quarterly&quarter=<?=$prevQuarter;?>&year=<?=$prevQuarterYear;?>";><i  class="fa fa-arrow-left"></i></a></div>
              <div class="col-sm-6 col-6" style="text-align:center;"><h2 class="">Trimestral</h2></div>
              <div class="col-sm-3 col-3" style="text-align:right;"><a class="next-arrow" href="<?=SITE_URL;?>/users/supergoals.php?type=quarterly&quarter=<?=$nextQuarter;?>&year=<?=$nextQuarterYear;?>"><i class="fa fa-arrow-right"></i></a></div>
            </div>
            <p><label>Trimestral:</label> <span><?=$selectedQuarter;?></span></p>
            <p><label>De :</label> <span class="datestr"><?=localDate($start_date);;?></span></p>
            <p><label>Hasta :</label> <span class="datestr"><?=localDate($end_date);;?></span></p>
            
            <?php elseif($type=='lifetime'): ?>
              <div class="row">  
              <div class="col-sm-12" style="text-align:center;"><h2 class="">De por Vida</h2></div>              
            </div>
            <?php endif; ?>
          </header> 
     
           
          
          
          <div style="background-color: #fef200; padding: 10px">
              <h2 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; "><?=$goals_heading; ?>
             
                <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn">Editar</button>
              
              </h2>
            </div>
          <form class="form" id="goalsFrom">
           
          <div class="golas-area" id="goals-area" style="display:block; padding:30px 20px;">
            
            <ol id="goal-list" class="">
              <?php foreach($goals as $key=>$item):  ?>
                <li class="<?=($key>9)? 'hidden more':'';?>" id="goal-list-item-<?=$item['id']; ?>">
                <label id="list-label-<?=$item['id']; ?>">

                      <span id="goalText-<?=$item['id']; ?>"><?=$item['goal']; ?> </span>                    
                      <input data-id="<?=$item['id']; ?>"  value="<?=$item['id']; ?>" class="input-goals" name="achieved[<?=$item['id']; ?>]" type="checkbox" <?php if($item['achieved']==1) echo 'checked'; ?>>
                      <a class="edit-actions edit-goal-btn" data-id="<?=$item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                      <a class="edit-actions delete-goal-btn" data-id="<?=$item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </label>  
                </li> 
                <?php endforeach; ?>                         
            </ol>
            <?php if(count($goals)>10): ?>
            <div class="screenonly" style="text-align:center;"><button id="morelessToggleBtn" type="button" class="btn btn-primary">Mostrar más</button></div>
            <?php endif; ?>
            <div class="form-group" id="new-goal-creation-container"></div>
            <?php if($today<$end_date): ?>
            <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-goal-btn-wrapper">
            <button type="button" id="save-new-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewGoals('<?=$type;?>')"><i class="fa fa-save"></i> Save New Goals</button>
              <button type="button" class="button btn btn-info" onClick="CreateGoal('<?=$type;?>')"><i class="fa fa-book"></i> Agrega Objetivo</button>
            </div>
            <?php endif; ?>
            <div class="form-group">
                  <div class="description-area">
                        
                          <label style="color:#FFF; font-size:1.5rem; margin:5px 0;"><?=$evaluation_heading ?></label>
                          <div class="print-description" id="print-evaluation"><?=$evaluation; ?></div>  
                          <textarea id="LetterApplication" class="LetterApplication" name="LetterApplication"><?=$evaluation; ?></textarea>
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
                       <?php if($today<=$end_date):?>
                        <input class="btn btn-info letter" type="button" id="saveBtn" name="saveBtn" value="Guardar" />
                        <?php endif; ?>
                      
                      </div>
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
            <input style="width:100%;" type="email"  class="form-control" name="toemail" id="toEmail" placeHolder="Enter Email Address">
          </div>
        </div>
        <div class="modal-footer">
          <div id="modal-msg"></div>
          <button class="btn btn-primary" type="button" id="sendBtn" name="sendBtn" >Send Email</button>
        </div>
      </div>
    </div>
  </div>
    <script>
    
      $('#show').css('display','none');
    
      tinymce.init({
        selector: 'textarea.LetterApplication',
        height: 600,
        plugins: [
          'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
          'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
          'insertdatetime', 'media', 'table', 'help', 'wordcount','autoresize',
          'autosave','codesample','directionality','emoticons','importcss',
          'nonbreaking','pagebreak','quickbars','save','template','visualchars'
        ],
        toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help' +
        'anchor | restoredraft | ' +
        'charmap | code | codesample | ' +
        'ltr rtl | emoticons | fullscreen | '+
        'image | importcss | insertdatetime | '+
        'link | numlist bullist | media | nonbreaking | '+
        'pagebreak | preview | save | searchreplace | '+
        'table tabledelete | tableprops tablerowprops tablecellprops | '+
        'tableinsertrowbefore tableinsertrowafter tabledeleterow | '+
        'tableinsertcolbefore tableinsertcolafter tabledeletecol | '+
        'template | visualblocks | visualchars | wordcount | undo redo | '+
        'blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | '+
        'bullist numlist outdent indent',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
      });
      var goalstobeadded=0;
      var newgoalsInput=[];
      function SaveNewGoals(type){
        var newgoalsinput=document.querySelectorAll("textarea.newgoals");        
        var validated=hasFilledNewGoals();
        if(newgoalsInput.length>0){

            $.ajax({
              url: SITE_URL+"/users/ajax/ajax.php",
              type: "POST",
              data: {
                saveNewGoals: 'saveNewGoals',
                  type:type,
                  startDate:startDate,
                  endDate:endDate,
                  goals:newgoalsInput
              },
              success: function (data) {                
                var jsonObj=JSON.parse(data);
                console.log('data',data,jsonObj);
                  if(jsonObj.success) {
                    goalstobeadded=0;
                    newgoalsInput=[];
                    $('#new-goal-creation-container').html('');
                    for (const prop in jsonObj.goals) {
                      console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
                      console.log(prop,jsonObj.goals[prop]);

                     
                      $("#goal-list").append('<li class="" id="goal-list-item-'+prop+'"><label class="form-label" id="list-label-'+prop+'"><span id="goalText-'+prop+'">'+jsonObj.goals[prop]+'</span> <input name="achieved['+prop+']" class="input-goals" type="checkbox" data-id="'+prop+'" value="'+prop+'"><a class="edit-actions edit-goal-btn" data-id="'+prop+'" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="'+prop+'" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
                    }
                    $('#save-new-goals-btn').hide();
                  }
              }
          });
        }
      }
      function hasFilledNewGoals(){
        var filled=true;
        newgoalsInput=[];
        $newgoalsinputEmpty=document.querySelectorAll("textarea.newgoals");
        for (var i = 0; i < $newgoalsinputEmpty.length; ++i) {           
            if($newgoalsinputEmpty[i].value==''){
              filled=false;
              $newgoalsinputEmpty[i].classList.add('is-invalid');
            }else{
              $newgoalsinputEmpty[i].classList.remove('is-invalid');
              newgoalsInput.push($newgoalsinputEmpty[i].value);
            }
        }
        return filled;
      }
      function CreateGoal(type){
        
        $wrapper=$('#new-goal-creation-container');
        var validated=hasFilledNewGoals();
        console.log('validated',validated);
        $newgoalsinput=document.querySelectorAll("textarea.newgoals");
        if(validated){
          $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newgoals' name='newgoals[]'/></textarea></div>"); 
          goalstobeadded++;
          if(goalstobeadded>0){
            $('#save-new-goals-btn').show();
          }
        }
        
        
      }
      $(document).on('click','.edit-goal-btn',function(e){
        e.preventDefault();

        var goalId=$(this).data('id');
        console.log('goalId',goalId);
        var goalText=$('#goalText-'+goalId).text();
        console.log('goalText',goalText);
        if($(this).find('.fa').hasClass('fa-pencil')){
          $(this).find('.fa').removeClass('fa-pencil');
          $(this).find('.fa').addClass('fa-save');
          $('#goalText-'+goalId).hide();
          $("#list-label-"+goalId).append('<textarea id="edittextarea-'+goalId+'">'+goalText+'</textarea>');
        }else{
          var checked=$(this).find('.input-goals').is(':checked');
          $(this).find('.fa').addClass('fa-pencil');
          $(this).find('.fa').removeClass('fa-save');
          $('#goalText-'+goalId).show();
          var goalText=$('#edittextarea-'+goalId).val();
          $('#goalText-'+goalId).text(goalText);
          $('#edittextarea-'+goalId).remove();
          var achieved=0;
          if(checked){
            achieved=1;
          }else{
            achieved=0;
          }
          $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
              UpdateSuperGoal: 'UpdateSuperGoal',
                type:goalType,
                startDate:startDate,
                endDate:endDate,
                goalText:goalText,
                achieved:achieved,
                goalId:goalId,
            },
            success: function (data) {
              console.log('data',data);
                if(data == 'Update') {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ddffff');
                    $('#success_msg_verification_text').html('Update Successfully');
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                   
                }
            }
        });
          
        }
      
       
      });
      $(document).on('click','.delete-goal-btn',function(e){
        e.preventDefault();
        var goalId=$(this).data('id');
        console.log('goalId',goalId);
        var goalIds=[];
        goalIds.push(goalId);
        $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
              DeleteGoals: 'DeleteGoals',
              type:goalType,
              startDate:startDate,
              endDate:endDate,
              goalIds:goalIds,
            },
            success: function (data) {
              console.log('data',data,goalIds);
              for (let index = 0; index < goalIds.length; index++) {
                var gid = goalIds[index];
                $("#goal-list-item-"+gid).remove();
              }
              
                if(data == 'Deleted') {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ddffff');
                    $('#success_msg_verification_text').html('Update Successfully');
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                   
                }
            }
        });
      });
      
      $('#editBtn').click(function (e) {
        if($(this).text()=='Editar'){
          $(this).text('Cancelar');
        }else{
          $(this).text('Editar');
        }
        $('#goals-area').toggleClass('edit');
      });
      $('input.input-goals').change(function () {
         var checked=$(this).is(':checked');
         var goalId=$(this).val();
         var goalText=$("#goalText-"+goalId).text();
         console.log('goalId',goalId,checked,goalText);
        var achieved=0;
        if(checked) achieved=1;
         $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
              UpdateSuperGoal: 'UpdateSuperGoal',
                type:goalType,
                startDate:startDate,
                endDate:endDate,
                goalText:goalText,
                achieved:achieved,
                goalId:goalId,
            },
            success: function (data) {
              console.log('data',data);
                if(data == 'Update') {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ddffff');
                    $('#success_msg_verification_text').html('Update Successfully');
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                   
                }
            }
        });
      });

      function UpdateGoals(){
        var LetterApplication = tinyMCE.get('LetterApplication').getContent();
        $("#print-evaluation").html(LetterApplication);
       var goalsData=[];        
        $('input.input-goals').each(function() {
          var checked=$(this).is(':checked');
          var goalId=$(this).data('id');
          var goalText=$("#goalText-"+goalId).text();
          var golaItem={};
          golaItem.id=goalId;
          golaItem.checked=(checked==true)? 1:0;
          golaItem.text=goalText;
          goalsData.push(golaItem);
        });
        
        $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
              UpdateSuperGoals: 'UpdateSuperGoals',
                type:goalType,
                description:LetterApplication,
                goalsData:goalsData,
                startDate:startDate,
                endDate:endDate,
            },
            success: function (data) {
                if(data == 'Update') {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ddffff');
                    $('#success_msg_verification_text').html('Update Successfully');
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                   
                }
            }
        });
        

      }

      
      $('#morelessToggleBtn').click(function () {     
          console.log($(this).text());  
              $("#goal-list li.more").toggleClass("hidden");
              if($(this).text()=='Mostrar más'){
                $(this).text('Mostrar Menos');
              }else{
                $(this).text('Mostrar más');
              }
      });
      $('#saveBtn').click(function () {       
        UpdateGoals();        
      });
      $('#savePrintBtn').click(function () { 
        var LetterApplication = tinyMCE.get('LetterApplication').getContent(); 
        $("#print-evaluation").html(LetterApplication);     
        window.print();       
      });
      $('#sendBtn').click(function () {
        var self=$(this);
        var btnText=$(this).text();
        $("#modal-msg").html('');
        $("#toEmail").parent().removeClass('has-errors');
        var toEmail=$("#toEmail").val(); 
        console.log('toEmail',toEmail);
       
        if(toEmail && toEmail!=''){
          $(this).text('Sending...');
          var LetterApplication = tinyMCE.get('LetterApplication').getContent();
          $.ajax({
              url: SITE_URL+"/users/ajax/ajax.php",
              type: "POST",
              data: {
                EmailSendSuperGoal: 'EmailSendSuperGoal',
                type:goalType,
                description:LetterApplication,
                startDate:startDate,
                endDate:endDate,
                toEmail:toEmail,
              },
              success: function (data) {
                $("#sendBtn").text(btnText);
                //console.log(data);
                  if(data == 'Insert') { 
                    $("#modal-msg").html('<label class="danger">Email Sent Successfully</label>')
                    setTimeout(() => {
                        $("#modal-msg").html('');                         
                      }, 1000);
                      $('#exampleModalToggle').modal('hide');
                      $('#show').css('display','block');
                      $('#error_success_msg_verification').css('color','#000000');
                      $('#error_success_msg_verification').css('background-color','#ddffff');
                      $('#success_msg_verification_text').html('Email Sent Successfully');
                      setTimeout(() => {
                          $('#show').css('display','none');
                      }, 3000);
                     
                  }
                  else {
                    $("#modal-msg").html('<label class="danger">'+data+'</label>')
                     
                      setTimeout(() => {
                        $("#modal-msg").html('');                         
                      }, 3000);
                  }
              }
          });
        }else{
          $("#toEmail").parent().addClass('has-errors');
        }
        
      });
      
      
      
      $(function() {
        if(typeof goalType !== 'undefined'){
                var calOptions={
                format:'dd-mm-yyyy',
                autoclose:true,
                calendarWeeks:true,
                todayHighlight:true,
                weekStart:1
                };
                if(goalType=='yearly'){
                    calOptions={
                        format: "yyyy",
                        viewMode: "years", 
                        minViewMode: "years"
                    }
                }
                $('.datepicker').datepicker(calOptions)
                .on('changeDate', function(e) {
                    console.log('changeDate',e.date, e.format('yyyy-mm-dd'));
                    if(goalType=='yearly'){
                        window.location.href=SITE_URL+"/users/supergoals.php?type="+goalType+"&year="+e.format('yyyy');
                    }else{
                        window.location.href=SITE_URL+"/users/supergoals.php?type="+goalType+"&date="+e.format('yyyy-mm-dd');
                    }
                
                    
                });
            }
      });
    

     
    </script>
<?php require_once "inc/footer.php"; ?>