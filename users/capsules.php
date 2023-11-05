<?php require_once "inc/header.php"; ?>
<?php 
$date=empty($_GET['date'])? '':$_GET['date'];
$filter=empty($_GET['filter'])? '':$_GET['filter'];


$user_id = Session::get('user_id');

if(!empty($date) && !empty($filter)){
  if($filter=='week'){
   $week = (int)date('W', strtotime($date));
   $year=date('Y',strtotime($date));   
   $capsules = $common->get(table: "user_capsules", cond: 'user_id = :user_id AND YEAR(created_at)=:year AND WEEKOFYEAR(created_at)=:week', params: ['user_id' => $user_id,'year' => $year,'week' => $week], orderBy: 'created_at',order: 'DESC');
  }
  if($filter=='month'){
    $month=date('m',strtotime($date));
    $year=date('Y',strtotime($date));
    $capsules = $common->get(table: "user_capsules", cond: 'user_id = :user_id AND YEAR(created_at)=:year AND MONTH(created_at)=:month', params: ['user_id' => $user_id,'year' => $year,'month' => $month], orderBy: 'created_at',order: 'DESC');
  }

}else{
  $capsules = $common->get(table: "user_capsules", cond: 'user_id = :user_id', params: ['user_id' => $user_id], orderBy: 'created_at',order: 'DESC');
 
}



?>
<script>
 var SITE_URL='<?=SITE_URL; ?>';
 var selectedDate='<?=$date; ?>';
</script>
    <script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

    <script src="<?=SITE_URL; ?>/users/assets/countdown.min.js"></script>
    <style>
      .maincontonent {
          width: 100%;
          min-height: 100vh;
      }
      .admin-dashbord{
        background:#ed008c;
      }
      .projects{border:none;}
      #capsules-wrapper .card{
        margin-bottom:30px;
      }
      #capsules-wrapper .card .card-body{
        padding:0;
      }
      #capsules-wrapper .card .card-body textarea{
        border:none;
        color:#000;
      }
      #createCapsuleBtn{
        position:absolute;
        right:50px; 
        bottom:100px;
        z-index:999;
      }
      #create-capsule-wrapper{
     
      }
      .card-footer .nav.nav-fill .nav-link{
        font-size:10px;
      }
      .form-check-input:checked {
        background-color: #ebde0f;
        border-color: #ebe256;
      }
      @media screen and (max-width: 767px) {
        #createCapsuleBtn{
          position: absolute;
          right:50px; 
          bottom:50px;
          z-index:999;
        }
      }
      
      
    </style>
    <?php
    $currentDate=date('Y-m-d H:i:s')
    ?>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 " >
      <div class="projects mb-4" style="background-color: #ed008c; min-height: 100vh;" >
        <div class="projects-inner" style="padding-left:10px; padding-right:10px;" id="projects-inner-wrapper">
          <header class="projects-header" style="">
            <div class="row">         
                <div class="col-sm-12 col-12" style="text-align:center;">                
                  <h1 style="text-transform: capitalize;"><?=translate('MejorCapsules');?></h1>  
                </div>             
            </div>
            <div class="row" style="margin-bottom:15px;">
            
            <div class="col-sm-3">
              <div class="input-group date datepicker" id="date">                
                <input type="text" class="form-control" value="<?=!empty($date)?date('d-m-Y', strtotime($date)):''; ?>" id="date" name="date"  readonly />
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
            </div>
            <div class="col-sm-3">
            <div class="form-check form-check-inline">
              <input class="form-check-input filter_by" type="radio" name="filter_by" id="filter_week" value="week" <?php if($filter=='week') echo 'checked'; ?>>
              <label class="form-check-label" for="filter_week"><?=translate('Por Semana');?></label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input filter_by" type="radio" name="filter_by" id="filter_month" value="month" <?php if($filter=='month') echo 'checked'; ?>>
              <label class="form-check-label" for="filter_month"><?=translate('Por Mes');?></label>
            </div>
            </div>
           
           
          </div>
          </header> 
        
        <div id="capsules-wrapper">
            <?php foreach($capsules as $item): ?>
              <div class="card" id="capsule-<?=$item['id'];?>">
                <div class="card-body">
                  <textarea class="form-control capsule_content" rows="5"  id="capsule_content_<?=$item['id'];?>" data-capsule="<?=$item['id'];?>" name="capsule_content[]"><?=$item['content'];?></textarea>                
                </div>
                <div class="card-footer text-muted">
                  <ul class="nav nav-fill">
                    <li class="nav-item left" style="text-align:left;">
                      <button type="button"  data-capsule="<?=$item['id'];?>" class="btn btn-primary btn-sm savecapsule"><i class="fa fa-save"></i></button>
                    </li>
                    <li class="nav-item right" style="text-align:right;">
                      <?php setlocale(LC_ALL, $locales[$userLanguage]);
                      $string = date('Y-m-d H:i:s', strtotime($item['created_at']));
                      $dateObj = DateTime::createFromFormat("Y-m-d H:i:s", $string);  ?>
                      <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"><?= utf8_encode(strftime("%A, %d %B, %Y %H:%M", $dateObj->getTimestamp())); ?> </a>
                      
                    </li>
                    <li class="nav-item right" style="text-align:right;"><button  data-capsule="<?=$item['id'];?>" title="Delete" type="button" class="btn btn-outline-danger btn-sm btn-inline delete_capsule"><i class="fa fa-trash-o"></i></button></li>
                  </ul>
                </div>
              </div>
            <?php endforeach; ?>            
             
       
        <button id="createCapsuleBtn" type="button" class="btn btn-lg btn-info rounded-circle"> <i class="fa fa-plus"></i></button>
        

         
        </div>
      </div>
      <div class="clearfix;"></div>
    </main>
   
   
   




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
         $('#toast').addClass('bg-info text-white');
         $('#toast').removeClass('bg-danger text-white');
       }else{
         $('#toast').removeClass('bg-info text-white');
         $('#toast').addClass('bg-danger text-white');
       }
       var toastElList = [].slice.call(document.querySelectorAll('.toast'));
       var toastList = toastElList.map(function(toastEl) {
       // Creates an array of toasts (it only initializes them)
       
         return new bootstrap.Toast(toastEl) // No need for options; use the default options
       });
      toastList.forEach(toast => toast.show()); // This show them
     }     
  let capsulesWrapper = document.getElementById("capsules-wrapper");
  let createCapsuleBtn = document.getElementById("createCapsuleBtn");
  $(document).on("click","textarea",function(e) {
      $('textarea').css('height','auto'); 
        this.style.overflow = 'hidden';
        //this.style.height = 0;
        this.style.height = this.scrollHeight + 'px';
  });
  $(document).on("keyup","textarea",function(e) {
        this.style.overflow = 'hidden';
        //this.style.height = 0;
        this.style.height = this.scrollHeight + 'px';
  });
  
  $(document).on("click","button.delete_capsule",function(e) {
    var self=$(this);
    var capParent=$(this).closest('.card');
    var result = confirm("Sure you want to delete?");
    if (result) {
      const capsuleid=$(this).data('capsule');
      console.log('capsuleid',capsuleid);
      $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'DeleteCapsule',
                    id:capsuleid,
                },
                success: function(json) {
                  capParent.remove();
                }
      });
    }
    
    
  });
  $(document).on("click","button.savecapsule",function(e) {
      var self=$(this);
      var capParent=$(this).closest('.card');
      var footerMenuNav=capParent.find('.nav');
      var dateWrap=capParent.find('.nav .nav-link');

      const capsuleid=$(this).data('capsule');
      const capsuleContent=$("#capsule-"+capsuleid+" textarea").val();
      //const capsuleContent=$("#capsule_content_"+capsuleid).val();
      console.log('capsuleContent',capsuleContent);
      if(capsuleContent!="" && capsuleContent!=null){
        $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'createCapsule',
                    content:capsuleContent, 
                    id:capsuleid,
                },
                success: function(json) {                   
                    const obj = JSON.parse(json);
                    console.log(obj,dateWrap);
                    if(obj.success){
                        if(obj.new){
                          self.data('capsule',obj.id);
                          capParent.attr('id','capsule-'+obj.id);
                          createFormAdded=false;
                          createdItems=0;
                          dateWrap.html(obj.date);
                          footerMenuNav.append('<li class="nav-item right" style="text-align:right;"><button  data-capsule="'+obj.id+'" title="Delete" type="button" class="btn btn-outline-danger btn-sm btn-inline delete_capsule"><i class="fa fa-trash-o"></i></button></li>')
                          showToast('success','Message has been updated.')
                        }else{  
                          showToast('success','Message has been updated.')
                        }
                    }
                }
            });
      }
  });
  var createFormAdded=false;
  var createdItems=0;


  createCapsuleBtn.addEventListener('click', event => { 
    
    console.log('createCapsuleBtn',event,$("#projects-inner-wrapper").offset().top);
    if(createFormAdded==false){
      var createTmp='';
      var nowDate=new Intl.DateTimeFormat('es-ES', { dateStyle: 'full', timeStyle: 'short' }).format(new Date());
      const row = document.createElement("div");
      const rowid = document.createAttribute("id");
      rowid.value = "capsule-"+createdItems;
      row.setAttributeNode(rowid);
      const nodebodyclass = document.createAttribute("class");
      nodebodyclass.value = "card";
      row.setAttributeNode(nodebodyclass);
      const cardBody='<div class="card-body"><textarea class="form-control" rows="5" data-capsule="0" id="capsule_content_0" name="capsule_content"></textarea></div>';
      const cardFooter='<div class="card-footer text-muted"><ul class="nav nav-fill">   <li class="nav-item left" style="text-align:left;"><button type="button" data-capsule="0" class="btn btn-primary btn-sm savecapsule"><i class="fa fa-save"></i></button></li> <li class="nav-item right" style="text-align:right;"><a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">'+nowDate+'</a></li></ul></div>';
      row.innerHTML=cardBody+cardFooter;
      capsulesWrapper.prepend(row);
      createFormAdded=true;
      createdItems++;
      $("html, body").animate({ scrollTop: 0 }, 600);
      $( "#capsule_content_0" ).focus();
    }
    
  });
  $(function() {
   
    $("input:radio[name=filter_by]").click(function(){
      var filter_by=$("input[name='filter_by']:checked").val();
        if(selectedDate!='' && selectedDate!=null && filter_by!="" && filter_by!=null){
          window.location.href = SITE_URL + "/users/capsules.php?filter=" + filter_by + "&date=" + selectedDate;
        }
    });
    
    $('.datepicker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        calendarWeeks: true,
        todayHighlight: true,
        weekStart: 1,
        clearBtn:true
        }).on('changeDate', function(e) {
          const filter_by=$('input[name="filter_by"]:checked').val();
          console.log('changeDate', e.date, e.format('yyyy-mm-dd'),filter_by);
          selectedDate=e.format('yyyy-mm-dd');
          if(selectedDate!="" && selectedDate!=null){
            if(filter_by!="" && filter_by!=null){
              window.location.href = SITE_URL + "/users/capsules.php?filter=" + filter_by + "&date=" + e.format('yyyy-mm-dd');
            }
          }else{
            window.location.href = SITE_URL + "/users/capsules.php";
          }
          
        
      });
    
    
  });
 </script>
<?php require_once "inc/footer.php"; ?>