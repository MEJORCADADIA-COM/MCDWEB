<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
//$preHead='<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />';
?>
<?php  require_once "inc/header.php"; ?>

<style>
  
  .price-plan {
  list-style-type: none;
  border: 1px solid #eee;
  margin: 0;
  padding: 0;
  -webkit-transition: 0.3s;
  transition: 0.3s;
}

.price-plan:hover {
  box-shadow: 0 8px 12px 0 rgba(0,0,0,0.2)
}

.price-plan .header {
  background-color: #fef200;
  color: #000;
  font-size: 25px;
}

.price-plan li {
  border-bottom: 1px solid #eee;
  padding: 20px;
  text-align: center;
}

.price-plan .grey {
  background-color: #eee;
  font-size: 20px;
}
table{
  color:#FFF !important;
}
button,.btn{
  box-shadow: none !important;
}
</style>
<?php
$user_id = Session::get('user_id');
 $dropbox_access_token = Session::get('dropbox_access_token');
$dropbox_client_id='qrtsqcvytf9zn4h';
$redirect_url = SITE_URL.'/users/backup.php';
$isDropBoxAutheticated=false;
$selectedPlan=isset($_GET['plan'])? trim($_GET['plan']):'';
$action=isset($_GET['action'])? trim($_GET['action']):'';

$userBackUp = $common->first(
  'dropbox_backups',
  'user_id = :user_id',
  ['user_id' => $user_id],
  ['*']
);



if(!empty($dropbox_access_token)){
   $token_received_time =  Session::get('dropbox_access_token_time');
  $expires_in =  Session::get('dropbox_expires_in');
  $current_time = time();
  $elapsed_time = $current_time - $token_received_time;
  if ($elapsed_time >= $expires_in) {
    $isDropBoxAutheticated=false;
  } else {
      $isDropBoxAutheticated=true;
  }
}

if(isset($_GET['code']) && !empty($_GET['code'])){
  $code=$_GET['code'];
  $dropbox_secret = 'n9gjmjjagwfjf6l';


  $url = 'https://api.dropbox.com/oauth2/token';
  
  $data = array(
      'code' => $code,
      'grant_type' => 'authorization_code',
      'redirect_uri' => $redirect_url
  );
  
  $query_string = http_build_query($data);
  
  $ch = curl_init($url);
  
  curl_setopt($ch, CURLOPT_USERPWD, $dropbox_client_id . ":" . $dropbox_secret);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  
  $response = curl_exec($ch);
  
  curl_close($ch);
  
  // The response is in json format
  $json = json_decode($response);
  if(!empty($json->access_token)){    
    Session::set('dropbox_access_token',$json->access_token);
    Session::set('dropbox_refresh_token',$json->refresh_token);
    Session::set('dropbox_account_id',$json->account_id);
    Session::set('dropbox_expires_in',$json->expires_in);
    Session::set('dropbox_access_token_time',time());
    $isDropBoxAutheticated=true;
    $dropbox_access_token=$json->access_token;
  }  
}

$authorization_url = 'https://www.dropbox.com/oauth2/authorize?client_id=' . $dropbox_client_id
    . '&token_access_type=offline'
    . '&response_type=code'
    . '&redirect_uri=' . $redirect_url;
?>
<script>
  var SITE_URL = '<?= SITE_URL; ?>'; 
  var DrobboxAccessToken= '<?=$dropbox_access_token; ?>'; 
  var CLIENT_ID= '<?=$dropbox_client_id; ?>'; 
  var selectedPlan='<?=$selectedPlan;?>';
</script>

<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropbox.js/10.34.0/Dropbox-sdk.min.js"></script>

<style>
  
                        
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3">

  <?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        <div class="row">
          
          <div class="col-sm-12 col-12" style="text-align:center;">
            <h2 style="text-transform: capitalize;">Data Backup </h2>
            <div id="dropboxAccountInfo" style="text-align:left;"></div>
          </div>
          
        </div>


      </header>
    
      
     
      <div class="container main">
        <?php  if($isDropBoxAutheticated===true): ?>
          <div id="authed-section">          

            
            <?php if($action=='create-backup' && empty($selectedPlan)): ?>
              <div class="row">
                <div class="col-md-4">
                  <ul class="price-plan">
                    <li class="header"><?=translate('15 Días')?></li>
                   
                    <li class="grey"><a href="<?=SITE_URL;?>/users/backup.php?plan=15-days" class="btn btn-info">Backup Now</a></li>
                  </ul>
                </div>
                <div class="col-md-4">
                  <ul class="price-plan">
                    <li class="header">30 <?=translate('Días')?></li>
                    
                    <li class="grey"><a href="<?=SITE_URL;?>/users/backup.php?plan=30-days" class="btn btn-info">Backup Now</a></li>
                  </ul>
                </div>
                <div class="col-md-4">
                  <ul class="price-plan">
                    <li class="header"><?=translate('Todo')?></li>                 
                    <li class="grey"><a href="<?=SITE_URL;?>/users/backup.php?plan=all-time" class="btn btn-info"><?=translate('Backup Now')?></a></li>
                  </ul>
                </div>
            </div>  
            <?php elseif(!empty($selectedPlan)): ?>
              <div class="mt-2" style="background-color: #fef200; padding: 10px">
                 <h3 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; "><?=translate('Has elegido Plan de')?> <?=$selectedPlan;?>
                     </h3>
              </div>
              <div class="cardd mb-4 py-4" id="section-1">
                <div class="btn-wrapper"><button class="btn btn-primary" id="startBackupBtn"><?=translate('Comienza Backup')?></button></div>
                <div id="progressBarContainer" style="display:none;">
                  <div id="progressBar" style="width: 0%; background-color: green; height: 30px;"></div>
              </div>
              </div>
            <?php else: ?>
              <div class="" id="backup-files"></div>         
            <?php endif; ?>
            
    </div>
        <?php else: ?>
          <div id="pre-auth-section" style="text-align:center;">
            <a href="<?=$authorization_url?>" id="authlinkd" class="button btn btn-warning"><i class="fa fa-dropbox"></i> <?=translate('Accede a tu Cuenta de DropBox')?></a>
          </div>
        <?php endif; ?>

  
  </div>

     



      
    </div>
  </div>

  
</main>







<div class="toast-container position-absolute top-0 end-0 p-3">
  <div class="toast" id="toast">

    <div class="toast-body">
      Hello, world! This is a toast message.
    </div>
  </div>
</div>

<?php if($isDropBoxAutheticated): ?>
<script>
  
  var dbx = new Dropbox.Dropbox({ accessToken: DrobboxAccessToken });
  function ImportBackup(filePath){
    $('#importBtn').text('Importing...');
    $.ajax({
      url: SITE_URL+"/users/ajax/ajax.php",
      type: "POST",
      dataType: "json",
      data:{action:'importDropboxBackup','filePath':filePath},
      success: function(response) {
        showToast('success','Backup Imported Successfully.');
        $('#importBtn').text('Import');
      },
      error: function(xhr, status, error) {
          console.error("Error: " + error);
          showToast('error',error);
          $('#importBtn').text('Import');
      }
    });
  }
  function renderItems(items) {
      //var filesContainer = document.getElementById('files');
      console.log('renderItems',items);
      var filesContainer = document.getElementById('backup-files');
      var filesHtml='';
      if(items.length>0){
        var rows='';
        var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        
        items.forEach(function(item) {   
          const javaScriptRelease = new Date(item.server_modified);        
          var actionBtn=`<button class="btn btn-primary" id="importBtn" onclick="ImportBackup('${item.path_display}')">Import</button>`;     
          rows+=`<tr><td><?php if($userBackUp){ echo $userBackUp['plan'];}?></td><td>${item.name} <br><span>Size: ${item.size}</span><br><span>Date: ${javaScriptRelease.toLocaleDateString('es-ES', options)}</span></td><td>${actionBtn}</td></tr>`;
        });
        var tbody=`<tbody>${rows}</tbody>`;
        var table=`<table class="table"><thead><tr><th>Plan</th><th>Backup</th><th>Action</th></tr></thead>${tbody}</table>`;
        filesHtml+=table;
      }
      filesHtml+=`<div class="create-backup-now"><a class="btn btn-info" href="${SITE_URL}/users/backup.php?action=create-backup">Crea tu Backup Ahora</a></div>`;
      filesContainer.innerHTML=filesHtml;
     
    }
    function getBaseName(filePath) {
        var parts = filePath.split(/[\\/]/);
        return parts[parts.length - 1];
    }
    function uploadFileToDropbox(filePath) {
      var dropboxPath = getBaseName(filePath);
      fetch(filePath)
                .then(function (response) {
                    if (response.ok) {
                        return response.blob();
                    } else {
                       // throw new Error('Network response was not ok');
                        showToast('error','Network response was not ok');
                        $("#startBackupBtn").text('Comienza Backup');    
                    }
                })
                .then(function (blob) {
                    dbx.filesUpload({ path: '/' + dropboxPath, contents: blob,mode: { '.tag': 'overwrite' } })
                        .then(function (response) {
                            console.log('File uploaded successfully:', response,response.result);
                            showToast('success','<?=translate("Su copia de seguridad se ha cargado en su cuenta de Dropbox."); ?>');
                            $("#startBackupBtn").text('<?=translate("Su copia de seguridad se ha cargado en su cuenta de Dropbox."); ?>');
                            $.ajax({
                              url: SITE_URL+"/users/ajax/ajax.php",
                              type: "POST",
                              dataType: "json",
                              data:{action:'addDropboxBackup',plan:selectedPlan,data:response.result},
                              success: function(response) {
                                window.location.href=SITE_URL+'/users/backup.php';
                              },
                              error: function(xhr, status, error) {
                                  console.error("Error: " + error);
                                  $("#startBackupBtn").text('Comienza Backup');
                              }
                            });
                        })
                        .catch(function (error) {
                            showToast('error',error);
                            $("#startBackupBtn").text('Comienza Backup');
                        });
                })
                .catch(function (error) {                  
                    showToast('error',error);
                    $("#startBackupBtn").text('Comienza Backup');
                });
      
  }
  function uploadToDropBox(staticFilePath){
    var file = new File([""], staticFilePath.split("/").pop(), {
        type: "image/png",
          lastModified: new Date().getTime()
        });
      dbx.filesUpload({path: '/' + file.name, contents: file})
          .then(function(response) {
          console.log(response);
          })
          .catch(function(error) {
            console.error(error.error || error);
          });
  }
  dbx.usersGetCurrentAccount()
      .then((response) => {
        console.log('Access token is valid.',response);       
        
        if(response.status==200){
          if(response.result && response.result.name){
            var accountInfo=response.result.name;
            $('#dropboxAccountInfo').html(`<h6>Cuenta de DropBox</h6> 
            <p>Display Name: ${accountInfo.display_name}</p>
            <p>Email: ${response.result.email}</p>
            <p><a href="https://www.dropbox.com/" class="btn-sm btn btn-warning mt-2" target="_blank">Open DropBox</a></p>`
            );
          }
          
        }
        dbx.filesListFolder({path: ''})
          .then(function(response) {
            console.log('response',response);
            renderItems(response.result.entries);
          })
          .catch(function(error) {
            console.error(error.error || error);
          });
      })
      .catch((error) => {
        if (error.response && error.response.status === 401) {
          console.log('Access token is expired.');
        } else {
          console.error('Error checking access token:', error);
        }
      });
    
      $(document).ready(function() {

        $(document).on('click','#startBackupBtn',function(e){
          console.log('startBackupBtn');
          e.preventDefault();
          $("#startBackupBtn").text('Creating Backup...');
          $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            dataType: "json",
            data:{action:'dropboxBackupData',plan:selectedPlan},
            success: function(response) {
              $("#progressBarContainer").hide();
              if(response.success && response.backup_file_url!=""){
                $("#startBackupBtn").text('<?=translate('Subiendo Backup a tu Cuenta de DropBox...') ?>');
                uploadFileToDropbox(response.backup_file_url);
              } 
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
                $("#startBackupBtn").text('<?=translate('Comienza Backup') ?>');
            }
          });
        });
      });

</script>
<?php endif; ?>
<script> 
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

    return new bootstrap.Toast(toastEl) // No need for options; use the default options
  });
  toastList.forEach(toast => toast.show()); // This show them
} 
</script>

<?php require_once "inc/footer.php"; ?>