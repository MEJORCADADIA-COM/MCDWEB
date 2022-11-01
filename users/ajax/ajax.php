<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../../lib/Database.php');
include_once ($filepath . '/../../lib/Session.php');
include_once ($filepath . '/../../helper/Format.php');


spl_autoload_register(function($class_name) {
    include_once "../../classes/" . $class_name . ".php";
});

$database = new Database();
$format = new Format();
$common = new Common();

// phpmailer start
include_once ($filepath . "/../../PHPMailer/PHPMailer.php");
include_once ($filepath . "/../../PHPMailer/SMTP.php");
include_once ($filepath . "/../../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// phpmailer end

require_once '../../vendor/autoload.php';

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
if(!empty($_POST) && !empty($_POST['saveDinstyLetter'])){ 
    $UserId=$user_id = Session::get('user_id');
    $letterid=isset($_REQUEST['id'])? (int) $_REQUEST['id']:0;
    $date=isset($_POST['date'])? date('Y-m-d',strtotime($_POST['date'])):'';
    $email=isset($_POST['email'])? $_POST['email']:'';
    $emailto=isset($_POST['emailto'])? $_POST['emailto']:'';
    $Title=isset($_POST['Title'])? $_POST['Title']:'';
    $LetterApplication=isset($_POST['LetterApplication'])? $_POST['LetterApplication']:'';
    if(!empty($date) && !empty($email) && !empty($emailto) && !empty($Title)){
       $LetterApplication= $common->db->link->real_escape_string($LetterApplication);
      if(empty($letterid)){
        $AdminId=0;
        $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$date','$Title','$LetterApplication','$UserId','$AdminId')");
        $letterid=$common->insert_id();    
              
      }else{
        $sql="UPDATE letterapplication SET letterapplicationtext='".$LetterApplication."',  email='".$email."', emailto='".$emailto."', date='".$date."', title='".$Title."'  WHERE id=".$letterid;
        $common->db->update($sql);
      }
      header('Location: '.SITE_URL.'/users/notebook.php?id='.$letterid) ;
        exit; 
  
    }
  
   }
if(isset($_POST['LetterApplicationCheck']) && ($_POST['LetterApplicationCheck'] == 'LetterApplicationCheck')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    if (isset($LetterApplication)) {
        echo 'Insert';
    }
    else {
        echo 'Field Fill';
    }
}

if(isset($_POST['EmailSendCheck']) && ($_POST['EmailSendCheck'] == 'EmailSendCheck')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    $Title = $format->validation($_POST['Title']);
    $Date = $format->validation($_POST['Date']);
    $Date=date('Y-m-d',strtotime($Date));
    $UserId=$user_id = Session::get('user_id');
    $AdminId = 0;
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    $letterId = isset($_POST['id'])? (int)$_POST['id']:0;
    $resArr=['success'=>false,'message'=>'error','data'=>[]];
    $resArr['new']=false; 
    $resArr['letterId']=$letterId;
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $NewLetterApplication= $common->db->link->real_escape_string($LetterApplication);
            $result=$common->db->select("SELECT * FROM users WHERE id=".$user_id);
            if($result && $result->num_rows>0){
                $user = $result -> fetch_assoc();
                if(empty($letterId)){
                    $AdminId=0;
                    $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$Date','$Title','$LetterApplication','$UserId','$AdminId')");
                    $letterId=$common->insert_id();    
                    $resArr['new']=true;      
                  }else{
                     $sql="UPDATE letterapplication SET letterapplicationtext='".$LetterApplication."',  email='".$email."', emailto='".$emailto."', date='".$Date."', title='".$Title."'  WHERE id=".$letterId;
                    $common->db->update($sql);
                  }
                  $result = $common->select("`letterapplication`", "id='".$letterId."'");
                  $letterapp = mysqli_fetch_assoc($result);
                  $goalBodyHtml='<div style="width:600px; background-color:#FFF; margin:0 auto;">';
                  $goalBodyHtml.='<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
                  $goalBodyHtml.='<div style="padding:20px; background-color:#FFF; ">
                      <h2 style="text-transform: capitalize;">'.$Title.'</h2>
                      <p>Fecha: '.date('d-m-Y',strtotime($Date)).'</p> 
                      <p>De: '.$user['full_name'].'</p>        
                      <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><div style="">';
                      $goalBodyHtml.=html_entity_decode($letterapp['letterapplicationtext']);
                      $goalBodyHtml.='</div></div>      
                  </div>';
                  $goalBodyHtml.='<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0;"><span style="float:left;">Mejorcadadia.com</span>  <span style="float:right;">All rights reserved 2022</span></p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';
  
                  $AdminId = 0;
                  $Date=date('Y-m-d');
                  $sent=sendEmail($user_id,'Cartas Eternidad - '.$Title,$emailto,$goalBodyHtml);
                  if($sent===true){
                      $resArr['success']=true;
                      $resArr['letterId']=$letterId;
                  }
            }
           
          
            
        } else {
            $resArr['message']='Something is wrong!';
        }
    }
    else {
        $resArr['message']='Field Fill';
    }        
    echo json_encode($resArr);
}

if(isset($_POST['EmailSendCheckOnlySend']) && ($_POST['EmailSendCheckOnlySend'] == 'EmailSendCheckOnlySend')) {
	$LetterApplication = $format->validation($_POST['LetterApplication']);
    $Title = $format->validation($_POST['Title']);
    $Date = $format->validation($_POST['Date']);
    $id = $format->validation($_POST['id']);
    $UserId = Session::get('user_id');
    $AdminId = 0;
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $LetterApplication= $common->db->link->real_escape_string($LetterApplication);
            if(!empty($id)){
                $sql="UPDATE letterapplication SET letterapplicationtext='".$LetterApplication."',  email='".$email."', emailto='".$emailto."', date='".$Date."', title='".$Title."'   WHERE id=".$id;
                $common->db->update($sql);
                echo 'Update';
            }else{
                $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$Date','$Title','$LetterApplication','$UserId','$AdminId')");
                if ($LetterApplication_insert) {
                    echo 'Insert';
                } else {
                    echo 'Something is wrong!';
                }
            }
            
        } else {
            echo 'Something is wrong!';
        }
    }
    else {
        echo 'Field Fill';
    }
}

if(isset($_POST['EmailIdCheck']) && ($_POST['EmailIdCheck'] == 'EmailIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("`letterapplication`", "`letterapplicationtext` = '$LetterApplication'", "`id` = $id");
        echo 'Update';
    }
    else {
        echo 'Something is wrong!';
    }
}

if(isset($_POST['SaveIdCheck']) && ($_POST['SaveIdCheck'] == 'SaveIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("`letterapplication`", "`letterapplicationtext` = '$LetterApplication'", "`id` = $id");
        echo 'Update';
    }
    else {
        echo 'Something is wrong!';
    }
}

if(isset($_POST['EmailDeleteCheck']) && ($_POST['EmailDeleteCheck'] == 'EmailDeleteCheck')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("`letterapplication`", "`id` = '$id'");
        echo 'Delete';
    }
    else {
        echo 'Something is wrong!';
    }
}

if(isset($_POST['MobileSendCheck']) && ($_POST['MobileSendCheck'] == 'MobileSendCheck')) {
   
}
function pullPreviousLifeGoals($userId,$currentDate){
    global $common;
    $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$userId."'  AND created_at='".$currentDate."'");
    if($result==false || $result->num_rows<1){
        $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$userId."'  AND created_at<='".$currentDate."' ORDER BY created_at DESC LIMIT 0, 1");
        if($result){
            $row = $result -> fetch_assoc();
            $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$userId."' AND created_at='".$row['created_at']."'");
            if($result){
              while ($row = $result -> fetch_assoc()) {  
                $goalText=$row['goal'];   
                $common->insert('daily_life_goals(user_id,goal,created_at)', '("'.$userId.'","'.$goalText.'","'.$currentDate.'")');         
                
              }
            }
        } 
    }
    
}
function pullPreviousGoals($user_id,$type,$start_date,$end_date){
    global $common;
    $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$start_date."' AND end_date<='".$end_date."'");
    
    if($result==false || $result->num_rows<1){
        $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND end_date<='".$start_date."' ORDER BY end_date DESC LIMIT 0,1");
        if($result){
          $row = $result -> fetch_assoc();
          $previous_start_date=$row['start_date'];
          $previous_end_date=$row['end_date'];
          $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$previous_start_date."' AND end_date<='".$previous_end_date."'");
          if($result){
            while ($row = $result -> fetch_assoc()) { 
                $goalText=$row['goal'];             
                //$common->insert('supergoals(user_id,type,goal,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$goalText.'","'.$start_date.'","'.$end_date.'")');
            }
          }
        }
    }
}
if(isset($_POST['UpdateSuperGoal']) && ($_POST['UpdateSuperGoal'] == 'UpdateSuperGoal')) {
  
    $type=$_POST['type'];
    $user_id = Session::get('user_id');
    $achieved=isset($_POST['achieved'])? $_POST['achieved']: 0;
    $goalText=empty($_POST['goalText'])? '': $_POST['goalText'];
    $startDate=isset($_POST['startDate'])? $_POST['startDate']:'';
    $endDate=isset($_POST['endDate'])? $_POST['endDate']:'';
    $goalId=isset($_POST['goalId'])? (int)$_POST['goalId']:0;
  
   
    $goalText= $common->db->link->real_escape_string($goalText);

    if(!empty($goalId)){
        $sql="UPDATE supergoals SET  supergoals.goal='".$goalText."', supergoals.`achieved`='".$achieved."' WHERE supergoals.`id`=".$goalId;
        $common->db->update($sql);
        echo 'Updated';
    }else{
        if(!empty($goalText)){   
           
            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $result=$common->db->select("SELECT * FROM supergoals WHERE goal='".$goalText."' AND user_id='".$user_id."' AND type='".$type."' AND DATE(start_date)>='".$startDate."' AND DATE(end_date)<='".$endDate."'");
           
            if($result->num_rows){
                $row = $result->fetch_assoc();
                  $sql="UPDATE supergoals SET supergoals.`achieved`='".$achieved."' WHERE supergoals.`id`=".$row['id'];
                $common->db->update($sql);
               
            }else{
                $common->insert('supergoals(user_id,type,goal,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$goalText.'","'.$startDate.'","'.$endDate.'")');
            }
        }
        echo 'Update';
    }
    
   
}

if(isset($_POST['UpdateDailyLifeGoalChecked']) && ($_POST['UpdateDailyLifeGoalChecked'] == 'UpdateDailyLifeGoalChecked')) {  
    
    $user_id = Session::get('user_id');
    $achieved=isset($_POST['achieved'])? $_POST['achieved']: 0;
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:$today;    
    $goalId=isset($_POST['goalId'])? (int)$_POST['goalId']:0; 

    if(!empty($goalId)){
        $result=$common->db->select("SELECT * FROM dailylifegoals_marked WHERE goal_id='".$goalId."' AND user_id='".$user_id."' AND created_at='".$currentDate."'");
        if($result && $result->num_rows>0){
            $sql="UPDATE dailylifegoals_marked SET  checked='".$achieved."' WHERE goal_id='".$goalId."' AND user_id='".$user_id."' AND created_at='".$currentDate."'";
            $common->db->update($sql);
            echo 'Updated';
        }else{
            $common->insert('dailylifegoals_marked(user_id,goal_id,checked,created_at)', '("'.$user_id.'","'.$goalId.'","'.$achieved.'","'.$currentDate.'")');
        }      
        
    }
     echo 'Update';
    
   
}

if(isset($_POST['UpdateDailyGoal']) && ($_POST['UpdateDailyGoal'] == 'UpdateDailyLifeGoal')) {  
    
    $user_id = Session::get('user_id');
    $achieved=isset($_POST['achieved'])? $_POST['achieved']: 0;
    $edit=isset($_POST['edit'])? $_POST['edit']: 0;
    $goalText=empty($_POST['goalText'])? '': $_POST['goalText'];
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');    
    $goalId=isset($_POST['goalId'])? (int)$_POST['goalId']:0; 
   
    $goalText= $common->db->link->real_escape_string($goalText);

    if($edit==1 && !empty($goalId) && !empty($goalText)){
        $sql="UPDATE dailylifegoals SET  goal='".$goalText."' WHERE `id`=".$goalId;
        $common->db->update($sql);
        echo 'Updated';
    }
     echo 'Update';
    
   
}



if(isset($_POST['UpdateDailyGoal']) && ($_POST['UpdateDailyGoal'] == 'UpdateDailyTopGoal')) {
  

    $user_id = Session::get('user_id');
    $achieved=isset($_POST['achieved'])? $_POST['achieved']: 0;
    $goalText=empty($_POST['goalText'])? '': $_POST['goalText'];
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');    
    $goalId=isset($_POST['goalId'])? (int)$_POST['goalId']:0;
  
   
    $goalText= $common->db->link->real_escape_string($goalText);

    if(!empty($goalId)){
        $sql="UPDATE daily_top_goals SET  daily_top_goals.goal='".$goalText."', daily_top_goals.`achieved`='".$achieved."' WHERE daily_top_goals.`id`=".$goalId;
        $common->db->update($sql);
        echo 'Updated';
    }else{
        if(!empty($goalText)){   
           
            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $result=$common->db->select("SELECT * FROM daily_top_goals WHERE goal='".$goalText."' AND user_id='".$user_id."' AND created_at='".$currentDate."'");
           
            if($result->num_rows){
                $row = $result->fetch_assoc();
                  $sql="UPDATE daily_top_goals SET daily_top_goals.`achieved`='".$achieved."' WHERE daily_top_goals.`id`=".$row['id'];
                $common->db->update($sql);               
            }else{
                $common->insert('daily_top_goals(user_id,goal,created_at)', '("'.$user_id.'","'.$goalText.'","'.$currentDate.'")');
            }
        }
        echo 'Update';
    }
    
   
}



if(isset($_POST['UpdateDailyGoals']) && ($_POST['UpdateDailyGoals'] == 'UpdateDailyGoals')) {
    

   
    $user_id = Session::get('user_id');
    $lifeGoalsData=isset($_POST['lifeGoalsData'])? $_POST['lifeGoalsData']:[];
    $topGoalsData=isset($_POST['topGoalsData'])? $_POST['topGoalsData']:[];
    $dailyEvolution=empty($_POST['dailyEvolution'])? '': $_POST['dailyEvolution'];
    $dailyImprovements=empty($_POST['dailyImprovements'])? '': $_POST['dailyImprovements'];
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');

    
    $dailyEvolution= $common->db->link->real_escape_string($dailyEvolution);
    $dailyImprovements= $common->db->link->real_escape_string($dailyImprovements);
    
    $result=$common->db->select("SELECT * FROM dailygaols WHERE user_id='".$user_id."' AND created_at='".$currentDate."'");
    
    try{

   
    if($result){
      $row = $result -> fetch_assoc();
      $sql="UPDATE dailygaols SET improvements='".$dailyImprovements."', evolution='".$dailyEvolution."' WHERE id=".$row['id'];
        $common->db->update($sql);
    }else{        
        $common->insert('dailygaols(user_id,improvements,evolution,created_at)', '("'.$user_id.'","'.$dailyImprovements.'","'.$dailyEvolution.'","'.$currentDate.'")');
    }

    if(!empty($lifeGoalsData)){
        pullPreviousLifeGoals($user_id,$currentDate);
        foreach ($lifeGoalsData as $key => $item) {
            $id=(int)$item['id'];
            $goalText= $common->db->link->real_escape_string($item['text']);
            $achieved=(int)$item['checked'];            
            $result=$common->db->select("SELECT * FROM daily_life_goals WHERE id='".$id."' AND user_id='".$user_id."' AND created_at='".$currentDate."'");
             if($result->num_rows){
                $row = $result->fetch_assoc();
                $sql="UPDATE daily_life_goals SET achieved='".$achieved."' WHERE id=".$row['id'];
                $common->db->update($sql);
            
            }else{
                if(!empty($goalText)){
                    $common->insert('daily_life_goals(user_id,goal,created_at)', '("'.$user_id.'","'.$goalText.'","'.$currentDate.'")');
                }               
            }
        }
    }
    if(!empty($topGoalsData)){
        //pullPreviousGoals($user_id,$type,$startDate,$endDate);
        foreach ($topGoalsData as $key => $item) {
            $id=(int)$item['id'];
            $goalText= $common->db->link->real_escape_string($item['text']);
            $achieved=(int)$item['checked'];            
            $result=$common->db->select("SELECT * FROM daily_top_goals WHERE id='".$id."' AND user_id='".$user_id."' AND created_at='".$currentDate."'");
             if($result->num_rows){
                $row = $result->fetch_assoc();
                $sql="UPDATE daily_top_goals SET achieved='".$achieved."' WHERE id=".$row['id'];
                $common->db->update($sql);
            
            }else{
                if(!empty($goalText)){
                    $common->insert('daily_top_goals(user_id,goal,created_at)', '("'.$user_id.'","'.$goalText.'","'.$currentDate.'")');
                }               
            }
        }
    }
    
    }catch(Exception $exp){
        echo $exp;    
    }
    
   
    
    echo 'Update';
   

}

if(isset($_POST['UpdateSuperGoals']) && ($_POST['UpdateSuperGoals'] == 'UpdateSuperGoals')) {
    

    $type=$_POST['type'];
    $user_id = Session::get('user_id');
    $goalsData=isset($_POST['goalsData'])? $_POST['goalsData']:[];
    $description=empty($_POST['description'])? '': $_POST['description'];
    $startDate=isset($_POST['startDate'])? $_POST['startDate']:'';
    $endDate=isset($_POST['endDate'])? $_POST['endDate']:'';

    if(!empty($goalsData)){
        //pullPreviousGoals($user_id,$type,$startDate,$endDate);
        foreach ($goalsData as $key => $item) {
            $id=(int)$item['id'];
            $goalText= $common->db->link->real_escape_string($item['text']);
            $achieved=(int)$item['checked'];
            
            $result=$common->db->select("SELECT * FROM supergoals WHERE id='".$id."' AND user_id='".$user_id."' AND type='".$type."'");
       
            if($result->num_rows){
                $row = $result->fetch_assoc();
                $sql="UPDATE supergoals SET supergoals.`achieved`='".$achieved."' WHERE supergoals.`id`=".$row['id'];
                $common->db->update($sql);
            
            }else{
                if(!empty($goalText)){
                    $common->insert('supergoals(user_id,type,goal,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$goalText.'","'.$startDate.'","'.$endDate.'")');
                }
               
            }
        }
    }
    
        $result=$common->db->select("SELECT * FROM supergoals_evaluation WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$startDate."' AND end_date<='".$endDate."'");
        if($result){
          $row = $result -> fetch_assoc();
          $sql="UPDATE supergoals_evaluation SET supergoals_evaluation.`description`='".$description."' WHERE supergoals_evaluation.`id`=".$row['id'];
            $common->db->update($sql);
        }else{
            
            $common->insert('supergoals_evaluation(user_id,type,description,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$description.'","'.$startDate.'","'.$endDate.'")');
        }
    
   
    
    echo 'Update';
   

}

if(isset($_POST['DeleteGoals']) && ($_POST['DeleteGoals'] == 'DeleteGoals')) {
    $user_id = Session::get('user_id');
    $goalIds=isset($_POST['goalIds'])? $_POST['goalIds']:[];
    $type=$_POST['type'];
    $startDate=isset($_POST['startDate'])? $_POST['startDate']:date('Y-m-d h:i:s');
    $endDate=isset($_POST['endDate'])? $_POST['endDate']:date('Y-m-d h:i:s');
    $table_name='supergoals';   
    if(!empty($goalIds)){
       $common->db->delete("DELETE FROM supergoals WHERE supergoals.id IN('".implode(",",$goalIds)."')");
    }
    echo 'Deleted';
}
if(isset($_POST['DeleteDailyGoals']) && ($_POST['DeleteDailyGoals'] == 'DeleteDailyGoals')) {
    $user_id = Session::get('user_id');
    $goalIds=isset($_POST['goalIds'])? $_POST['goalIds']:[];
    $type=$_POST['type'];
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d h:i:s');     
    if(!empty($goalIds)){
        if($type=='top'){
            $common->db->delete("DELETE FROM daily_top_goals WHERE daily_top_goals.id IN('".implode(",",$goalIds)."')");
        }elseif($type=='life'){
            $common->db->delete("DELETE FROM dailylifegoals WHERE id IN('".implode(",",$goalIds)."')");
            $common->db->delete("DELETE FROM dailylifegoals_marked WHERE goal_id IN('".implode(",",$goalIds)."')");
        }      
    }
    echo 'Deleted';
}
if(isset($_POST['action']) && ($_POST['action'] == 'SaveVictory7Box')) {
    $user_id = Session::get('user_id'); 
    $box = $format->validation($_POST['box']);
    $body = $format->validation($_POST['body']);
    $selectedDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');
    $resArr=['success'=>false,'goals'=>[],'message'=>""];
    if($selectedDate<$today){
        $resArr['message']='Not allowed in past.';
    }else if($selectedDate>=$today && !empty($box)){
        $result=$common->db->select("SELECT * FROM victory7boxes WHERE user_id='".$user_id."' AND box='".$box."' AND created_at='".$today."'");
        if($result && $result->num_rows>0 ){
            $sql="UPDATE victory7boxes SET body='".$body."' WHERE user_id='".$user_id."' AND box='".$box."' AND created_at='".$today."'";
            $common->db->update($sql);           
            $resArr['success']=true;
        }else{
            $common->insert('victory7boxes(user_id,box,body,created_at)', '("'.$user_id.'","'.$box.'","'.$body.'","'.$today.'")');
            $resArr['success']=true;
        }
    }
    echo json_encode($resArr);    
    
   
}
if(isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitmentAnswer')) {
    $user_id = Session::get('user_id'); 
    $selectedDate=isset($_POST['selectedDate'])? $_POST['selectedDate']:$today;
    $table_name='daily_commitments_answers';
    $goalId = $format->validation($_POST['goalId']);
    $answer = (int)$format->validation($_POST['answer']);   

    $resArr=['success'=>false,'goals'=>[],'message'=>""];
    if($selectedDate<$today){
        $resArr['message']='Not allowed in past.';
    }else{
        $result=$common->db->select("SELECT * FROM daily_commitments_answers WHERE goal_id='".$goalId."' AND user_id='".$user_id."' AND created_at='".$selectedDate."'");
        
        if($result && $result->num_rows>0){
            echo $sql="UPDATE daily_commitments_answers SET answer='".$answer."' WHERE goal_id='".$goalId."' AND user_id='".$user_id."' AND created_at='".$selectedDate."'";
            $common->db->update($sql);           
            $resArr['success']=true;
        }else{
            $common->insert('daily_commitments_answers(user_id,goal_id,answer,created_at)', '("'.$user_id.'","'.$goalId.'","'.$answer.'","'.$selectedDate.'")');
            $resArr['success']=true;
        }

    }    
    echo json_encode($resArr);
}
if(isset($_POST['action']) && ($_POST['action'] == 'SaveNewDailyCommitments')) {
    $user_id = Session::get('user_id');
    $goals=isset($_POST['goals'])? $_POST['goals']:[];   
    $seletedDate=isset($_POST['seletedDate'])? $_POST['seletedDate']:$today;
    $table_name='daily_commitments_goals';
    $addedGoals=[];
    $resArr=['success'=>false,'goals'=>$addedGoals,'message'=>""];
    if($seletedDate<$today){
        $resArr['message']='Not allowed in past.';
    }else{
        foreach ($goals as $key => $goal) {        
            if(!empty($goal)){
                $goal= $common->db->link->real_escape_string($goal);
                $common->insert($table_name.'(user_id,goal,created_at)', '("'.$user_id.'","'.$goal.'","'.$today.'")');
                $id=$common->insert_id();
                $addedGoals[$id]=$goal;
            }        
        }
        $resArr['goals']=$addedGoals;
        $resArr['message']='Added';
        $resArr['success']=true;
    }    
    echo json_encode($resArr);
}
if(isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitment')) {
    $user_id = Session::get('user_id');
    
    $achieved=isset($_POST['achieved'])? $_POST['achieved']: 0;
    $goalText=empty($_POST['goalText'])? '': $_POST['goalText'];
    $selectedDate=isset($_POST['selectedDate'])? $_POST['selectedDate']:date('Y-m-d');    
    $goalId=isset($_POST['goalId'])? (int)$_POST['goalId']:0;
    $edit=isset($_POST['edit'])? (int)$_POST['edit']:0;
    $delete=isset($_POST['delete'])? (int)$_POST['delete']:0;
    $goalIds=isset($_POST['goalIds'])? $_POST['goalIds']:[];
    if(!empty($goalIds) && $delete==1){
        foreach ($goalIds as $key => $gid) {
           if(!empty($gid)){
            $sql="UPDATE daily_commitments_goals SET deleted_at='".$today."' WHERE id='".$gid."' AND user_id='".$user_id."'";
            $common->db->update($sql);           
            $resArr['success']=true;
           }
        }
    }else if(!empty($goalText) && !empty($goalId)){
        $sql="UPDATE daily_commitments_goals SET goal='".$goalText."' WHERE id='".$goalId."' AND user_id='".$user_id."'";
        $common->db->update($sql);           
        $resArr['success']=true;

    }
    echo 'Updated';

}
if(isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitments')) {
    $user_id = Session::get('user_id');
    $dailyEvolution=empty($_POST['dailyEvolution'])? '': $_POST['dailyEvolution'];
    $selectedDate=isset($_POST['selectedDate'])? $_POST['selectedDate']:date('Y-m-d');    
  
    $result=$common->db->select("SELECT * FROM daily_commitments_description WHERE user_id='".$user_id."' AND created_at='".$selectedDate."'");
    if($result && $result->num_rows>0 ){
        $sql="UPDATE daily_commitments_description SET description='".$dailyEvolution."' WHERE user_id='".$user_id."' AND created_at='".$selectedDate."'";
        $common->db->update($sql);           
        $resArr['success']=true;
    }else{
        $common->insert('daily_commitments_description(user_id,description,created_at)', '("'.$user_id.'","'.$dailyEvolution.'","'.$selectedDate.'")');
        
    }

    
    echo 'Updated';

}


if(isset($_POST['SaveNewDailyTopGoals']) && ($_POST['SaveNewDailyTopGoals'] == 'SaveNewDailyTopGoals')) {
    $user_id = Session::get('user_id');
    $goals=isset($_POST['goals'])? $_POST['goals']:[];   
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');
    $table_name='daily_top_goals';
    $addedGoals=[];
    //pullPreviousGoals($user_id,$type,$startDate,$endDate);
    foreach ($goals as $key => $goal) {
        
        if(!empty($goal)){
            $goal= $common->db->link->real_escape_string($goal);
            $common->insert($table_name.'(user_id,goal,created_at)', '("'.$user_id.'","'.$goal.'","'.$currentDate.'")');
            $id=$common->insert_id();
            $addedGoals[$id]=$goal;
        }        
    }
        echo json_encode(['success'=>true,'goals'=>$addedGoals]);
}
if(isset($_POST['SaveNewDailyLifeGoals']) && ($_POST['SaveNewDailyLifeGoals'] == 'SaveNewDailyLifeGoals')) {
    $user_id = Session::get('user_id');
    $goals=isset($_POST['goals'])? $_POST['goals']:[];   
    $currentDate=isset($_POST['currentDate'])? $_POST['currentDate']:date('Y-m-d');
    $table_name='dailylifegoals';
    $addedGoals=[];    
    if($currentDate>=$today){
        foreach ($goals as $key => $goal) {        
            if(!empty($goal)){
                $goal= $common->db->link->real_escape_string($goal);
                $common->insert($table_name.'(user_id,goal,created_at)', '("'.$user_id.'","'.$goal.'","'.$today.'")');
                $id=$common->insert_id();
                $addedGoals[$id]=$goal;
            }        
        }
    }    
    echo json_encode(['success'=>true,'goals'=>$addedGoals]);
}

if(isset($_POST['saveNewGoals']) && ($_POST['saveNewGoals'] == 'saveNewGoals')) {
    $user_id = Session::get('user_id');
    $goals=isset($_POST['goals'])? $_POST['goals']:[];
    $type=$_POST['type'];
    $startDate=isset($_POST['startDate'])? $_POST['startDate']:date('Y-m-d');
    $endDate=isset($_POST['endDate'])? $_POST['endDate']:date('Y-m-d');
    $table_name='supergoals';
    $addedGoals=[];
    //pullPreviousGoals($user_id,$type,$startDate,$endDate);
    foreach ($goals as $key => $goal) {
        
        if(!empty($goal)){
            $goal= $common->db->link->real_escape_string($goal);
            $common->insert($table_name.'(user_id,type,goal,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$goal.'","'.$startDate.'","'.$endDate.'")');
            $id=$common->insert_id();
            $addedGoals[$id]=$goal;
        }
        
    }
    echo json_encode(['success'=>true,'goals'=>$addedGoals]);

}

if(isset($_POST['EmailSendDailyGoal']) && ($_POST['EmailSendDailyGoal'] == 'EmailSendDailyGoal')) {
	$dailyImprovements = $format->validation($_POST['dailyImprovements']);
    $dailyEvolution = $format->validation($_POST['dailyEvolution']); 
    $toEmail = $format->validation($_POST['toEmail']);
    $currentDate = date('Y-m-d',strtotime($format->validation($_POST['currentDate'])));
    $user_id = Session::get('user_id');

    
    $dailyTopGoals=[];
    $dailyLifeGoals=[];
   

    $result=$common->db->select("SELECT * FROM daily_top_goals WHERE user_id='".$user_id."' AND created_at='".$currentDate."'");
    if($result){
    while ($row = $result -> fetch_assoc()) {
        $dailyTopGoals[]=$row;  
    }
    }

    $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$user_id."' AND created_at='".$currentDate."'");
    if($result){
    while ($row = $result -> fetch_assoc()) {
        $dailyLifeGoals[]=$row;  
    }
    }else{
    $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$user_id."'  AND created_at<='".$currentDate."' ORDER BY created_at DESC LIMIT 0, 1");
    if($result){
        $row = $result -> fetch_assoc();
        $result=$common->db->select("SELECT * FROM daily_life_goals WHERE user_id='".$user_id."' AND created_at='".$row['created_at']."'");
        if($result){
        while ($row = $result -> fetch_assoc()) {
    
            $dailyLifeGoals[]=$row;  
        }
        }
    } 
    }
    
    $topGoalsHtml='<ol>';
    foreach ($dailyTopGoals as $goal) {
      $topGoalsHtml.='<li>'.$goal['goal'].'</li>';    
    }
    $topGoalsHtml.='</ol>';

    $lifeGoalsHtml='<ol>';
    foreach ($dailyLifeGoals as $goal) {
      $lifeGoalsHtml.='<li>'.$goal['goal'].'</li>';    
    }
    $lifeGoalsHtml.='</ol>';

      $goalBodyHtml='<div style="width:600px; background-color:#FFF; margin:0 auto;">';
      $goalBodyHtml.='<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
      $goalBodyHtml.='<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">'.date('l F d , Y',strtotime($currentDate)).'</h2>  
            
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;"><h4>Objectives and priorities today: 7-Objetivos y Prioridades Hoy</h4> '.$topGoalsHtml.'</div>  
        
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Resumen del Día. Las 7-Victorias o Triunfos Hoy</h4><div style="">'.html_entity_decode($dailyEvolution).'</div></div>      
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;"><h4>Qué Podías haber hecho Mejor?</h4>'.$lifeGoalsHtml.'</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Tus 7-Objetivos y Prioridades Más Importantes para tu Vida:</h4><div style="">'.html_entity_decode($dailyImprovements).'</div></div>   
      </div>';
      $goalBodyHtml.='<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0;"><span style="float:left;">Mejorcadadia.com</span>  <span style="float:right;">All rights reserved 2022</span></p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date=date('Y-m-d');
    $result=$common->db->select("SELECT * FROM users WHERE id=".$user_id);
    if($result){
        $user = $result -> fetch_assoc();
        //print_r($user);
        if($user){
            $Title="Victory-7";
            $email = 'verify@mejorcadadia.com';
            $email = $user['gmail'];
            $from=$user['full_name'].'<'.$email.'>';
            
            $mail = new PHPMailer();
            $mail->isSMTP();
            
            $mail->Host = "smtp.ionos.es";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = "verify@mejorcadadia.com";
            $mail->Password = "Ta$77!/8H7u/SX?";
            $mail->Subject = $Title;
            $mail->setFrom($email);
            $mail->addReplyTo('verify@mejorcadadia.com');
            $mail->addReplyTo($email);
            $mail->isHTML(true);
           // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png'); 
            $mail->Body = '
                    <html>
                        <head>
                            <title>'.$Title.'</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            '.$goalBodyHtml.'
                        </div>
                        </body></html>';
            $mail->AltBody = "This is the plain text version of the email content";
            //$emailto='ehsan.ullah.tarar@gmail.com';
            $mail->addAddress($toEmail);
            if($mail->send()) {
                    echo 'Insert';
            } else {
                    echo 'Failed to send mail!';
            }
            $mail->smtpClose();
        }else{
            echo 'Something is wrong!!';
        }
    }else{
        echo 'Something is wrong!';
    }   
    
    
}

if(isset($_POST['EmailSendSuperGoal']) && ($_POST['EmailSendSuperGoal'] == 'EmailSendSuperGoal')) {
	$description = $format->validation($_POST['description']);
    $type = $format->validation($_POST['type']);
    $toEmail = $format->validation($_POST['toEmail']);
    $startDate = date('Y-m-d',strtotime($format->validation($_POST['startDate'])));
    $endDate = date('Y-m-d',strtotime($format->validation($_POST['endDate'])));
   
    $user_id = Session::get('user_id');
    $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$startDate."' AND end_date<='".$endDate."'");
    $goals=[];
    if($result){
        while ($row = $result -> fetch_assoc()) {
            $goals[]=$row;  
        }
    }
    $result=$common->db->select("SELECT * FROM users WHERE id=".$user_id);
    $df='d-m-Y';
    if($type=='yearly'){
      $df='Y';
    }
    $goalsHtml='<ol>';
    foreach ($goals as $goal) {
      $goalsHtml.='<li>'.$goal['goal'].'</li>';    }
      $goalsHtml.='</ol>';
      $goalBodyHtml='<div style="width:600px; background-color:#FFF; margin:0 auto;">';
      $goalBodyHtml.='<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
      $goalBodyHtml.='<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">'.$type.' Super Goals</h2>
        <p><label>From :</label> <span>'.date('l F d , Y',strtotime($startDate)).'</span></p>
        <p><label>To :</label> <span>'.date('l F d , Y',strtotime($endDate)).'</span></p>
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;">'.$goalsHtml.'</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Evaluation / Progress this year; things to improve</h4><div style="">'.html_entity_decode($description).'</div></div>      
      </div>';
      $goalBodyHtml.='<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0;"><span style="float:left;">Mejorcadadia.com</span>  <span style="float:right;">All rights reserved 2022</span></p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date=date('Y-m-d');
    if($result){
        $user = $result -> fetch_assoc();
        //print_r($user);
        if($user){
            $Title="SuperGoals - ".$type;
            $email = 'verify@mejorcadadia.com';
            $email = $user['gmail'];
            $from=$user['full_name'].'<'.$email.'>';
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = "smtp.ionos.es";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = "verify@mejorcadadia.com";
            $mail->Password = "Ta$77!/8H7u/SX?";
            $mail->Subject = $Title;
            $mail->setFrom($email);
            $mail->addReplyTo('verify@mejorcadadia.com');
            $mail->addReplyTo($email);
            $mail->isHTML(true);
           // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png'); 
            $mail->Body = '
                    <html>
                        <head>
                            <title>'.$Title.'</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            '.$goalBodyHtml.'
                        </div>
                        </body></html>';
            $mail->AltBody = "This is the plain text version of the email content";
            //$emailto='ehsan.ullah.tarar@gmail.com';
            $mail->addAddress($toEmail);
            if($mail->send()) {
                    echo 'Insert';
            } else {
                    echo 'Failed to send mail!';
            }
            $mail->smtpClose();
        }else{
            echo 'Something is wrong!';
        }
    }else{
        echo 'Something is wrong!';
    }   
    
    
}

if(isset($_POST['action']) && ($_POST['action'] == 'EmailSendDailyCommitment')) {
	$description = $format->validation($_POST['dailyEvolution']);
    $toEmail = $format->validation($_POST['toEmail']);
    $selectedDate = $format->validation($_POST['selectedDate']);
   
    $user_id = Session::get('user_id');
    if($selectedDate<$today){
        $goalDate=$selectedDate;       
      }else{
        $goalDate=$today;
      }
    $goals=[];
    $result=$common->db->select("SELECT * FROM daily_commitments_goals WHERE user_id='".$user_id."' AND created_at<='".$goalDate."' AND (deleted_at IS NULL OR deleted_at>'".$goalDate."')");
    if($result){
        while ($row = $result -> fetch_assoc()) {
            $resultAns=$common->db->select("SELECT * FROM daily_commitments_answers WHERE user_id='".$user_id."' AND goal_id='".$row['id']."' AND created_at='".$selectedDate."'");
            if($resultAns && $resultAns->num_rows>0){
            $ansRow = $resultAns -> fetch_assoc();
                $row['answer']=$ansRow['answer'];      
            }else{
                $row['answer']=0;
            }
            $goals[]=$row;  
        }
    }
    
    $goalsHtml='<ol>';
    foreach ($goals as $goal) {
      $goalsHtml.='<li>'.$goal['goal'].'</li>';   
     }
      $goalsHtml.='</ol>';
      $goalBodyHtml='<div style="width:600px; background-color:#FFF; margin:0 auto;">';
      $goalBodyHtml.='<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
      $goalBodyHtml.='<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">Guerrero Diario </h2>
        <p><label>'.date('l F d , Y',strtotime($selectedDate)).'</label></p>     
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;">'.$goalsHtml.'</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Evaluación y Mejoramiento</h4><div style="">'.html_entity_decode($description).'</div></div>      
      </div>';
      $goalBodyHtml.='<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0;"><span style="float:left;">Mejorcadadia.com</span>  <span style="float:right;">All rights reserved 2022</span></p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date=date('Y-m-d');
    sendEmail($user_id,'Guerrero Diario',$toEmail,$goalBodyHtml);
    
    
}

function sendEmail($user_id,$Title,$toEmail,$body){
    global $common;
    $result=$common->db->select("SELECT * FROM users WHERE id=".$user_id);
    if($result){
        $user = $result -> fetch_assoc();
        //print_r($user);
        if($user){
           
            $fromEmail = 'verify@mejorcadadia.com';
            $email = $user['gmail'];
            $from=$user['full_name'].'<'.$fromEmail.'>';
            $mail = new PHPMailer();
            $mail->isSMTP();
           // $mail->SMTPDebug = 2;
            $mail->Host = "smtp.ionos.es";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = "verify@mejorcadadia.com";
            $mail->Password = "Ta$77!/8H7u/SX?";
            $mail->Subject = $Title;
            $mail->setFrom($fromEmail,$user['full_name']);
            $mail->addReplyTo('verify@mejorcadadia.com');
            $mail->addReplyTo($email);
            $mail->isHTML(true);
           // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png'); 
            $mail->Body = '
                    <html>
                        <head>
                            <title>'.$Title.'</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            '.$body.'
                        </div>
                        </body></html>';
            $mail->AltBody = "This is the plain text version of the email content";
            //$emailto='ehsan.ullah.tarar@gmail.com';
            $mail->addAddress($toEmail);
            if($mail->send()) {
                    return true;
            } else {
                  
                    echo 'Mailer Error: ' . $mail->ErrorInfo;
                
            }
            $mail->smtpClose();
        }else{
            echo 'Something is wrong!';
        }
    }else{
        echo 'Something is wrong!';
    }  
}



?>