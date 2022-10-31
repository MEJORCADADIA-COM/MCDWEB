<?php
    /*Just for your server-side code*/
   // header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php require_once "inc/header.php"; ?>
<?php 
function localDate($ctime,$format="%A, %d %B, %Y"){

  setlocale(LC_ALL,"es_ES");
  $string = date('d/m/Y',$ctime);
  $dateObj = DateTime::createFromFormat("d/m/Y", $string);
  return utf8_encode(strftime($format,$dateObj->getTimestamp()));
}
$user_id = Session::get('user_id');
$cronovida=[];
$result=$common->db->select("SELECT * FROM cronovida WHERE user_id='".$user_id."'");
if($result && $result->num_rows>0 ){
  $cronovida = $result -> fetch_assoc();
}
if(isset($_POST) && !empty($_POST)){
 
  $dob=isset($_POST['dob'])? $_POST['dob']:[];
  $max_years=isset($_POST['max_years'])? $_POST['max_years']:0;
  if(!empty($dob) && !empty($max_years)){
    $birthday=date('Y-m-d',mktime(0, 0, 0, $dob['month'], $dob['day'], $dob['year']));

    if(empty($cronovida)){
      $common->insert('cronovida(user_id,dob,age)', '("'.$user_id.'","'.$birthday.'","'.$max_years.'")');         
    }else{
      $sql="UPDATE cronovida SET dob='".$birthday."', age='".$max_years."' WHERE user_id=".$user_id;
      $common->db->update($sql);
    }
  }
}
$result=$common->db->select("SELECT * FROM cronovida WHERE user_id='".$user_id."'");
if($result && $result->num_rows>0 ){
  $cronovida = $result -> fetch_assoc();
}

if(isset($_GET['timezoneoffset'])){
  $_SESSION['timezoneOffset']=$_GET['timezoneoffset'];
  header('Location: dailycommitments.php');
}
$timezoneOffset= empty($_SESSION['timezoneOffset'])? '': $_SESSION['timezoneOffset']; 
$time=time();
if(!empty($timezoneOffset)){
  $timezoneHours=($timezoneOffset-240)/60;
  $timeHourString='';
  if($timezoneHours<0){  
    
  $timeHourString='+'.abs($timezoneHours);
  }else{
    
    $timeHourString='-'.abs($timezoneHours);
  }  
  $time=strtotime($timeHourString.' hours');
}


$today = date("Y-m-d", $time);
$type='commitment';
$date=!empty($_REQUEST['date'])? $_REQUEST['date']:'';
$goalId=!empty($_REQUEST['id'])? (int)$_REQUEST['id']:0;
$selectedDate=empty($date)? $today: $date;
$selectedMonth = date('m',strtotime($selectedDate)); 			     
$selectedYear = date('Y',strtotime($selectedDate));

$ym = date('Y-m',strtotime($selectedDate));




?>
<?php if($timezoneOffset==''): ?>
  <script>
 var browserTime = new Date();
  var timezoneOffset=browserTime.getTimezoneOffset();
  window.location.href="cronovida.php?timezoneoffset="+timezoneOffset;

  </script>
<?php endif; ?>
<?php



?>


<script>
 

   
      var SITE_URL='<?=SITE_URL; ?>';
      var selectedDate='<?=$selectedDate; ?>';
      </script>
    <script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
    <script src="https://mejorcadadia.com/users/assets/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://mejorcadadia.com/users/assets/tinymce-jquery.min.js"></script>
    <script src="<?=SITE_URL; ?>/users/assets/countdown.min.js"></script>
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
      .user-cronovida-area form{max-width:400px; margin:0 auto;}
      .user-cronovida-area form .form-group{margin-bottom:20px;}
      .user-cronovida-area form .form-group label{color:#FFF; font-size:1.2rem;}
      #user-cronovida-area{display:none;}
      #countdown-wrapper{
        display: block;
      text-align: center; 
      clear:both; overflow:hidden;
      }
      ul.clock{
        list-style: none;
        display: inline-block;
        margin:0;
        padding:0;
      }
      ul.clock li{       
       float:left;
       margin-right:15px;
      }
      ul.clock li i {
        display: block;
        width: 90px;
        height: 90px;
        line-height: 90px;
        font-size: 60px;
        font-weight: bold;
        margin: auto;
        border-radius: 10px;
        box-shadow: 0 0 10px rgb(0 0 0 / 30%);
        background: #fff;
        color: #fdaf40;
        font-style: normal;
    }
    ul.clock li label {
        display: block;
        color: #fff;
        text-transform: uppercase;
        font-size: 14px;
        font-weight: bold;
        line-height: 1;
        margin-top: 10px;
    }

      @media screen and (max-width: 767px) {
        h2.maintitle{font-size:1.3rem;}
        .projects-header h2{font-size:1.1rem;}
        ul.clock li i{
          width:48px;
          height:48px;
          font-size:24px;
          line-height: 48px;
        }
        ul.clock li{
          margin-right:5px;
        }
        ul.clock li label {
          font-size: 10px;
        }
      }
      
      @media print {       
       
      }
     
      .admin-dashbord{
        background:#ed008c;
      }
      .projects{border:none;}
      @media (min-width: 1200px){
        
      }
     
    </style>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 " >
      <div class="projects mb-4" style="background-color: #ed008c; min-height: 100vh;" >
        <div class="projects-inner" style="padding-left:10px; padding-right:10px;">
        <header class="projects-header" style="">
        
         <?php setlocale(LC_ALL,"es_ES");
            $string = date('d/m/Y',strtotime($selectedDate));
            $dateObj = DateTime::createFromFormat("d/m/Y", $string);
            ?>
        
         <div class="row">         
              <div class="col-sm-12 col-12" style="text-align:center;">
              <h1 style="text-transform: capitalize;">CronoVida</h1>
              <h4>El Reloj de tu Vida</h4>
            
            </div>             
         </div>
                      
          </header> 
          <div class="mt-1 mb-2" style="color:#FFF; font-size:1rem;">
          <h6 class="text-center">
          CronoVida, el Reloj para tu Vida calcula cuanto tiempo te queda
de Vida calculando a Partir de 
Cuantos años Crees que vas a Vivir. 
Lo Puedes Cambiar en Cualquier Momento. Tu decides.
          </h6>
          </div>


          <div class="cronovida-meter-button text-center align-center mt-5">
          <?php if(!empty($cronovida)):
            
            $dob=date('Y-m-d',strtotime($cronovida['dob']));
            $age=(int)$cronovida['age'];
            $deathDate = date('Y-m-d', strtotime($dob. ' + '.$age.' years'));
            
            ?>
            <div id="countdown-wrapper" class="mb-5 mt-5">
              <ul class="clock">
                <li>
                  <i class="years">00</i>
                  <label>Años</label>
                </li>
                <li>
                <i class="days">00</i>
                  <label>Días</label>
                </li>
                <li>
                <i class="hours">00</i>
                  <label>Horas</label>
                </li>
                <li>
                <i class="minutes">00</i>
                  <label>Minutos</label>
                </li>
                <li>
                <i class="seconds">00</i>
                  <label>Segundos</label>
                </li>
              </ul>
            </div>
           

           <script>
               $(function() {
                countdown.setLabels(
	' milissegundo| segundo| minuto| hora| dia| semana| mês| ano| década| século| milênio',
	' milissegundos| Segundos| Minutos| Horas| Días| semanas| Meses| Años| décadas| séculos| milênios',
	' & ',
	' , ',
	'agora');
                var timerId = countdown(
            new Date(<?=date('Y',strtotime($deathDate));?>,<?=date('m',strtotime($deathDate));?>,<?=date('d',strtotime($deathDate));?>),
            function(ts) {
             // console.log(ts);
              $("#countdown-wrapper .clock i.years").text(ts.years.toString().padStart(2, '0'));
              $("#countdown-wrapper .clock i.months").text(ts.months.toString().padStart(2, '0'));
              $("#countdown-wrapper .clock i.days").text(ts.days.toString().padStart(2, '0'));
              $("#countdown-wrapper .clock i.hours").text(ts.hours.toString().padStart(2, '0'));
              $("#countdown-wrapper .clock i.minutes").text(ts.minutes.toString().padStart(2, '0'));
              $("#countdown-wrapper .clock i.seconds").text(ts.seconds.toString().padStart(2, '0'));
             // document.getElementById('countdown').innerHTML = ts.toHTML("strong");
            },
            countdown.YEARS |    countdown.MONTHS |    countdown.DAYS |countdown.HOURS|countdown.MINUTES|countdown.SECONDS);

        // later on this timer may be stopped
        //console.log('timerId',timerId);
        //window.clearInterval(timerId);
                
              });
              </script>
            <?php endif; ?>
            <div id="user-cronovida-area" class="user-cronovida-area align-left" style="text-align:left;">
             <?php $selected_month='';
             $selected_day = '';
             $selected_year='';
             if(!empty($cronovida) && !empty($cronovida['dob'])){
              $selected_month=date('m',strtotime($cronovida['dob']));
              $selected_day=date('d',strtotime($cronovida['dob']));
              $selected_year=date('Y',strtotime($cronovida['dob']));
             }
             
              ?>
            <form class="x" method="POST" action="">
                <div class="form-group">
                  <label class="form-label">Escribe fecha de Nacimiento</label>
                  <div class="row">
                    <div class="col">
                    
                    <select id="inputMonth" name="dob[month]" class="form-select">
                      <option>Choose...</option>
                      <?php 
                      for ($i_month = 1; $i_month <= 12; $i_month++) { 
                        $selected = ($selected_month == $i_month ? ' selected' : '');
                        echo '<option value="'.$i_month.'"'.$selected.'>'. localDate(mktime(0,0,0,$i_month),"%B").'</option>'."\n";
                    } ?>
                    </select>
                    <label for="inputMonth" class="form-label">Mes</label>
                    </div>
                    <div class="col">
                    
                    <select id="inputDay" name="dob[day]" class="form-select">
                      <option>Choose...</option>
                      <?php
                      for ($i_day = 1; $i_day <= 31; $i_day++) { 
                        $selected = ($selected_day == $i_day ? ' selected' : '');
                        echo '<option value="'.$i_day.'"'.$selected.'>'.$i_day.'</option>'."\n";
                    }
                      ?>
                    </select>
                    <label for="inputDay" class="form-label">Es</label>
                    </div>
                    <div class="col">
                    
                    <select id="inputYear" name="dob[year]" class="form-select">
                      <option >Choose...</option>
                      <?php
                       for ($i_year = 1900; $i_year <= date('Y'); $i_year++) {
                        $selected = ($selected_year == $i_year ? ' selected' : '');
                        echo '<option value="'.$i_year.'"'.$selected.'>'.$i_year.'</option>'."\n";
                      }
                      ?>
                    </select>
                    <label for="inputYear" class="form-label">Año</label>
                    </div>
                  </div>
                    
                </div>
                <div class="form-group">
                    <label class="form-label">Número de Años Crees que vas a Vivir</label>
                    <input type="number" class="form-control" name="max_years" value="<?php if(!empty($cronovida) && !empty($cronovida['age'])){ echo $cronovida['age'];} ?>" placeholder="Escribe número de Años" required min="0" max="200"/>
                 </div>
                 <div class="form-group">
                  <button type="submit" class="btn btn-warning btn-lg">Submit</button>
                 </div>

              </form>
            </div>
            <button class="btn btn-warning btn-lg" id="start-cronovida-btn">
            Actualiza tu Reloj            
            </button>
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
      function showToast(type='success',message=''){
       
        $('#toast .toast-body').html(message);
        if(type=='success'){
          $('#toast').addClass('bg-primary text-white');
          $('#toast').removeClass('bg-danger text-white');
        }else{
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
      
      $(document).on('click','#start-cronovida-btn',function(e){
        e.preventDefault();
        $("#user-cronovida-area").show();
        $(this).hide();       
       
      });
     
    </script>
<?php require_once "inc/footer.php"; ?>