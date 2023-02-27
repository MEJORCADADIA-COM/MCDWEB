<?php require_once "inc/header.php"; ?>
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
        
      }
      @media screen and (min-width: 600px) {
        .tox-notifications-container {
            display: none !important;
        }
        .letter {
            float: right;
            margin: 15px 10px 15px 10px;
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
        
      }
      @media screen and (min-width: 992px) {
        .tox-notifications-container {
            display: none !important;
        }
        .letter {
            float: right;
            margin: 15px 10px 15px 10px;
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
       
      }
      table.table{background:#273142; border-color:grey; padding:0; margin:0;}
      table th{color:#FFF; background-color: #313d4f; border-color:grey; padding:1rem !important; }
      table td{color:#738297; border-color:grey; padding:1rem 0.5rem !important;}

    </style>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 ">
      <div class="projects mb-4">
        <div class="projects-inner" style="min-height:100vh;">
          <!-- <header class="projects-header">
            <div class="title">Notebook View</div>
            <i class="zmdi zmdi-download"></i>
          </header> -->
          <div style="display: none;" id="show">
            <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
              <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>  
            </div>
          </div>
          <?php
          $useridletter = Session::get('user_id');
          $letters=[];
          $result = $common->get(table: "letterapplication", cond: 'UserId = :user_id', params: ['user_id' => $useridletter], orderBy: 'id',order: 'DESC');
          if($result) {
            foreach($result as $row) {
              $letters[]=$row;
            }
          }
            ?>
          <table class="table">
  <thead>
    <tr>
      <th scope="col" width="120">Date</th>
      <th scope="col">Title</th>
      <th scope="col" class="text-center">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($letters as $key=>$item) : ?>
      <tr onclick="window.location.href='<?=SITE_URL; ?>/users/notebook.php?id=<?= $item['id']; ?>'">
      
      <td style="color:#FFF;"><?=date('d-m-Y',strtotime($item['date'])); ?></td>
      <td><p style="white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis; color:#FFF;"><?=strlen($item['title']) > 100 ? substr($item['title'],0,100)."..." : $item['title'];  ?></p></td>
      <td class="text-center" style="width:100px;">
      <a id="Edit" href="<?=SITE_URL; ?>/users/notebook.php?id=<?= $item['id']; ?>"  class="btn btn-info btn-sm btn-inline" ><i class="fa fa-pencil"></i></a>
      <a id="Delete" onclick="DeleteOnClick(<?= $item['id']; ?>)" class="btn btn-danger btn-sm btn-inline" ><i class="fa fa-trash-o" aria-hidden="true"></i></a>
             
      </td>
    </tr>
    <?php endforeach; ?>
   
    
  </tbody>
</table>
         
        </div>
      </div>
    </main>
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
      
      function SaveOnClick(id) {
        var LetterApplication = tinyMCE.get('LetterApplication'+id).getContent()
        $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
                SaveIdCheck: 'SaveIdCheck',
                id:id,
                LetterApplication:LetterApplication,
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
                    window.location.reload();
                }
                else {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ffdddd');
                    $('#success_msg_verification_text').html(data);
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                }
            }
        });
      }

      function EditOnClick(id) {
        var LetterApplication = tinyMCE.get('LetterApplication'+id).getContent()
        $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
                EmailIdCheck: 'EmailIdCheck',
                id:id,
                LetterApplication:LetterApplication,
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
                    window.location.reload();
                }
                else {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ffdddd');
                    $('#success_msg_verification_text').html(data);
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                }
            }
        });
      }

      function DeleteOnClick(id) {
        $.ajax({
            url: SITE_URL+"/users/ajax/ajax.php",
            type: "POST",
            data: {
                EmailDeleteCheck: 'EmailDeleteCheck',
                id:id,
            },
            success: function (data) {
                if(data == 'Delete') {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ddffff');
                    $('#success_msg_verification_text').html('Update Successfully');
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                    window.location.reload();
                }
                else {
                    $('#show').css('display','block');
                    $('#error_success_msg_verification').css('color','#000000');
                    $('#error_success_msg_verification').css('background-color','#ffdddd');
                    $('#success_msg_verification_text').html(data);
                    setTimeout(() => {
                        $('#show').css('display','none');
                    }, 3000);
                }
            }
        });
      }
    </script>
<?php require_once "inc/footer.php"; ?>