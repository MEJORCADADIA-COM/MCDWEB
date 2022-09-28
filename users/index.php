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

    </style>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 maincontonent">
      <div class="projects mb-4">
        <div class="projects-inner">
          <!-- <header class="projects-header">
            <div class="title">Notebook View</div>
            <i class="zmdi zmdi-download"></i>
          </header> -->
          <div style="display: none;" id="show">
            <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
              <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>  
            </div>
          </div>
          <table class="projects-table">
            <thead>
              <tr>
                <!-- <th>id</th>
                <th>From</th>
                <th>To</th> -->
                <th>Date</th>
                <th>Title</th>
                <!--<th>Letter Application</th>-->
                <th>UserName</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <?php
            $useridletter = Session::get('user_id');
            $all_letterapplication = $common->select("`letterapplication`", "`UserId` = '$useridletter'");
            if($all_letterapplication) {
              while($all_letterapp = mysqli_fetch_assoc($all_letterapplication)) {
                $useridcheck = Session::get('user_id');
                $letterusercheck = $common->selectcolumn("COUNT(id) AS id","`users`","id = ".$useridcheck."");
                $letteruser_appcheck = mysqli_fetch_assoc($letterusercheck);
                if($letteruser_appcheck['id'] >= 1) {
                  $letteruser = $common->select("`users`","id = ".$useridcheck."");
                  $letteruser_app = mysqli_fetch_assoc($letteruser);
                  $letteruser_appview = $letteruser_app['full_name'];
                } 
                else {
                  $letteruser_appview= 'Unknown';
                }
            ?>
            <tr onclick="window.location.href='<?=SITE_URL; ?>/users/notebook.php?id=<?= $all_letterapp['id']; ?>'">
              <!-- <td>
                <p><?php //echo $all_letterapp['id']; ?></p>
              </td>
              <td>
                <p><?php //echo $all_letterapp['email']; ?></p>
              </td>
              <td>
                <p><?php //echo $all_letterapp['emailto']; ?></p>
              </td> -->
              <td>
                <p><?= $all_letterapp['date']; ?></p>
              </td>
              <td>
                <p><?= $all_letterapp['title']; ?></p>
              </td>
              <!--<td>
                <textarea class="LetterApplication" id="LetterApplication<?= $all_letterapp['id']; ?>" name="LetterApplication"><?= $all_letterapp['letterapplicationtext']; ?></textarea>
              </td>-->
              <td>
                  <p><?= $letteruser_appview; ?></p>
              </td>
              <td class="text-center">
               
                <a id="Edit" href="<?=SITE_URL; ?>/users/notebook.php?id=<?= $all_letterapp['id']; ?>"  class="btn btn-info">Edit</a>
                <a id="Delete" onclick="DeleteOnClick(<?= $all_letterapp['id']; ?>)" class="btn btn-danger ms-2">Delete</a>
              </td>
            </tr>
            <?php
              }
            } else {

            }
            ?>
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