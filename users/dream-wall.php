<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php require_once "inc/header.php"; ?>
<?php

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
$type = isset($_GET['type'])? trim($_GET['type']):'image';

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

<?php

$user_id = Session::get('user_id');

$dreamWallImages = $common->get('dreamwall_images', 'user_id = :user_id', ['user_id' => $user_id],[],'created_at','DESC');


$selectedDate = $currentDate;

$isPastDate = false;


if ($selectedDate < $today) {
  $goalDate = $selectedDate;
  $isPastDate = true;
} else {
  $goalDate = $today;
}
$isPastDate=false;



?>


<script>
  var SITE_URL = '<?= SITE_URL; ?>';
  var currentDate = '<?= $currentDate; ?>';
</script>
<link rel="stylesheet" href="<?=SITE_URL; ?>/users/assets/uikit-lightbox.css" />
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/js/uikit-icons.min.js"></script>

<style>
  .modal-header .modal-title{
    color:#FFF;
  }
  .v7-media-box{
    width:114px;
    position: relative;
  }
  .file-actions{
    position:absolute;
    top:10px; right:10px;
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
    width:106px;
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
.preview-img{
  height:104px;
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

<main role="main" class="col-md-9 ml-sm-auto col-lg-10">

<?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
        
        
        <div class="row">
          <div class="col-12" style="text-align:center;">
            <h2 style="text-transform: capitalize;">
            My DreamWall
            
            </h2>
          </div>
          
        </div>

      </header>
      <div class="media-items media-gallery p-2" style="min-height:750px;">
       
       
           
          
                
                <div class="d-flex flex-wrap bd-highlight mb-3" id="gallary-items" uk-lightbox="animation: slide">
                    
                    <?php  $i=0; foreach ($dreamWallImages as $key => $file):  ?>
                      <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($file['created_at']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
                    <div class="p-1 bd-highlight v7-media-box" data-index="<?=$key;?>"  data-file="<?=$file['id'];?>" style="order:1; position:relative;">
                            
                                <a href="<?=$file['url'];?>" data-index="<?=$key;?>"  data-file="<?=$file['id'];?>" data-caption="<?= utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp())); ?>" > 
                                <img data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?=$key;?>" class="img-fluid rounded-3 w-100 shadow-1-strong"  src="<?=$file['thumb'];?>">
                              </a>
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
                    <?php $i++; endforeach;?>
                    <div class="p-1 bd-highlight v7-media-box" style="order:0;">                        
                        <div class="upload-box-image upload-file-box" data-type="image">                        
                          <input type="file" name="newfileAdd" id="newfileAdd" class="inputfile" multiple="true" accept="image/png, image/gif, image/jpeg"  />
                          <label for="newfileAdd"><i class="fa fa-camera"></i></label>
                        </div>
                    </div>
                </div>
            
        
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

$(document).on('click','.file-actions .file_delete',function(e){
    console.log('de;ete');
    e.preventDefault();
    $parentElem=$(this).parents('.v7-media-box');
    let fileId=$parentElem.data('file');
    $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'DeleteDreamWallImage',
          currentDate: currentDate,
          id: fileId,
        },
        success: function(data) {
          console.log('data', data);
          $parentElem.remove();
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


 

  
  
  function createFilePreviewEle(id, url, type,$wrapper){
    console.log('createFilePreviewEle',id, url, type,$wrapper);
    
        let filePreview ='';
        filePreview= `<img alt="preview" class="preview-img files_img img-fluid rounded-3 w-100 shadow-1-strong" src="${url}"/>`;
       $wrapper=$("#gallary-items");
        let $previewCard = $(
            `<div class="p-2 bd-highlight v7-media-box" style="order:1" id="${id}">
                    ${filePreview}
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
                        <div class="jquery-uploader-preview-progress">
                            <div class="progress-mask"></div>
                            <div class="progress-loading">
                                <i class="fa fa-spinner fa-spin"></i>
                            </div>
                        </div>
                 </div>`);
        $wrapper.prepend($previewCard);
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
  function paramsBuilder(uploaderFile) {
        let form = new FormData();
        form.append("file", uploaderFile.file);
        form.append("action", 'UploadDreamWallImage');
        form.append("hash", uploaderFile.id);
       //form.append("date", currentDate);        
        return form;
  }
  function handleFileUpload(files){
    let addFiles = [];
    for (let i = 0; i < files.length; i++) {
            let file = files[i]
            let type = file.type;
            let url = BLOB_UTILS.createBlobUrl(file)
            let id = uuid()
            let $previewCard = createFilePreviewEle(id, url, type)
            
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
          $fileWrapperElem=$("#"+file.id);
          $.ajax({
            url: SITE_URL+'/users/ajax/ajax.php',
            contentType: false,
            processData: false,
            method: "POST",
            data: paramsBuilder(file),
            success: function (json) {
              console.log('success response',json);
              let response=JSON.parse(json);            
              if(response.success){
                $uploadFileWrapper=$("#"+response.hash);
                $uploadFileWrapper.find('.jquery-uploader-preview-progress').hide();              
                
                 // $fileWrapperElem.find('img').attr('src',response.url);
                 $uploadFileWrapper.find('img').remove();
                  let $audioElm=$(`<a href="${response.file_url}"> <img class="img-fluid rounded-3 w-100 shadow-1-strong"  src="${response.thumb_url}"></a>`);
                  $uploadFileWrapper.prepend($audioElm);
                
                
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
     
      handleFileUpload(this.files)
      
    });
  });


  $(function() {
   
  });
</script>
<div id="popovertip" data-page="dreamwall" data-bs-custom-class="mejor-info-popover" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-content="Crea la 'Pared de tus Sueños'. Agrega Imágenes que Representen lo que quieres tener o Manifestar en tu Vida. Empodera tu mente viéndo esta pared todos los días. ¡Nunca te des por vencido en lo que es importante para tí!"></div>
<?php require_once "inc/footer.php"; ?>