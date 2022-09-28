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
    $UserId = Session::get('user_id');
    $AdminId = 0;
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $LetterApplication_insert = $common->insert("`letterapplication`(`email`,`emailto`,`date`,`title`,`letterapplicationtext`,`UserId`,`AdminId`)", "('$email','$emailto','$Date','$Title','$LetterApplication','$UserId','$AdminId')");
            if ($LetterApplication_insert) {
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
                $mail->isHTML(true);
                $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png'); 
                $mail->Body = '
                    <html>
                        <head>
                            <title>'.$Title.'</title>
                        </head>
                        <body>
                            <h1 style="font-size: 20px; text-align: left; font-weight: 600; font-family: sans-serif;">'.$Date.'</h1>
                            <p><center><img style="width: 10%;" src="cid:logoimg" /></center></p>
                            <h1 style="font-size: 40px; text-align: center; font-weight: 600; font-family: sans-serif;">'.$Title.'</h1>
                            <h1 style="font-size: 40px; text-align: left; font-weight: 600; font-family: sans-serif;">Message :</h1>
                            '.html_entity_decode($LetterApplication).'
                        </body>
                    </html>
                ';
                $mail->AltBody = "This is the plain text version of the email content";
                $mail->addAddress($emailto);
                if($mail->send()) {
                    echo 'Insert';
                } else {
                    echo 'Failed to send mail!';
                }
                $mail->smtpClose();
            } else {
                echo 'Something is wrong!';
            }
        } else {
            echo 'Something is wrong!';
        }
    }
    else {
        echo 'Field Fill';
    }
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
      $goalBodyHtml.='<footer style="background-color: #fef200; padding:20px;"><p style="clear:both;"><span style="float:left;">Mejorcadadia.com</span>  <span style="float:right;">All rights reserved 2022</span></p> </footer></div>';

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



?>