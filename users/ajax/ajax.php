<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../../lib/Database.php');
include_once($filepath . '/../../lib/Session.php');
include_once($filepath . '/../../helper/Format.php');
include_once($filepath . '/../../lib/RememberCookie.php');
include_once base_path('/users/repositories/dailyVictories.php');
include_once base_path('/users/repositories/toRemember.php');


spl_autoload_register(function ($class_name) {
    include_once "../../classes/" . $class_name . ".php";
});

$database = new Database();
$format = new Format();
$common = new Common();

// phpmailer start
include_once($filepath . "/../../PHPMailer/PHPMailer.php");
include_once($filepath . "/../../PHPMailer/SMTP.php");
include_once($filepath . "/../../PHPMailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// phpmailer end

require_once '../../vendor/autoload.php';

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
if (!empty($_POST) && !empty($_POST['saveDinstyLetter'])) {
    $UserId = $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $letterid = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
    $date = isset($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $emailto = isset($_POST['emailto']) ? $_POST['emailto'] : '';
    $Title = isset($_POST['Title']) ? $_POST['Title'] : '';
    $LetterApplication = isset($_POST['LetterApplication']) ? $_POST['LetterApplication'] : '';
    if (!empty($date) && !empty($email) && !empty($emailto) && !empty($Title)) {

        if (empty($letterid)) {
            $AdminId = 0;
            $LetterApplication_insert = $common->insert("letterapplication", [
                'email' => $email,
                'emailto' => $emailto,
                'date' => $date,
                'title' => $Title,
                'letterapplicationtext' => $LetterApplication,
                'UserId' => $UserId,
                'AdminId' => $AdminId,
            ]);
            $letterid = $common->insertId();

        } else {
            $common->update(
                'letterapplication',
                ['letterapplicationtext' => $LetterApplication, 'email' => $email, 'emailto' => $emailto, 'date' => $date, 'title' => $Title],
                'id = :id',
                ['id' => $letterid],
                false
            );
        }
        header('Location: ' . SITE_URL . '/users/notebook.php?id=' . $letterid);
        exit;

    }

}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['daily_inspirations']) && $_GET['daily_inspirations'] === 'daily_inspirations') {
    $user_id = Session::get('user_id');
    echo $selectedDate=isset($_GET['date'])? $_GET['date']:'';
   
    $inspirations = $common->paginate(table: 'daily_inspirations', limit: 50, orderBy: 'date', order: 'desc');
    $totalPage = $common->pageCount(table: 'daily_inspirations', limit: 50);
    
   setlocale(LC_ALL, "es_ES");
   foreach($inspirations as $k=>$row){
       $string = date('d/m/Y', strtotime($row['date']));
       $dateObj = DateTime::createFromFormat("d/m/Y", $string);
       $row['local_date']=utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()));
       $inspirations[$k]=$row;
   }
   return response(['success' => true, 'data' => ['inspirations' => $inspirations, 'total_page' => $totalPage, 'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1]]);

}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['get_supergoals_summary']) && $_GET['get_supergoals_summary'] === 'get_supergoals_summary') {
     $user_id = Session::get('user_id');
     $selectedDate=isset($_GET['date'])? $_GET['date']:'';
     $startDate=isset($_GET['startDate'])? $_GET['startDate']:'';
     $endDate=isset($_GET['endDate'])? $_GET['endDate']:'';
       $type=isset($_GET['type'])? $_GET['type']:'weekly'; 

    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tag=$_GET['tag'];        
        $totalPage=1;
        if(empty($startDate) && !empty($endDate)){
            $evolutions = $common->db->query("SELECT * FROM supergoals_evaluation WHERE user_id='".$user_id."' AND type='".$type."' AND description LIKE '%".$tag."%'")->fetchAll();;
        }else{
            $evolutions = $common->db->query("SELECT * FROM supergoals_evaluation WHERE user_id='".$user_id."' AND type='".$type."' AND description LIKE '%".$tag."%' AND DATE(start_date)='".$selectedDate."'")->fetchAll();;
        }
       
        
    } else {
      
        if(!empty($startDate) && !empty($endDate)){          
          
            $evolutions = $common->paginate(table: 'supergoals_evaluation', cond: 'type=:type AND description!="" AND user_id = :user_id AND DATE(start_date)>="'.$startDate.'" AND DATE(start_date)<="'.$endDate.'"', params: ['user_id' => $user_id,'type'=>$type], orderBy: 'start_date', order: 'desc');
         
            $totalPage = $common->pageCount(table: 'supergoals_evaluation', cond: 'type=:type AND description!="" AND user_id = :user_id AND DATE(start_date)>="'.$startDate.'" AND DATE(start_date)<="'.$endDate.'"', params: ['user_id' => $user_id,'type'=>$type]);
       
        }else{
            $evolutions = $common->paginate(table: 'supergoals_evaluation', cond: 'type=:type AND description!="" AND user_id = :user_id', params: ['user_id' => $user_id,'type'=>$type], orderBy: 'start_date', order: 'desc');
            $totalPage = $common->pageCount(table: 'supergoals_evaluation', cond: 'type=:type AND description!="" AND user_id = :user_id', params: ['user_id' => $user_id,'type'=>$type]);
           
        }
       
        
        
        
    }
    setlocale(LC_ALL, "es_ES");
    foreach($evolutions as $k=>$row){
        $string = date('d/m/Y', strtotime($row['start_date']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        $row['local_date']=utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()));
        $evolutions[$k]=$row;
    }
    return response(['success' => true, 'data' => ['evolutions' => $evolutions, 'total_page' => $totalPage, 'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1]]);

}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['get_improvements']) && $_GET['get_improvements'] === 'get_improvements') {
    $UserId = $user_id = Session::get('user_id');
    $selectedDate=isset($_GET['date'])? $_GET['date']:'';
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tag=$_GET['tag'];        
        $totalPage=1;
        if(!empty($selectedDate)){
            $improvements = $common->db->query("SELECT * FROM dailygaols WHERE user_id='".$user_id."' AND improvements LIKE '%".$tag."%' AND DATE(created_at)='".$selectedDate."'")->fetchAll();;
        }else{
            $improvements = $common->db->query("SELECT * FROM dailygaols WHERE user_id='".$user_id."' AND improvements LIKE '%".$tag."%'")->fetchAll();;
        }
        
        
    } else {
        if(!empty($selectedDate)){
            $improvements = $common->paginate(table: 'dailygaols', cond: 'improvements!="" AND user_id = :user_id AND DATE(created_at)=:selectedDate', params: ['user_id' => $user_id,'selectedDate'=>$selectedDate], columns:['improvements','id','created_at','modified'], orderBy: 'created_at', order: 'desc');
            $totalPage = $common->pageCount(table: 'dailygaols', cond: 'improvements!="" AND user_id = :user_id AND DATE(created_at)=:selectedDate', params: ['user_id' => $user_id,'selectedDate'=>$selectedDate]);
        }else{
           
            $improvements = $common->paginate(table: 'dailygaols', cond: 'improvements!="" AND user_id = :user_id', params: ['user_id' => $user_id], columns:['improvements','id','created_at','modified'], orderBy: 'created_at', order: 'desc');
            $totalPage = $common->pageCount(table: 'dailygaols', cond: 'improvements!="" AND user_id = :user_id', params: ['user_id' => $user_id]);
        }
       
    }
    setlocale(LC_ALL, "es_ES");
    foreach($improvements as $k=>$row){
        $string = date('d/m/Y', strtotime($row['created_at']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        $row['local_date']=utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()));
        $improvements[$k]=$row;
    }
    return response(['success' => true, 'data' => ['improvements' => $improvements, 'total_page' => $totalPage, 'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1]]);

}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['get_evolutions']) && $_GET['get_evolutions'] === 'get_evolutions') {
    $UserId = $user_id = Session::get('user_id');
    $selectedDate=isset($_GET['date'])? $_GET['date']:'';
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tag=$_GET['tag'];        
        $totalPage=1;
        if(!empty($selectedDate)){
            $evolutions = $common->db->query("SELECT * FROM dailygaols WHERE user_id='".$user_id."' AND evolution LIKE '%".$tag."%' AND DATE(created_at)='".$selectedDate."'")->fetchAll();
        }else{
            $evolutions = $common->db->query("SELECT * FROM dailygaols WHERE user_id='".$user_id."' AND evolution LIKE '%".$tag."%'")->fetchAll();;
        }       
        
    } else {
        if(!empty($selectedDate)){
         
            $evolutions = $common->paginate(table: 'dailygaols', cond: 'evolution!="" AND user_id = :user_id AND DATE(created_at)=:selectedDate', params: ['user_id' => $user_id,'selectedDate'=>$selectedDate], columns:['evolution','id','created_at','modified'], orderBy: 'created_at', order: 'desc');
            $totalPage = $common->pageCount(table: 'dailygaols', cond: 'evolution!="" AND user_id = :user_id AND DATE(created_at)=:selectedDate', params: ['user_id' => $user_id,'selectedDate'=>$selectedDate]);
        }else{
            
            $evolutions = $common->paginate(table: 'dailygaols', cond: 'evolution!="" AND user_id = :user_id', params: ['user_id' => $user_id], columns:['evolution','id','created_at','modified'], orderBy: 'created_at', order: 'desc');
            $totalPage = $common->pageCount(table: 'dailygaols', cond: 'evolution!="" AND user_id = :user_id', params: ['user_id' => $user_id]);
        }
    }
    setlocale(LC_ALL, "es_ES");
    foreach($evolutions as $k=>$row){
        $string = date('d/m/Y', strtotime($row['created_at']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        $row['local_date']=utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()));
        $evolutions[$k]=$row;
    }
    return response(['success' => true, 'data' => ['evolutions' => $evolutions, 'total_page' => $totalPage, 'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1]]);

}
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] === 'get_user_photo_folders') {
    $UserId = $user_id = Session::get('user_id');
   
    $userFolders = $common->get('user_photo_folders', 'user_id = :user_id', ['user_id' => $user_id],[],'created_at','DESC'); 
    foreach($userFolders as $k=>$folder) {
        $folder_photo = $common->get('user_folder_photos', 'user_id = :user_id AND folder_id=:folder_id', ['user_id' => $user_id,'folder_id'=>$folder['id']],[],'created_at','DESC'); 
        $userFolders[$k]['count']=count($folder_photo);
        if(!empty($folder_photo)){
            $userFolders[$k]['icon']=$folder_photo[0]['thumb'];
        }else{
            $userFolders[$k]['icon']=SITE_URL.'/assets/images/icons8-folder-48.svg';
        }
    }
   
    return response(['success' => true, 'data' => ['folders' => $userFolders]]);

}
if (isset($_POST['LetterApplicationCheck']) && ($_POST['LetterApplicationCheck'] == 'LetterApplicationCheck')) {
    $LetterApplication = $format->validation($_POST['LetterApplication']);
    if (isset($LetterApplication)) {
        echo 'Insert';
    } else {
        echo 'Field Fill';
    }
}

if (isset($_POST['EmailSendCheck']) && ($_POST['EmailSendCheck'] == 'EmailSendCheck')) {
    $LetterApplication = $format->validation($_POST['LetterApplication']);
    $Title = $format->validation($_POST['Title']);
    $Date = $format->validation($_POST['Date']);
    $Date = date('Y-m-d', strtotime($Date));
    $UserId = $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $AdminId = 0;
    $email = $format->validation($_POST['email']);
    $emailto = $format->validation($_POST['emailto']);
    $letterId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $resArr = ['success' => false, 'message' => 'error', 'data' => []];
    $resArr['new'] = false;
    $resArr['letterId'] = $letterId;
    if (isset($email)) {
        if (isset($LetterApplication)) {
            $user = $common->first('users', "id = :id", ['id' => $user_id]);
            if ($user) {
                if (empty($letterId)) {
                    $AdminId = 0;
                    $LetterApplication_insert = $common->insert("letterapplication", [
                        'email' => $email,
                        'emailto' => $emailto,
                        'date' => $Date,
                        'title' => $Title,
                        'letterapplicationtext' => $LetterApplication,
                        'UserId' => $UserId,
                        'AdminId' => $AdminId,
                    ]);
                    $letterId = $common->insertId();
                    $resArr['new'] = true;
                } else {
                    $common->update(
                        'letterapplication',
                        ['letterapplicationtext' => $LetterApplication, 'email' => $email, 'emailto' => $emailto, 'date' => $Date, 'title' => $Title],
                        'id = :id',
                        ['id' => $letterId],
                        false
                    );
                }

                $letterapp = $common->first("letterapplication", "id = :id", ['id' => $letterId]);
                $goalBodyHtml = '<div style="width:600px; background-color:#FFF; margin:0 auto;">';
                $goalBodyHtml .= '<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
                $goalBodyHtml .= '<div style="padding:20px; background-color:#FFF; ">
                      <h2 style="text-transform: capitalize;">' . $Title . '</h2>
                      <p>Fecha: ' . date('d-m-Y', strtotime($Date)) . '</p> 
                      <p>De: ' . $user['full_name'] . '</p>        
                      <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><div style="">';
                $goalBodyHtml .= html_entity_decode($letterapp['letterapplicationtext']);
                $goalBodyHtml .= '</div></div>      
                  </div>';
                $goalBodyHtml .= '<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0; text-align:center;">Mejorcadadia.com</p><p style="clear:both; margin:0; padding:0; text-align:center;">All rights reserved 2022</p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

                $AdminId = 0;
                $Date = date('Y-m-d');
                $sent = sendEmail($user_id, 'Cartas Eternidad - ' . $Title, $emailto, $goalBodyHtml);
                if ($sent === true) {
                    $resArr['success'] = true;
                    $resArr['letterId'] = $letterId;
                }
            }


        } else {
            $resArr['message'] = 'Something is wrong!';
        }
    } else {
        $resArr['message'] = 'Field Fill';
    }
    echo json_encode($resArr);
}

if (isset($_POST['EmailSendCheckOnlySend']) && ($_POST['EmailSendCheckOnlySend'] == 'EmailSendCheckOnlySend')) {
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
            if (!empty($id)) {
                $common->update(
                    'letterapplication',
                    ['letterapplicationtext' => $LetterApplication, 'email' => $email, 'emailto' => $emailto, 'date' => $Date, 'title' => $Title],
                    'id = :id',
                    ['id' => $id],
                    false
                );
                echo 'Update';
            } else {
                $LetterApplication_insert = $common->insert("letterapplication", [
                    'email' => $email,
                    'emailto' => $emailto,
                    'date' => $Date,
                    'title' => $Title,
                    'letterapplicationtext' => $LetterApplication,
                    'UserId' => $UserId,
                    'AdminId' => $AdminId
                ]);
                if ($LetterApplication_insert) {
                    echo 'Insert';
                } else {
                    echo 'Something is wrong!';
                }
            }

        } else {
            echo 'Something is wrong!';
        }
    } else {
        echo 'Field Fill';
    }
}

if (isset($_POST['EmailIdCheck']) && ($_POST['EmailIdCheck'] == 'EmailIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("letterapplication", ["letterapplicationtext" => $LetterApplication], "id = :id", ['id' => $id], false);
        echo 'Update';
    } else {
        echo 'Something is wrong!';
    }
}

if (isset($_POST['SaveIdCheck']) && ($_POST['SaveIdCheck'] == 'SaveIdCheck')) {
    if (isset($_POST['id'])) {
        $LetterApplication = htmlentities($_POST['LetterApplication']);
        $id = $_POST['id'];
        $app_update = $common->update("letterapplication", ["letterapplicationtext" => $LetterApplication], "id = :id", ['id' => $id], false);
        echo 'Update';
    } else {
        echo 'Something is wrong!';
    }
}

if (isset($_POST['EmailDeleteCheck']) && ($_POST['EmailDeleteCheck'] == 'EmailDeleteCheck')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("letterapplication", "id = :id", ['id' => $id]);
        echo 'Delete';
    } else {
        echo 'Something is wrong!';
    }
}

if (isset($_POST['MobileSendCheck']) && ($_POST['MobileSendCheck'] == 'MobileSendCheck')) {

}
function pullPreviousLifeGoals($userId, $currentDate)
{
    global $common;
    $result = $common->first('daily_life_goals', 'user_id = :user_id AND created_at = :created_at', ['user_id' => $userId, 'created_at' => $currentDate]);
    if ($result === false) {
        $row = $common->first('daily_life_goals', "user_id = :user_id AND created_at <= :created_at ORDER BY created_at DESC", ['user_id' => $userId, 'created_at' => $currentDate]);
        if ($row) {
            $result = $common->get('daily_life_goals', 'user_id = :user_id AND created_at = :created_at', ['user_id' => $userId, 'created_at' => $row['created_at']]);
            if ($result) {
                foreach ($result as $row) {
                    $goalText = $row['goal'];
                    $common->insert('daily_life_goals', [
                        'user_id' => $userId,
                        'goal' => $goalText,
                        'created_at' => $currentDate
                    ]);
                }
            }
        }
    }
}

//function pullPreviousGoals($user_id,$type,$start_date,$end_date){
//    global $common;
//    $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$start_date."' AND end_date<='".$end_date."'");
//
//    if($result==false || $result->num_rows<1){
//        $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND end_date<='".$start_date."' ORDER BY end_date DESC LIMIT 0,1");
//        if($result){
//          $row = $result -> fetch_assoc();
//          $previous_start_date=$row['start_date'];
//          $previous_end_date=$row['end_date'];
//          $result=$common->db->select("SELECT * FROM supergoals WHERE user_id='".$user_id."' AND type='".$type."' AND start_date>='".$previous_start_date."' AND end_date<='".$previous_end_date."'");
//          if($result){
//            while ($row = $result -> fetch_assoc()) {
//                $goalText=$row['goal'];
//                //$common->insert('supergoals(user_id,type,goal,start_date,end_date)', '("'.$user_id.'","'.$type.'","'.$goalText.'","'.$start_date.'","'.$end_date.'")');
//            }
//          }
//        }
//    }
//}

if (isset($_POST['UpdateSuperGoal']) && ($_POST['UpdateSuperGoal'] == 'UpdateSuperGoal')) {

    $type = $_POST['type'];
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $common->update('supergoals', ['goal' => $goalText, 'achieved' => $achieved], 'id = :goal_id', ['goal_id' => $goalId], false);
        echo 'Updated';
    } else {
        if (!empty($goalText)) {

            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $row = $common->first(
                "supergoals",
                'goal = :goal AND user_id = :user_id AND type = :type AND DATE(start_date) >= :start_date AND DATE(end_date) <= :end_date',
                ['goal' => $goalText, 'user_id' => $user_id, 'type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]
            );

            if ($row) {
                $common->update('supergoals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']], false);

            } else {
                $common->insert('supergoals', [
                    'user_id' => $user_id,
                    'type' => $type,
                    'goal' => $goalText,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]);
            }
        }
        echo 'Update';
    }


}

if (isset($_POST['UpdateDailyLifeGoalChecked']) && ($_POST['UpdateDailyLifeGoalChecked'] == 'UpdateDailyLifeGoalChecked')) {

    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : $today;
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $result = $common->count(
            "dailylifegoals_marked",
            'goal_id = :goal_id AND user_id = :user_id AND created_at = :created_at',
            ['goal_id' => $goalId, 'user_id' => $user_id, 'created_at' => $currentDate]
        );
        if ($result > 0) {
            $common->update(
                'dailylifegoals_marked',
                ['checked' => $achieved],
                'goal_id = :goal_id AND user_id = :user_id AND created_at = :created_at',
                ['goal_id' => $goalId, 'user_id' => $user_id, 'created_at' => $currentDate]
            );
            echo 'Updated';
        } else {
            $common->insert('dailylifegoals_marked', ['user_id' => $user_id, 'goal_id' => $goalId, 'checked' => $achieved, 'created_at' => $currentDate]);
        }

    }
    echo 'Update';


}

if (isset($_POST['UpdateDailyGoal']) && ($_POST['UpdateDailyGoal'] == 'UpdateDailyLifeGoal')) {

    $user_id = Session::get('user_id');
    
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $edit = isset($_POST['edit']) ? $_POST['edit'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if ($edit == 1 && !empty($goalId) && !empty($goalText)) {
        $common->update('dailylifegoals', ['goal' => $goalText], 'id = :id', ['id' => $goalId]);
        echo 'Updated';
    }
    echo 'Update';


}

if (isset($_POST['UpdateDailyBiggestVictories']) && ($_POST['UpdateDailyBiggestVictories'] == 'UpdateDailyBiggestVictories')) {


    $user_id = Session::get('user_id');
    
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $currentDateTime = isset($_POST['currentDateTime']) ? $_POST['currentDateTime'] : date('Y-m-d H:i:s');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $common->update('daily_biggest_victories', ['goal' => $goalText, 'achieved' => $achieved], 'id = :id', ['id' => $goalId]);
        echo 'Updated';
    } else {
        if (!empty($goalText)) {

            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $row = $common->first('daily_biggest_victories', 'goal = :goal_text AND user_id = :user_id AND created_at = :created_at', [
                'goal_text' => $goalText,
                'user_id' => $user_id,
                'created_at' => $currentDate
            ]);

            if ($row) {
                $common->update('daily_biggest_victories', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);
            } else {
                $common->insert('daily_biggest_victories', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
            }
        }
        echo 'Update';
    }


}

if (isset($_POST['UpdateDailySuperDias']) && ($_POST['UpdateDailySuperDias'] == 'UpdateDailySuperDias')) {


    $user_id = Session::get('user_id');
    
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $currentDateTime = isset($_POST['currentDateTime']) ? $_POST['currentDateTime'] : date('Y-m-d H:i:s');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $common->update('daily_superdias', ['goal' => $goalText, 'achieved' => $achieved], 'id = :id', ['id' => $goalId]);
        echo 'Updated';
    } else {
        if (!empty($goalText)) {

            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $row = $common->first('daily_superdias', 'goal = :goal_text AND user_id = :user_id AND created_at = :created_at', [
                'goal_text' => $goalText,
                'user_id' => $user_id,
                'created_at' => $currentDate
            ]);

            if ($row) {
                $common->update('daily_superdias', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);
            } else {
                $common->insert('daily_superdias', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
            }
        }
        echo 'Update';
    }


}
if (isset($_POST['UpdateDailyGoal']) && ($_POST['UpdateDailyGoal'] == 'UpdateDailyImportantGoal')) {


    $user_id = Session::get('user_id');
    
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $common->update('daily_important_goals', ['goal' => $goalText, 'achieved' => $achieved], 'id = :id', ['id' => $goalId]);
        echo 'Updated';
    } else {
        if (!empty($goalText)) {

            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $row = $common->first('daily_important_goals', 'goal = :goal_text AND user_id = :user_id AND created_at = :created_at', [
                'goal_text' => $goalText,
                'user_id' => $user_id,
                'created_at' => $currentDate
            ]);

            if ($row) {
                $common->update('daily_important_goals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);
            } else {
                $common->insert('daily_important_goals', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
            }
        }
        echo 'Update';
    }


}
if (isset($_POST['UpdateDailyGoal']) && ($_POST['UpdateDailyGoal'] == 'UpdateDailyTopGoal')) {


    $user_id = Session::get('user_id');
    
    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;

    if (!empty($goalId)) {
        $common->update('daily_top_goals', ['goal' => $goalText, 'achieved' => $achieved], 'id = :id', ['id' => $goalId]);
        echo 'Updated';
    } else {
        if (!empty($goalText)) {

            //pullPreviousGoals($user_id,$type,$startDate,$endDate);    
            $row = $common->first('daily_top_goals', 'goal = :goal_text AND user_id = :user_id AND created_at = :created_at', [
                'goal_text' => $goalText,
                'user_id' => $user_id,
                'created_at' => $currentDate
            ]);

            if ($row) {
                $common->update('daily_top_goals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);
            } else {
                $common->insert('daily_top_goals', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
            }
        }
        echo 'Update';
    }


}


if (isset($_POST['UpdateDailyGoals']) && ($_POST['UpdateDailyGoals'] == 'UpdateDailyGoals')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $lifeGoalsData = $_POST['lifeGoalsData'] ?? [];
    $topGoalsData = $_POST['topGoalsData'] ?? [];
    $dailyEvolution = empty($_POST['dailyEvolution']) ? '' : $_POST['dailyEvolution'];
    $dailyImprovements = empty($_POST['dailyImprovements']) ? '' : $_POST['dailyImprovements'];
    $currentDate = $_POST['currentDate'] ?? date('Y-m-d');
    $dailyVictory = trim($_POST['dailyVictory'] ?? '');
    $dailyVictoryTags = $_POST['dailyVictoryTags'] ?? [];
    $toRemember = trim($_POST['toRemember'] ?? '');
    $toRememberTags = $_POST['toRememberTags'] ?? [];

    if ((!empty($dailyVictory) && count($dailyVictoryTags) === 0) || (!empty($toRemember) && count($toRememberTags) === 0)) {
        echo json_encode(["success" => false, "message" => 'You need to have one tag at least']);
        return;
    }

    $row = $common->first("dailygaols", "user_id = :user_id AND created_at = :created_at", ['user_id' => $user_id, 'created_at' => $currentDate]);
    $dailyVictoryData = $common->first("daily_victories", "user_id = :user_id AND date = :date", ['user_id' => $user_id, 'date' => $currentDate]);
    $toRememberData = $common->first("to_remember", "user_id = :user_id AND date = :date", ['user_id' => $user_id, 'date' => $currentDate]);

    try {
        if ($row) {
            $common->update('dailygaols', ['improvements' => $dailyImprovements, 'evolution' => $dailyEvolution], 'id = :id', ['id' => $row['id']]);
        } else {
            $common->insert('dailygaols', ['user_id' => $user_id, 'improvements' => $dailyImprovements, 'evolution' => $dailyEvolution, 'created_at' => $currentDate]);
        }

        if (!empty($lifeGoalsData)) {
            pullPreviousLifeGoals($user_id, $currentDate);
            foreach ($lifeGoalsData as $key => $item) {
                $id = (int)$item['id'];
                $goalText = $item['text'];
                $achieved = (int)$item['checked'];
                $row = $common->first("daily_life_goals", 'id = :id AND user_id = :user_id AND created_at = :created_at', ['id' => $id, 'user_id' => $user_id, 'created_at' => $currentDate]);
                if ($row) {
                    $common->update('daily_life_goals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);

                } else {
                    if (!empty($goalText)) {
                        $common->insert('daily_life_goals', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
                    }
                }
            }
        }
        if (!empty($topGoalsData)) {
            //pullPreviousGoals($user_id,$type,$startDate,$endDate);
            foreach ($topGoalsData as $key => $item) {
                $id = (int)$item['id'];
                $goalText = $item['text'];
                $achieved = (int)$item['checked'];
                $row = $common->first("daily_top_goals", 'id = :id AND user_id = :user_id AND created_at = :created_at', ['id' => $id, 'user_id' => $user_id, 'created_at' => $currentDate]);
                if ($row) {
                    $common->update('daily_top_goals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']]);

                } else {
                    if (!empty($goalText)) {
                        $common->insert('daily_top_goals', ['user_id' => $user_id, 'goal' => $goalText, 'created_at' => $currentDate]);
                    }
                }
            }
        }

        if(!empty($dailyVictory)) {
            foreach ($dailyVictoryTags as $newTag) {
                $tag = trim($newTag);
                if (str_contains($tag, " ")) {
                    echo json_encode(['success' => false, 'message' => 'Tags can not have spaces in them']);
                    return;
                }
            }
            if ($dailyVictoryData) {
                updateVictoryWithTags($dailyVictoryData['id'], $dailyVictory, $dailyVictoryTags, $user_id);
            } else {
                addVictoryWithTags($dailyVictory, $dailyVictoryTags, $user_id, $currentDate);
            }
        }

        if (!empty($toRemember)) {
            foreach ($toRememberTags as $newTag) {
                $tag = trim($newTag);
                if (str_contains($tag, " ")) {
                    echo json_encode(['success' => false, 'message' => 'Tags can not have spaces in them']);
                    return;
                }
            }
            if ($toRememberData) {
                updateToRememberWithTags($toRememberData['id'], $toRemember, $toRememberTags, $user_id);
            } else {
                addToRememberWithTags($toRemember, $toRememberTags, $user_id, $currentDate);
            }
        }
    } catch (Exception $exp) {
        echo json_encode(['success' => false, 'message' => $exp]);
    }


    echo json_encode(['success' => true]);


}

if (isset($_POST['UpdateSuperGoals']) && ($_POST['UpdateSuperGoals'] == 'UpdateSuperGoals')) {


    $type = $_POST['type'];
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goalsData = isset($_POST['goalsData']) ? $_POST['goalsData'] : [];
    $description = empty($_POST['description']) ? '' : $_POST['description'];
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';

    if (!empty($goalsData)) {
        //pullPreviousGoals($user_id,$type,$startDate,$endDate);
        foreach ($goalsData as $key => $item) {
            $id = (int)$item['id'];
            $goalText = $item['text'];
            $achieved = (int)$item['checked'];

            $row = $common->first("supergoals", "id = :id AND user_id = :user_id AND type = :type", ['id' => $id, 'user_id' => $user_id, 'type' => $type]);

            if ($row) {
                $common->update('supergoals', ['achieved' => $achieved], 'id = :id', ['id' => $row['id']], false);

            } else {
                if (!empty($goalText)) {
                    $common->insert('supergoals', ['user_id' => $user_id, 'type' => $type, 'goal' => $goalText, 'start_date' => $startDate, 'end_date' => $endDate]);
                }

            }
        }
    }

    $row = $common->first(
        "supergoals_evaluation",
        'user_id = :user_id AND type = :type AND start_date >= :start_date AND end_date <= :end_date',
        ['user_id' => $user_id, 'type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]
    );
    if ($row) {
        $common->update('supergoals_evaluation', ['description' => $description], 'id = :id', ['id' => $row['id']], false);
    } else {

        $common->insert('supergoals_evaluation', [
            'user_id' => $user_id,
            'type' => $type,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }


    echo 'Update';


}

if (isset($_POST['DeleteGoals']) && ($_POST['DeleteGoals'] == 'DeleteGoals')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goalIds = isset($_POST['goalIds']) ? $_POST['goalIds'] : [];
    $type = $_POST['type'];
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d h:i:s');
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d h:i:s');
    $table_name = 'supergoals';
    if (!empty($goalIds)) {
        $placeholders = array_fill(0, count($goalIds), '?');
        $common->delete("supergoals", "id IN (".implode(',', $placeholders).")", $goalIds);
    }
    echo 'Deleted';
}

if (isset($_POST['DeleteDailySuperDias']) && ($_POST['DeleteDailySuperDias'] == 'DeleteDailySuperDias')) {
    $user_id = Session::get('user_id');
    
    $goalIds = isset($_POST['goalIds']) ? $_POST['goalIds'] : [];
  
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d h:i:s');
    if (!empty($goalIds)) {
        $placeholders = array_fill(0, count($goalIds), '?');
        $common->delete("daily_superdias", "id IN(" . implode(",", $placeholders) . ")", $goalIds);
        
    }
    echo 'Deleted';
}
if (isset($_POST['DeleteDailyBiggestVictories']) && ($_POST['DeleteDailyBiggestVictories'] == 'DeleteDailyBiggestVictories')) {
    $user_id = Session::get('user_id');
    
    $goalIds = isset($_POST['goalIds']) ? $_POST['goalIds'] : [];
  
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d h:i:s');
    if (!empty($goalIds)) {
        $placeholders = array_fill(0, count($goalIds), '?');
        $common->delete("daily_biggest_victories", "id IN(" . implode(",", $placeholders) . ")", $goalIds);
        
    }
    echo 'Deleted';
}

if (isset($_POST['DeleteDailyGoals']) && ($_POST['DeleteDailyGoals'] == 'DeleteDailyGoals')) {
    $user_id = Session::get('user_id');
    
    $goalIds = isset($_POST['goalIds']) ? $_POST['goalIds'] : [];
    $type = $_POST['type'];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d h:i:s');
    if (!empty($goalIds)) {
        if ($type == 'top') {
            $placeholders = array_fill(0, count($goalIds), '?');
            $common->delete("daily_top_goals", "id IN(" . implode(",", $placeholders) . ")", $goalIds);
        } elseif ($type == 'life') {
            $placeholders = array_fill(0, count($goalIds), '?');
            $common->delete("dailylifegoals", "id IN(" . implode(",", $placeholders) . ")", $goalIds);
            $common->delete("dailylifegoals_marked", "goal_id IN(" . implode(",", $placeholders) . ")", $goalIds);
        }elseif ($type == 'important') {
            $placeholders = array_fill(0, count($goalIds), '?');
            $common->delete("daily_important_goals", "id IN(" . implode(",", $placeholders) . ")", $goalIds);
        }
    }
    echo 'Deleted';
}

if (isset($_POST['action']) && ($_POST['action'] == 'DeleteCapsule')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $id=isset($_POST['id'])? $_POST['id']:0; 
    $resArr=[];    
    if($id>0){
         $common->delete("user_capsules", "id = :id", ['id' => $id]);
    }
    echo json_encode($resArr);
}
if (isset($_POST['action']) && ($_POST['action'] == 'DeleteNotes')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $id=isset($_POST['id'])? $_POST['id']:0; 
    $resArr=[];    
    if($id>0){
         $common->delete("user_notes", "id = :id", ['id' => $id]);
    }
    echo json_encode($resArr);
}



if (isset($_POST['action']) && ($_POST['action'] == 'moveNotes')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $folder_id=isset($_POST['folder_id'])? $_POST['folder_id']:0; 
    $id=isset($_POST['id'])? $_POST['id']:0; 

    $resArr = ['success' => false,  'date'=>date('d-m-Y'),'id'=>$id,'folder_id'=>$folder_id, 'message' => ""];
    if(!empty($id)){
        $common->update(
            'user_notes',
            ['folder_id'=>$folder_id],
            'id = :id AND user_id = :user_id',
            ['id' => $id, 'user_id' => $user_id],
            modifiedColumnName: 'updated_at'
        );
        $resArr['message'] = "updated successfully";
        $resArr['success'] = true;
    }   
    
    echo json_encode($resArr);


}

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'getNotes')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $id=isset($_REQUEST['id'])? $_REQUEST['id']:0;   
    $row = $common->first(
        table: "user_notes",
        cond: 'user_id = :user_id  AND id = :id',
        params: ['user_id' => $user_id, 'id' => $id],
        orderBy: 'id',
        order: 'DESC'
    );
    $resArr['note']=$row;
    if(!empty($row)){
        $resArr['success']=true;
    }else{
        $resArr['success']=false;
    }
    echo json_encode($resArr);
}

if (isset($_POST['action']) && ($_POST['action'] == 'createCapsule')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $content = empty($_POST['content']) ? '' : $_POST['content'];    
    $id=isset($_POST['id'])? $_POST['id']:0;     
    setlocale(LC_ALL, "es_ES");
    $string = date('Y-m-d H:i:s');
    $dateObj = DateTime::createFromFormat("Y-m-d H:i:s", $string); 
    $cDate=utf8_encode(strftime("%A, %d %B, %Y %H:%M", $dateObj->getTimestamp()));
    $resArr = ['success' => false, 'new'=>true,  'date'=>$cDate,'id'=>$id,'content' => $content, 'message' => ""];
    if(!empty($content)){
        if(empty($id)){
            $common->insert('user_capsules', [
                'user_id' => $user_id,
                'content' => $content
            ]);
            $id = $common->insertId();
            $resArr['success'] = true;
            $resArr['message'] = "created successfully";
            $resArr['id'] = $id;
        }else{
           
            $common->update(
                'user_capsules',
                ['content' => $content],
                'id = :id AND user_id = :user_id',
                ['id' => $id, 'user_id' => $user_id],
                modifiedColumnName: 'updated_at'
            );
            $resArr['new'] = false;
            $resArr['message'] = "updated successfully";
            $resArr['success'] = true;
    
        } 
    }else{

    }
    
    echo json_encode($resArr);


}
if (isset($_POST['action']) && ($_POST['action'] == 'createNotes')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $notes = empty($_POST['notes']) ? '' : $_POST['notes'];
    //$title = empty($_POST['title']) ? '' : $_POST['title'];
    $title=substr(strip_tags($notes),0,30);
    $folder_id=isset($_POST['folder_id'])? $_POST['folder_id']:0; 
    $id=isset($_POST['id'])? $_POST['id']:0;     
    setlocale(LC_ALL, "es_ES");
    $string = date('Y-m-d H:i:s');
    $dateObj = DateTime::createFromFormat("Y-m-d H:i:s", $string); 
    $cDate=utf8_encode(strftime("%A, %d %B, %Y %H:%M", $dateObj->getTimestamp()));
    $resArr = ['success' => false, 'new'=>true, 'title'=>$title, 'date'=>$cDate,'id'=>$id,'folder_id'=>$folder_id,'notes' => $notes, 'message' => ""];
    
    if(empty($id)){
        $common->insert('user_notes', [
            'user_id' => $user_id,
            'folder_id' => $folder_id,
            'notes' => $notes
        ]);
        $id = $common->insertId();
        $resArr['success'] = true;
        $resArr['message'] = "created successfully";
        $resArr['id'] = $id;
    }else{
       
        $common->update(
            'user_notes',
            ['notes' => $notes,'folder_id'=>$folder_id],
            'id = :id AND user_id = :user_id',
            ['id' => $id, 'user_id' => $user_id],
            modifiedColumnName: 'updated_at'
        );
        $resArr['new'] = false;
        $resArr['message'] = "updated successfully";
        $resArr['success'] = true;

    }    
    
    echo json_encode($resArr);


}

if (isset($_POST['action']) && ($_POST['action'] == 'CreatePhotoFolder')) {
    $user_id = Session::get('user_id');    
    $folder_name = $format->validation($_POST['folder_name']);
    $folder_id=isset($_POST['folder_id'])? $_POST['folder_id']:0;    
    $resArr = ['success' => false, 'new'=>true, 'folder_id'=>$folder_id,'folder_name' => $folder_name, 'message' => ""];
    if(empty($folder_id)){
        $common->insert('user_photo_folders', [
            'user_id' => $user_id,
            'name' => $folder_name
        ]);
        $folder_id = $common->insertId();
        $resArr['success'] = true;
        $resArr['message'] = "created successfully";
        $resArr['folder'] = ['id'=>$folder_id,'name'=>$folder_name,'icon'=>SITE_URL.'/assets/images/icons8-folder-48.svg','count'=>0];        
        $resArr['new'] = true;
    }else{
        $common->update(
            table: "user_photo_folders",
            data: ['name' => $folder_name],
            cond: 'WHERE user_id = :user_id AND id = :id',
            params: ['user_id' => $user_id, 'id' => $folder_id]
        );
        $resArr['new'] = false;
        $resArr['message'] = "updated successfully";
        $resArr['success'] = true;
        $folder_photo = $common->get('user_folder_photos', 'user_id = :user_id AND folder_id=:folder_id', ['user_id' => $user_id,'folder_id'=>$folder_id],[],'created_at','DESC'); 
  
        $resArr['folder'] = ['id'=>$folder_id,'name'=>$folder_name,'icon'=>SITE_URL.'/assets/images/icons8-folder-48.svg','count'=>count($folder_photo)];  

    }    
    
    echo json_encode($resArr);


}
if (isset($_POST['action']) && ($_POST['action'] == 'createFolder')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $folder_name = $format->validation($_POST['folder_name']);
    $folder_id=isset($_POST['folder_id'])? $_POST['folder_id']:0;    
    $resArr = ['success' => false, 'new'=>true, 'folder_id'=>$folder_id,'folder_name' => $folder_name, 'message' => ""];
    if(empty($folder_id)){
        $common->insert('user_folders', [
            'user_id' => $user_id,
            'name' => $folder_name
        ]);
        $folder_id = $common->insertId();
        $resArr['success'] = true;
        $resArr['message'] = "created successfully";
        $resArr['folder_id'] = $folder_id;
    }else{
        $common->update(
            table: "user_folders",
            data: ['name' => $folder_name],
            cond: 'WHERE user_id = :user_id AND id = :id',
            params: ['user_id' => $user_id, 'id' => $folder_id]
        );
        $resArr['new'] = false;
        $resArr['message'] = "updated successfully";
        $resArr['success'] = true;

    }    
    
    echo json_encode($resArr);


}
if (isset($_POST['action']) && ($_POST['action'] == 'SaveVictory7Box')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $box = $format->validation($_POST['box']);
    $body = $format->validation($_POST['body']);
    $selectedDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $resArr = ['success' => false, 'goals' => [], 'message' => ""];
    
    /*if ($selectedDate < $today) {
        $resArr['message'] = 'Not allowed in past.';
    } else */
    if (!empty($box)) {
        $result = $common->count("victory7boxes", 'user_id = :user_id AND box = :box AND created_at = :created_at', ['user_id' => $user_id, 'box' => $box, 'created_at' => $selectedDate]);
        
        if ($result > 0) {
            $updated=$common->update(
                table: "victory7boxes",
                data: ['body' => $body],
                cond: 'user_id = :user_id AND box = :box AND created_at = :created_at',
                params: ['user_id' => $user_id, 'box' => $box, 'created_at' => $selectedDate],
                modifiedColumnName: 'modified_at'
            );
          
            $resArr['success'] = true;
        } else {
            $common->insert('victory7boxes', [
                'user_id' => $user_id,
                'box' => $box,
                'body' => $body,
                'created_at' => $selectedDate
            ]);
            $resArr['success'] = true;
        }
    }
    echo json_encode($resArr);


}
if (isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitmentAnswer')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : $today;
    $table_name = 'daily_commitments_answers';
    $goalId = $format->validation($_POST['goalId']);
    $answer = (int)$format->validation($_POST['answer']);

    $resArr = ['success' => false, 'goals' => [], 'message' => ""];
    if ($selectedDate < $today) {
        $resArr['message'] = 'Not allowed in past.';
    } else {
        $result = $common->count(
            "daily_commitments_answers",
            'goal_id = :goal_id AND user_id = :user_id AND created_at = :created_at',
            ['goal_id' => $goalId, 'user_id' => $user_id, 'created_at' => $selectedDate]
        );

        if ($result > 0) {
            $common->update(
                'daily_commitments_answers',
                ['answer' => $answer],
                'goal_id = :goal_id AND user_id = :user_id AND created_at = :created_at',
                ['goal_id' => $goalId, 'user_id' => $user_id, 'created_at' => $selectedDate]
            );
            $resArr['success'] = true;
        } else {
            $common->insert('daily_commitments_answers', [
                'user_id' => $user_id,
                'goal_id' => $goalId,
                'answer' => $answer,
                'created_at' => $selectedDate
            ]);
            $resArr['success'] = true;
        }

    }
    echo json_encode($resArr);
}
if (isset($_POST['action']) && ($_POST['action'] == 'SaveNewDailyCommitments')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $seletedDate = isset($_POST['seletedDate']) ? $_POST['seletedDate'] : $today;
    $table_name = 'daily_commitments_goals';
    $addedGoals = [];
    $resArr = ['success' => false, 'goals' => $addedGoals, 'message' => ""];
    if ($seletedDate < $today) {
        $resArr['message'] = 'Not allowed in past.';
    } else {
        foreach ($goals as $key => $goal) {
            if (!empty($goal)) {
                $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $today]);
                $id = $common->insertId();
                $addedGoals[$id] = $goal;
            }
        }
        $resArr['goals'] = $addedGoals;
        $resArr['message'] = 'Added';
        $resArr['success'] = true;
    }
    echo json_encode($resArr);
}
if (isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitment')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }

    $achieved = isset($_POST['achieved']) ? $_POST['achieved'] : 0;
    $goalText = empty($_POST['goalText']) ? '' : $_POST['goalText'];
    $selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : date('Y-m-d');
    $goalId = isset($_POST['goalId']) ? (int)$_POST['goalId'] : 0;
    $edit = isset($_POST['edit']) ? (int)$_POST['edit'] : 0;
    $delete = isset($_POST['delete']) ? (int)$_POST['delete'] : 0;
    $goalIds = isset($_POST['goalIds']) ? $_POST['goalIds'] : [];
    if (!empty($goalIds) && $delete == 1) {
        foreach ($goalIds as $key => $gid) {
            if (!empty($gid)) {
                $common->update(
                    table: 'daily_commitments_goals',
                    data: ['deleted_at' => $today],
                    cond: 'id = :id AND user_id = :user_id',
                    params: ['id' => $gid, 'user_id' => $user_id],
                    modifiedColumnName: 'modified_at'
                );
                $resArr['success'] = true;
            }
        }
    } else if (!empty($goalText) && !empty($goalId)) {
        $common->update(
            'daily_commitments_goals',
            ['goal' => $goalText],
            'id = :id AND user_id = :user_id',
            ['id' => $goalId, 'user_id' => $user_id],
            true,
            'modified_at'
        );
        $resArr['success'] = true;

    }
    echo 'Updated';

}
if (isset($_POST['action']) && ($_POST['action'] == 'UpdateDailyCommitments')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $dailyEvolution = empty($_POST['dailyEvolution']) ? '' : $_POST['dailyEvolution'];
    $selectedDate = isset($_POST['selectedDate']) ? $_POST['selectedDate'] : date('Y-m-d');

    $result = $common->count("daily_commitments_description", 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $selectedDate]);
    if ($result && $result->num_rows > 0) {
        $common->update(
            'daily_commitments_description',
            ['description' => $dailyEvolution],
            'user_id = :user_id AND created_at = :created_at',
            ['user_id' => $user_id, 'created_at' => $selectedDate]
        );
        $resArr['success'] = true;
    } else {
        $common->insert('daily_commitments_description', ['user_id' => $user_id, 'description' => $dailyEvolution, 'created_at' => $selectedDate]);
    }


    echo 'Updated';

}
if (isset($_POST['SaveNewDailyBiggestVictories']) && ($_POST['SaveNewDailyBiggestVictories'] == 'SaveNewDailyBiggestVictories')) {
    $user_id = Session::get('user_id');    
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $currentDateTime = isset($_POST['currentDateTime']) ? $_POST['currentDateTime'] : date('Y-m-d H:i:s');
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'daily_biggest_victories';
    $addedGoals = [];
    
    foreach ($goals as $key => $goal) {
        if (!empty($goal)) {
            $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $currentDateTime]);
            $id = $common->insertId();
            $addedGoals[$id] = $goal;
        }
    }
    setlocale(LC_ALL, "es_ES");
    $string = date('d/m/Y', strtotime($currentDateTime));
    $dateObj = DateTime::createFromFormat("d/m/Y", $string);
    echo json_encode(['success' => true, 'goals' => $addedGoals,
    'date'=>utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()))]
    );
}
if (isset($_POST['SaveNewDailySuperDias']) && ($_POST['SaveNewDailySuperDias'] == 'SaveNewDailySuperDias')) {
    $user_id = Session::get('user_id');    
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $currentDateTime = isset($_POST['currentDateTime']) ? $_POST['currentDateTime'] : date('Y-m-d H:i:s');
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'daily_superdias';
    $addedGoals = [];
    
    foreach ($goals as $key => $goal) {
        if (!empty($goal)) {
            $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $currentDateTime]);
            $id = $common->insertId();
            $addedGoals[$id] = $goal;
        }
    }
    setlocale(LC_ALL, "es_ES");
    $string = date('d/m/Y', strtotime($currentDateTime));
    $dateObj = DateTime::createFromFormat("d/m/Y", $string);
    echo json_encode(['success' => true, 'goals' => $addedGoals,
    'date'=>utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()))]
    );
}
if (isset($_POST['SaveNewDailyImportantGoals']) && ($_POST['SaveNewDailyImportantGoals'] == 'SaveNewDailyImportantGoals')) {
    $user_id = Session::get('user_id');
    
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'daily_important_goals';
    $addedGoals = [];
    //pullPreviousGoals($user_id,$type,$startDate,$endDate);
    foreach ($goals as $key => $goal) {

        if (!empty($goal)) {
            $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $currentDate]);
            $id = $common->insertId();
            $addedGoals[$id] = $goal;
        }
    }
    echo json_encode(['success' => true, 'goals' => $addedGoals]);
}
if (isset($_POST['SaveNewDailyTopGoals']) && ($_POST['SaveNewDailyTopGoals'] == 'SaveNewDailyTopGoals')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'daily_top_goals';
    $addedGoals = [];
    //pullPreviousGoals($user_id,$type,$startDate,$endDate);
    foreach ($goals as $key => $goal) {

        if (!empty($goal)) {
            $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $currentDate]);
            $id = $common->insertId();
            $addedGoals[$id] = $goal;
        }
    }
    echo json_encode(['success' => true, 'goals' => $addedGoals]);
}


if (isset($_POST['SaveNewDailyLifeGoals']) && ($_POST['SaveNewDailyLifeGoals'] == 'SaveNewDailyLifeGoals')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'dailylifegoals';
    $addedGoals = [];
    if ($currentDate >= $today) {
        foreach ($goals as $key => $goal) {
            if (!empty($goal)) {
//                $goal= $common->db->link->real_escape_string($goal);
                $common->insert($table_name, ['user_id' => $user_id, 'goal' => $goal, 'created_at' => $today]);
                $id = $common->insertId();
                $addedGoals[$id] = $goal;
            }
        }
    }
    echo json_encode(['success' => true, 'goals' => $addedGoals]);
}

if (isset($_POST['saveNewGoals']) && ($_POST['saveNewGoals'] == 'saveNewGoals')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goals = isset($_POST['goals']) ? $_POST['goals'] : [];
    $type = $_POST['type'];
    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');
    $table_name = 'supergoals';
    $addedGoals = [];
    //pullPreviousGoals($user_id,$type,$startDate,$endDate);
    foreach ($goals as $key => $goal) {

        if (!empty($goal)) {
            $common->insert($table_name, ['user_id' => $user_id, 'type' => $type, 'goal' => $goal, 'start_date' => $startDate, 'end_date' => $endDate]);
            $id = $common->insertId();
            $addedGoals[$id] = $goal;
        }

    }
    echo json_encode(['success' => true, 'goals' => $addedGoals]);

}

if (isset($_POST['EmailSendDailyGoal']) && ($_POST['EmailSendDailyGoal'] == 'EmailSendDailyGoal')) {
    $dailyImprovements = $format->validation($_POST['dailyImprovements']);
    $dailyEvolution = $format->validation($_POST['dailyEvolution']);
    $toEmail = $format->validation($_POST['toEmail']);
    $currentDate = date('Y-m-d', strtotime($format->validation($_POST['currentDate'])));
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }


    $dailyTopGoals = [];
    $dailyLifeGoals = [];

    $dailyTopGoals = $common->get("daily_top_goals", 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $currentDate]);

    $dailyLifeGoals = $common->get("daily_life_goals", 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $currentDate]);
    if (!$dailyLifeGoals) {
        $row = $common->first(
            table: "daily_life_goals",
            cond: 'user_id = :user_id  AND created_at <= :created_at',
            params: ['user_id' => $user_id, 'created_at' => $currentDate],
            orderBy: 'id',
            order: 'DESC'
        );
        if ($row) {
            $dailyLifeGoals = $common->get("daily_life_goals", 'user_id = :user_id AND created_at = :created_at', ['user_id' => $user_id, 'created_at' => $row['created_at']]);
        }
    }

    $topGoalsHtml = '<ol>';
    foreach ($dailyTopGoals as $goal) {
        $topGoalsHtml .= '<li>' . $goal['goal'] . '</li>';
    }
    $topGoalsHtml .= '</ol>';

    $lifeGoalsHtml = '<ol>';
    foreach ($dailyLifeGoals as $goal) {
        $lifeGoalsHtml .= '<li>' . $goal['goal'] . '</li>';
    }
    $lifeGoalsHtml .= '</ol>';

    $goalBodyHtml = '<div style="width:600px; background-color:#FFF; margin:0 auto;">';
    $goalBodyHtml .= '<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
    $goalBodyHtml .= '<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">' . date('l F d , Y', strtotime($currentDate)) . '</h2>  
            
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;"><h4>Objectives and priorities today: 7-Objetivos y Prioridades Hoy</h4> ' . $topGoalsHtml . '</div>  
        
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Resumen del Día. Las 7-Victorias o Triunfos Hoy</h4><div style="">' . html_entity_decode($dailyEvolution) . '</div></div>      
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;"><h4>Qué Podías haber hecho Mejor?</h4>' . $lifeGoalsHtml . '</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Tus 7-Objetivos y Prioridades Más Importantes para tu Vida:</h4><div style="">' . html_entity_decode($dailyImprovements) . '</div></div>   
      </div>';
    $goalBodyHtml .= '<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0; text-align:center;">Mejorcadadia.com</p><p style="clear:both; margin:0; padding:0; text-align:center;">All rights reserved 2022</p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date = date('Y-m-d');
    $user = $common->first('users', 'id=:id', ['id' => $user_id]);
    if ($user) {
        if ($user) {
            $Title = "Victory-7";
            $email = 'verify@mejorcadadia.com';
            $email = $user['gmail'];
            $from = $user['full_name'] . '<' . $email . '>';

            $mail = new PHPMailer();
            $mail->isSMTP();

            $mail->Host = "smtp.ionos.es";
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->Username = "verify@mejorcadadia.com";
            $mail->Password = "hQjg-D?x9Pr+Knvb@rexU)4J%9E?fVD,dzK";
            $mail->Subject = $Title;
            $mail->setFrom($email);
            $mail->addReplyTo('verify@mejorcadadia.com');
            $mail->addReplyTo($email);
            $mail->isHTML(true);
            // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png');
            $mail->Body = '
                    <html>
                        <head>
                            <title>' . $Title . '</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            ' . $goalBodyHtml . '
                        </div>
                        </body></html>';
            $mail->AltBody = "This is the plain text version of the email content";
            //$emailto='ehsan.ullah.tarar@gmail.com';
            $mail->addAddress($toEmail);
            if ($mail->send()) {
                echo 'Insert';
            } else {
                echo 'Failed to send mail!';
            }
            $mail->smtpClose();
        } else {
            echo 'Something is wrong!!';
        }
    } else {
        echo 'Something is wrong!';
    }


}

if (isset($_POST['EmailSendSuperGoal']) && ($_POST['EmailSendSuperGoal'] == 'EmailSendSuperGoal')) {
    $description = $format->validation($_POST['description']);
    $type = $format->validation($_POST['type']);
    $toEmail = $format->validation($_POST['toEmail']);
    $startDate = date('Y-m-d', strtotime($format->validation($_POST['startDate'])));
    $endDate = date('Y-m-d', strtotime($format->validation($_POST['endDate'])));

    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $goals = $common->get(
        "supergoals",
        'user_id = :user_id AND type = :type AND start_date >= :start_date AND end_date <= :end_date',
        ['user_id' => $user_id, 'type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]
    );
    if (!$goals) {
        $goals = [];
    }

    $df = 'd-m-Y';
    if ($type == 'yearly') {
        $df = 'Y';
    }
    $goalsHtml = '<ol>';
    foreach ($goals as $goal) {
        $goalsHtml .= '<li>' . $goal['goal'] . '</li>';
    }
    $goalsHtml .= '</ol>';
    $goalBodyHtml = '<div style="width:600px; background-color:#FFF; margin:0 auto;">';
    $goalBodyHtml .= '<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
    $goalBodyHtml .= '<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">' . $type . ' Super Goals</h2>
        <p><label>From :</label> <span>' . date('l F d , Y', strtotime($startDate)) . '</span></p>
        <p><label>To :</label> <span>' . date('l F d , Y', strtotime($endDate)) . '</span></p>
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;">' . $goalsHtml . '</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Evaluation / Progress this year; things to improve</h4><div style="">' . html_entity_decode($description) . '</div></div>      
      </div>';
    $goalBodyHtml .= '<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0; text-align:center;">Mejorcadadia.com</p><p style="clear:both; margin:0; padding:0; text-align:center;">All rights reserved 2022</p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date = date('Y-m-d');
    $user = $common->first("users", "id = :id", ['id' => $user_id]);
    if ($user) {
        $Title = "SuperGoals - " . $type;
        $email = 'verify@mejorcadadia.com';
        $email = $user['gmail'];
        $from = $user['full_name'] . '<' . $email . '>';
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = "smtp.ionos.es";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = "verify@mejorcadadia.com";
        $mail->Password = "hQjg-D?x9Pr+Knvb@rexU)4J%9E?fVD,dzK";
        $mail->Subject = $Title;
        $mail->setFrom($email);
        $mail->addReplyTo('verify@mejorcadadia.com');
        $mail->addReplyTo($email);
        $mail->isHTML(true);
        // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png');
        $mail->Body = '
                    <html>
                        <head>
                            <title>' . $Title . '</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            ' . $goalBodyHtml . '
                        </div>
                        </body></html>';
        $mail->AltBody = "This is the plain text version of the email content";
        //$emailto='ehsan.ullah.tarar@gmail.com';
        $mail->addAddress($toEmail);
        if ($mail->send()) {
            echo 'Insert';
        } else {
            echo 'Failed to send mail!';
        }
        $mail->smtpClose();
    } else {
        echo 'Something is wrong!';
    }


}

if (isset($_POST['action']) && ($_POST['action'] == 'EmailSendDailyCommitment')) {
    $description = $format->validation($_POST['dailyEvolution']);
    $toEmail = $format->validation($_POST['toEmail']);
    $selectedDate = $format->validation($_POST['selectedDate']);

    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    if ($selectedDate < $today) {
        $goalDate = $selectedDate;
    } else {
        $goalDate = $today;
    }
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

    $goalsHtml = '<ol>';
    foreach ($goals as $goal) {
        $goalsHtml .= '<li>' . $goal['goal'] . '</li>';
    }
    $goalsHtml .= '</ol>';
    $goalBodyHtml = '<div style="width:600px; background-color:#FFF; margin:0 auto;">';
    $goalBodyHtml .= '<header style="background-color: #74be41;"><img src="https://mejorcadadia.com/users/assets/logo.png"></header>';
    $goalBodyHtml .= '<div style="padding:20px; background-color:#FFF; ">
        <h2 style="text-transform: capitalize;">Guerrero Diario </h2>
        <p><label>' . date('l F d , Y', strtotime($selectedDate)) . '</label></p>     
        <div class="goals-area" style="margin-top:20px; margin-bottom:40px;">' . $goalsHtml . '</div>  
        <div class="description-area" style="margin-top:20px; margin-bottom:40px;"><h4>Evaluación y Mejoramiento</h4><div style="">' . html_entity_decode($description) . '</div></div>      
      </div>';
    $goalBodyHtml .= '<footer style="background-color: #fef200; padding:20px;"><p style="clear:both; margin:0; padding:0; text-align:center;">Mejorcadadia.com</p><p style="clear:both; margin:0; padding:0; text-align:center;">All rights reserved 2022</p><div style="clear:both; padding:0; margin:0;"></div> </footer></div>';

    $AdminId = 0;
    $Date = date('Y-m-d');
    sendEmail($user_id, 'Guerrero Diario', $toEmail, $goalBodyHtml);
}
if (isset($_POST['action']) && ($_POST['action'] == 'DeleteV7MediaFile')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("uploaded_files", "id = :id", ['id' => $id]);
        echo 'Delete';
    } else {
        echo 'Something is wrong!';
    }
}

if (isset($_POST['action']) && ($_POST['action'] == 'DeleteUserFolder')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("user_folder_photos", "folder_id = :id", ['id' => $id]);
        $app_delete = $common->delete("user_photo_folders", "id = :id", ['id' => $id]);
        echo 'Delete';
    } else {
        echo 'Something is wrong!';
    }
}

if (isset($_POST['action']) && ($_POST['action'] == 'DeleteUserFolderImage')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("user_folder_photos", "id = :id", ['id' => $id]);
        echo 'Delete';
    } else {
        echo 'Something is wrong!';
    }
}
if (isset($_POST['action']) && ($_POST['action'] == 'DeleteDreamWallImage')) {
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
        $app_delete = $common->delete("dreamwall_images", "id = :id", ['id' => $id]);
        echo 'Delete';
    } else {
        echo 'Something is wrong!';
    }
}

const IMAGE_HANDLERS = [
    IMAGETYPE_JPEG => [
        'load' => 'imagecreatefromjpeg',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_PNG => [
        'load' => 'imagecreatefrompng',
        'save' => 'imagejpeg',
        'quality' => 100
    ],
    IMAGETYPE_GIF => [
        'load' => 'imagecreatefromgif',
        'save' => 'imagejpeg'
    ],
    IMAGETYPE_WEBP => [
        'load' => 'imagecreatefromwebp',
        'save' => 'imagejpeg'
    ],
     
];
function createThumbnail($src, $dest, $targetWidth, $targetHeight = null) {

    
    $type = exif_imagetype($src);
    if (!$type || !IMAGE_HANDLERS[$type]) {
        return null;
    }
    $image = call_user_func(IMAGE_HANDLERS[$type]['load'], $src);
    

    // no image found at supplied location -> exit
    if (!$image) {
        return null;
    }


    $width = imagesx($image);
    $height = imagesy($image);

    // maintain aspect ratio when no height set
    if ($targetHeight == null) {

        // get width to height ratio
        $ratio = $width / $height;

        // if is portrait
        // use ratio to scale height to fit in square
        if ($width > $height) {
            $targetHeight = floor($targetWidth / $ratio);
        }
        // if is landscape
        // use ratio to scale width to fit in square
        else {
            $targetHeight = $targetWidth;
            $targetWidth = floor($targetWidth * $ratio);
        }
    }

    
    // create duplicate image based on calculated target size
    $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

    // set transparency options for GIFs and PNGs
    if ($type == IMAGETYPE_GIF || $type == IMAGETYPE_PNG) {

        // make image transparent
        imagecolortransparent(
            $thumbnail,
            imagecolorallocate($thumbnail, 0, 0, 0)
        );

        // additional settings for PNGs
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    }

    // copy entire source image to duplicate image and resize
    imagecopyresampled(
        $thumbnail,
        $image,
        0, 0, 0, 0,
        $targetWidth, $targetHeight,
        $width, $height
    );
    try{
        $exif = exif_read_data($src);
        if(!empty($exif) && !empty($exif['Orientation'])){
            $orientation = $exif['Orientation'];
            switch ($orientation) {
                case 2:
                    imageflip($thumbnail, IMG_FLIP_HORIZONTAL);
                    break;
                case 3:
                    $thumbnail = imagerotate($thumbnail, 180, 0);
                    break;
                case 4:
                    imageflip($thumbnail, IMG_FLIP_VERTICAL);
                    break;
                case 5:
                    $thumbnail = imagerotate($thumbnail, -90, 0);
                    imageflip($thumbnail, IMG_FLIP_HORIZONTAL);
                    break;
                case 6:
                    $thumbnail = imagerotate($thumbnail, -90, 0);
                    break;
                case 7:
                    $thumbnail = imagerotate($thumbnail, 90, 0);
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                    break;
                case 8:
                    $thumbnail = imagerotate($thumbnail, 90, 0); 
                    break;
            }
        }
    }catch(Exception $exp) {

    }
    
   
    $thumbImage=imagejpeg($thumbnail,$dest);
   
    return $thumbImage;
}
if (isset($_POST['action']) && ($_POST['action'] == 'UploadFolderImage')) {
    
    $maxAllowed=20;
    $hash = $format->validation($_POST['hash']);
    $folder_id=$format->validation($_POST['folder_id']);
    $user_id = Session::get('user_id');
   
    $resAr=['url'=>"",'success'=>false,'msg'=>'','hash'=>$hash];
    $fileCounts = $common->count(
        "user_folder_photos",
        'user_id = :user_id AND folder_id=:folder_id',
        ['user_id' => $user_id,'folder_id'=>$folder_id]
    );
    if($fileCounts<$maxAllowed){
        $file_name=strtolower(basename($_FILES["file"]["name"])); 
        $target_dir = "../uploads/photodrive/".$user_id."/";
        if ( !is_dir( $target_dir ) ) {
            mkdir( $target_dir,0777,true );       
        }
        $target_file = $target_dir . $file_name;
        $target_thumb = $target_dir .$file_name.'_thumb.jpg';
        $target_file_url=SITE_URL."/users/uploads/photodrive/".$user_id."/".$file_name;
        $target_thumb_url=$target_file_url.'_thumb.jpg';
        
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $resAr['file_type']=$FileType;
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        $uploadOk=1;
        if ($uploadOk == 0) {  
         $resAr['msg']="Sorry, your file was not uploaded.";
       } else {
         if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) { 
            $postData=[
                'user_id' => $user_id,
                'folder_id' => $folder_id,
                'url' => $target_file_url,
                'thumb' => $target_file_url
            ];
            try {
                createThumbnail($target_file,$target_thumb, 300,300);
                $postData['thumb']=$target_thumb_url;

            }catch (ImagickException $e) {
                $resAr['msg']=$e->getMessage();
            }    
            $file_insert = $common->insert("user_folder_photos", $postData);
            $fileId = $common->insertId(); 
            $resAr['thumb_url']=$target_thumb_url;           
           $resAr['file_url']=$target_file_url;
           $resAr['success']=true;
           $resAr['id']=$fileId;
         } else {
             $resAr['msg']="Sorry, there was an error uploading your file.";
           
         }
       }
    }else{
        $resAr['msg']="Maximum limit reached.";
    }
    echo  json_encode($resAr);
}

if (isset($_POST['action']) && ($_POST['action'] == 'UploadDreamWallImage')) {
    
    $maxAllowed=20;
    $selectedDate=$today;
    $hash = $format->validation($_POST['hash']);
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $resAr=['url'=>"",'success'=>false,'msg'=>'','hash'=>$hash];
    $fileCounts = $common->count(
        "dreamwall_images",
        'user_id = :user_id',
        ['user_id' => $user_id]
    );
    if($fileCounts<$maxAllowed){
        $file_name=strtolower(basename($_FILES["file"]["name"])); 
        $target_dir = "../uploads/dreamwall/".$user_id."/";
        if ( !is_dir( $target_dir ) ) {
            mkdir( $target_dir,0777,true );       
        }
        $target_file = $target_dir . $file_name;
        $target_thumb = $target_dir .$file_name.'_thumb.jpg';
        $target_file_url=SITE_URL."/users/uploads/dreamwall/".$user_id."/".$file_name;
        $target_thumb_url=$target_file_url.'_thumb.jpg';
        
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $resAr['file_type']=$FileType;
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        $uploadOk=1;
        if ($uploadOk == 0) {  
         $resAr['msg']="Sorry, your file was not uploaded.";
       } else {
         if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) { 
            $postData=[
                'user_id' => $user_id,
                'created_at' => $selectedDate,
                'url' => $target_file_url,
                'thumb' => $target_file_url
            ];
            try {
                createThumbnail($target_file,$target_thumb, 300,300);
                $postData['thumb']=$target_thumb_url;

            }catch (ImagickException $e) {
                $resAr['msg']=$e->getMessage();
            }    
            $file_insert = $common->insert("dreamwall_images", $postData);
            $fileId = $common->insertId(); 
            $resAr['thumb_url']=$target_thumb_url;           
           $resAr['file_url']=$target_file_url;
           $resAr['success']=true;
           $resAr['id']=$fileId;
         } else {
             $resAr['msg']="Sorry, there was an error uploading your file.";
           
         }
       }
    }else{
        $resAr['msg']="Maximum limit reached.";
    }
    echo  json_encode($resAr);
}
if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'UploadV7File')) {
    
    $date = $format->validation($_POST['date']);
    $type = $format->validation($_POST['type']);
    $maxAllowed=1;
    if($type=='image')
        $maxAllowed=7;
    $selectedDate=$today;
    if(!empty($date))
    $selectedDate=$date;
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $resAr=['url'=>"",'success'=>false,'msg'=>'','type'=>$type];
    $fileCounts = $common->count(
        "uploaded_files",
        'type = :type AND user_id = :user_id AND DATE(created_at) = :created_at',
        ['type' => $type, 'user_id' => $user_id, 'created_at' => $selectedDate]
    );
    if($fileCounts<$maxAllowed){
        $file_name=strtolower(basename($_FILES["file"]["name"])); 
        $target_dir = "../uploads/victory7/".$user_id."/".$selectedDate."/";
        if ( !is_dir( $target_dir ) ) {
            mkdir( $target_dir,0777,true );       
        }
        $target_file = $target_dir . $file_name;
        $target_thumb = $target_dir .$file_name.'_thumb.jpg';
        $target_file_url=SITE_URL."/users/uploads/victory7/".$user_id."/".$selectedDate."/".$file_name;
        $target_thumb_url=$target_file_url.'_thumb.jpg';
        
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $resAr['file_type']=$FileType;
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        $uploadOk=1;
        if ($uploadOk == 0) {  
         $resAr['msg']="Sorry, your file was not uploaded.";
       } else {
         if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) { 
            $postData=[
                'type' => $type,
                'user_id' => $user_id,
                'created_at' => $selectedDate,
                'url' => $target_file_url,
                'thumb' => $target_file_url
            ];
            try {
                createThumbnail($target_file,$target_thumb, 300,300);
                $postData['thumb']=$target_thumb_url;

            }catch (ImagickException $e) {
                echo $e->getMessage();
            }    
            $file_insert = $common->insert("uploaded_files", $postData);
            $fileId = $common->insertId(); 
            if($type=='image'){
                $resAr['url']=$target_thumb_url;
            }else{
                $resAr['url']=$target_file_url;
            }
           
           $resAr['file_url']=$target_file_url;
           $resAr['success']=true;
           $resAr['id']=$fileId;
         } else {
             $resAr['msg']="Sorry, there was an error uploading your file.";
           
         }
       }
    }else{
        $resAr['msg']="Maximum limit reached.";
    }
    echo  json_encode($resAr);
}


function sendEmail($user_id, $Title, $toEmail, $body)
{
    global $common;
    $user = $common->first("users", "id = :id", ['id' => $user_id]);

    if ($user) {

        $fromEmail = 'verify@mejorcadadia.com';
        $email = $user['gmail'];
        $from = $user['full_name'] . '<' . $fromEmail . '>';
        $mail = new PHPMailer();
        $mail->isSMTP();
        // $mail->SMTPDebug = 2;
        $mail->Host = "smtp.ionos.es";
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->Username = "verify@mejorcadadia.com";
        $mail->Password = "hQjg-D?x9Pr+Knvb@rexU)4J%9E?fVD,dzK";
        // $mail->Subject = $Title;
        $mail->charSet = "UTF-8";
        $mail->Subject = '=?utf-8?B?' . base64_encode($Title) . '?=';
        $mail->setFrom($fromEmail, $user['full_name']);
        $mail->addReplyTo('verify@mejorcadadia.com');
        $mail->addReplyTo($email);
        $mail->isHTML(true);

        // $mail->AddEmbeddedImage('../assets/logo.png', 'logoimg', '../assets/logo.png');
        $mail->Body = '
                    <html>
                        <head>
                            <title>' . $Title . '</title>
                        </head>
                        <body>
                        <div style="background-color:#f3f2f0;">                        
                            ' . $body . '
                        </div>
                        </body></html>';
        $mail->AltBody = "This is the plain text version of the email content";
        //$emailto='ehsan.ullah.tarar@gmail.com';
        $mail->addAddress($toEmail);
        if ($mail->send()) {
            return true;
        } else {

            echo 'Mailer Error: ' . $mail->ErrorInfo;

        }
        $mail->smtpClose();
    } else {
        echo 'Something is wrong!';
    }
}


?>