<?php
    /*Just for your server-side code*/
   // header('Content-Type: text/html; charset=ISO-8859-1');
?>
<?php require_once "inc/header.php"; ?>
<?php 

$user_id = Session::get('user_id');
$folder_id=empty($_GET['folder_id'])? 0: $_GET['folder_id'];

$notesItems = $common->get(table: "user_notes", cond: 'user_id = :user_id AND folder_id = :folder_id', params: ['user_id' => $user_id,'folder_id' => $folder_id], orderBy: 'created_at',order: 'DESC');
$currentFolder = $common->first(
  "user_folders",
  "`id` = :id AND user_id = :user_id",
  ['id' => $folder_id, 'user_id' => $user_id]
);

?>


<script>
 

   
      var SITE_URL='<?=SITE_URL; ?>';
      var folder_id='<?=$folder_id;?>';
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
      #createNotesBtn{
        position:absolute;
        right:50px; 
        bottom:100px;
        z-index:999;
      }
      .modal-header .btn-close{
        margin:0;
        padding:0;
        background:none;
        opacity: 0.8;
      }
      .modal-header .actions-menu .dropdown-toggle::after{
        display:none;
      }
      .modal-header .btn-close i, .modal-header .actions-menu svg{
        color:#FFF;
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
        .projects-inner{
          position:relative;
        }
        #createNotesBtn{
          position: absolute;
          right:30px; 
          bottom:-100px;
          z-index:999;
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
      td.notebody .date{
        float:right;
        font-size:12px;
      }
      /*td.notebody{
        overflow: hidden;
        text-overflow: ellipsis; 
        white-space: pre-wrap;
         word-wrap: break-word; 
        height: 4rem;
        display: block;
        line-height:1.5rem;
      }*/
     
      
    </style>

    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 " >
      <div class="projects mb-4" style="background-color: #ed008c; min-height: 100vh;" >
        <div class="projects-inner" style="padding-left:10px; padding-right:10px;">
          <header class="projects-header" style="">
            <div class="row">         
                <div class="col-sm-12 col-12" style="text-align:center;">
                <?php if(!empty($currentFolder)): ?>
                  <h1 style="text-transform: capitalize;"><?=$currentFolder['name']?></h1>
                <?php else: ?>
                  <h1 style="text-transform: capitalize;">My Notes</h1>
                <?php endif; ?>
                  
                </div>             
            </div>
          </header> 
         
       
          <table class="table table-light table-hover" id="notesTable">
            <thead>
              <tr>                
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
             <?php foreach($notesItems as $item): ?>
              <tr id="nid-<?=$item['id']?>">
              <td class="notebody" data-bs-id="<?=$item['id']?>" data-bs-toggle="modal" data-bs-target="#createNotesModal">
                <div class="content"><?=substr(strip_tags($item['notes']),0,80);?></div>
                <p class="date"><?=date('m-d-Y',strtotime($item['created_at']));?></p>
              </td>
                
              </tr>
              <?php endforeach; ?>
              
             
            </tbody>
          </table>
            
       
        <button id="createNotesBtn" type="button" class="btn btn-lg btn-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#createNotesModal"> <i class="fa fa-plus"></i></button>
            <!-- Modal -->
          <div class="modal fade" id="createNotesModal" tabindex="-1" aria-labelledby="createNotesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-md-down">
              <div class="modal-content">
                <form method="POST" id="createNotesForm">
                <div class="modal-header text-bg-primary">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-arrow-left"></i></button>
                  <div class="btn-group float-end actions-menu">
                    <button type="button" class="btn btn-default dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
  <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
</svg>
                    </button>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#"  onclick="DeleteNotes()">Delete</a></li>
                    <li><hr class="dropdown-divider"></li>
                      <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#foldersModal">Move to Folder</a></li>
                    </ul>
                </div>
                  
                </div>
                <div class="modal-body">
                  
                  <div class="form-group">
                    <textarea class="form-control" style="height:100%; min-height:400px;" name="notes" id="notes"></textarea>
                  </div>
                  <input type="hidden" name="notes_id" id="notes_id" value="0">
                </div>
                <div class="modal-footer">                  
                </div>
                </form>
              </div>
            </div>
          </div>

          <div class="modal fade" id="foldersModal" tabindex="-1" aria-labelledby="foldersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
              <div class="modal-content">
                
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="foldersModalLabel">Move to Folder</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <input type="hidden" name="move_note_id" id="move_note_id" class="move_note_id" value="">
                <div><h6 data-bs-nid="0" style="cursor:pointer;" onclick="MoveToFolder(0)"><i class="fa fa-folder-o me-3"></i> My Notes</h6></div>
                <?php foreach($userFolders as $folder): if($folder['id']!=$folder_id): ?>
                
                <div><h6 data-bs-nid="0" style="cursor:pointer;" onclick="MoveToFolder(<?=$folder['id']; ?>)"><i class="fa fa-folder-o me-3"></i> <?=$folder['name']?></h6></div>
      
                <?php endif; endforeach; ?>
                
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  
                </div>
               
              </div>
            </div>
          </div>
         
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
      tinymce.init({
    selector: 'textarea.LetterApplication',
    height: 600,
    setup: function(editor) {
      editor.on('Change', function(e) {
        if (e.target.targetElm.classList.contains('boxitem')) {
          if (e.target.targetElm.dataset.box) {
            let box = e.target.targetElm.dataset.box;
            let body = this.getContent();
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
      });
    },
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table', 'help', 'wordcount', 'autoresize',
      'autosave', 'codesample', 'directionality', 'emoticons', 'importcss',
      'nonbreaking', 'pagebreak', 'quickbars', 'save', 'template', 'visualchars'
    ],

    toolbar: 'paste | undo redo | blocks | ' +
      'bold italic backcolor | alignleft aligncenter ' +
      'alignright alignjustify | bullist numlist outdent indent | ' +
      'removeformat | help' +
      'anchor | restoredraft | ' +
      'charmap | code | codesample | ' +
      'ltr rtl | emoticons | fullscreen | ' +
      'image | importcss | insertdatetime | ' +
      'link | numlist bullist | media | nonbreaking | ' +
      'pagebreak | preview | save | searchreplace | ' +
      'table tabledelete | tableprops tablerowprops tablecellprops | ' +
      'tableinsertrowbefore tableinsertrowafter tabledeleterow | ' +
      'tableinsertcolbefore tableinsertcolafter tabledeletecol | ' +
      'template | visualblocks | visualchars | wordcount | undo redo | ' +
      'blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
    paste_as_text: true,
  });
  function MoveToFolder(fid,nid){
    let move_note_id=document.getElementById("move_note_id").value; 
    $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'moveNotes',                   
                    folder_id: fid,
                    id:move_note_id,
                },
                success: function(json) {                   
                    const obj = JSON.parse(json);
                    console.log(obj);
                    if(obj.success){
                      if(folder_id!=obj.folder_id){
                        $("#nid-"+obj.id).remove();
                      }
                      var pFromCount=$("#folder-"+folder_id+" span").data('count');
                      var pToCount= $("#folder-"+obj.folder_id+" span").data('count');
                      pFromCount--;
                      pToCount++;
                      $("#folder-"+folder_id+" span").data('count',pFromCount);
                      $("#folder-"+obj.folder_id+" span").data('count',pToCount);
                      $("#folder-"+obj.folder_id+" span").text(pToCount);
                      $("#folder-"+folder_id+" span").text(pFromCount);
                      const modal = bootstrap.Modal.getInstance(foldersModal);
                      modal.hide();
                    }
                }
      });
          
  }
  let notesTable = document.getElementById("notesTable");
      const createNotesModal = document.getElementById('createNotesModal');
      const foldersModal = document.getElementById('foldersModal');
      
      const createNotesForm = document.getElementById('createNotesForm');


      function DeleteNotes(noteid=0){
        var note_id=document.getElementById('notes_id').value;
        var result = confirm("Sure you want to delete?");
        if (result) {
          $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'DeleteNotes',
                    id:note_id,
                },
                success: function(json) {
                  $('#nid-'+note_id).remove();
                  const modal = bootstrap.Modal.getInstance(createNotesModal);
                        modal.hide();
                }
            });
          
        }
        
      }
      foldersModal.addEventListener('show.bs.modal', event => {     
          //const button = event.relatedTarget
         // const nid = button.getAttribute('data-bs-id');
        var moveNoteIdInput = document.getElementById("move_note_id");   
        var note_id=document.getElementById('notes_id').value;      
        if(note_id!=null){
          moveNoteIdInput.value=note_id;
        }
     });
     
     createNotesModal.addEventListener('hide.bs.modal', event => {  
      console.log('hide.bs.modal');
      submitNotesForm();
     });
      createNotesModal.addEventListener('show.bs.modal', event => {     
        const button = event.relatedTarget;
        const nid = button.getAttribute('data-bs-id');
        console.log(nid);
       
        
      const modalTitle = createNotesModal.querySelector('.modal-title');
      const modalBodyInput = createNotesModal.querySelector('.modal-body textarea');
   
      const modalBodyId = createNotesModal.querySelector('.modal-body input#notes_id');
      
      if(nid!=null){
        //modalTitle.textContent='Edit Notes';
        modalBodyId.value=nid;
        const xhttp = new XMLHttpRequest();
        xhttp.onload = function() {
          console.log(this.responseText);
          var obj=JSON.parse(this.responseText);
          console.log(obj);
          if(obj.success){
            modalBodyInput.value=obj.note.notes;
            //tinyMCE.get('notes').setContent(obj.note.notes);
          }
          
        }
        xhttp.open("POST", SITE_URL + "/users/ajax/ajax.php?action=getNotes&id="+nid);
        xhttp.send();
          
          
      }else{
        //modalTitle.textContent='Create Notes';
        //tinyMCE.get('notes').setContent('');
        modalBodyInput.value="";
        //modalBodyNotesTitle.value="";
        modalBodyId.value=0;
      }
      //modalTitle.textContent = `New message to ${recipient}`
      //modalBodyInput.value = recipient
      })

      function submitNotesForm(){
        console.log('submitNotesForm');
        var notes_id=document.getElementById('notes_id').value;
        //var notes = tinyMCE.get('notes').getContent();
        var notes=document.getElementById('notes').value;
        console.log('notes_id',notes_id,notes);
        console.log(folder_name,folder_id) ;     
        $.ajax({
                url: SITE_URL + "/users/ajax/ajax.php",
                type: "POST",
                data: {
                    action: 'createNotes',
                    notes:notes,                   
                    folder_id: folder_id,
                    id:notes_id,
                },
                success: function(json) {
                   
                    const obj = JSON.parse(json);
                    console.log(obj);
                    if(obj.success){
                        if(obj.new){
                          let row = document.createElement("tr");  
                          const rowid = document.createAttribute("id");
                          rowid.value = "nid-"+obj.id;
                          row.setAttributeNode(rowid);
                          let c2 = document.createElement("td");
                          const nodebodyclass = document.createAttribute("class");
                          nodebodyclass.value = "notebody";
                          c2.setAttributeNode(nodebodyclass);
                          const databsId = document.createAttribute("data-bs-id");
                          databsId.value = obj.id;
                          const databsToggle = document.createAttribute("data-bs-toggle");
                          databsToggle.value = "modal";
                          const databsTarget = document.createAttribute("data-bs-target");
                          databsTarget.value = "#createNotesModal"
                          c2.setAttributeNode(databsId);
                          c2.setAttributeNode(databsToggle);
                          c2.setAttributeNode(databsTarget);
                         
                          c2.innerHTML = '<div class="content">'+obj.title+'</div><p class="date">'+obj.date+'</p>';
                         
                          row.appendChild(c2);
                          notesTable.tBodies[0].appendChild(row);
                         
                           
                        }else{
                          $("#nid-"+notes_id+" td.notebody .content").html(obj.title);

                        }
                        document.getElementById('folder_name').value="";
                        document.getElementById('folder_id').value=0;
                        const modal = bootstrap.Modal.getInstance(createNotesModal);
                        modal.hide();
                    }
                }
      });
      }
      createNotesForm.addEventListener('submit', event => {
        event.preventDefault();
        submitNotesForm();            
       return false;
        
    });
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
      
     
     
    </script>
<?php require_once "inc/footer.php"; ?>