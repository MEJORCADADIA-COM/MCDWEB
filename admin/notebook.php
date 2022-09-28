<?php require_once "inc/header.php"; ?>
<?php
if(isset($_GET['block'])) {
  $block_id = $_GET['block'];
  $block_update = $common->update("`users`", "`status` = '0'", "`id` = '$block_id'");
  if($block_update) {
    header("Location: https://mejorcadadia.com/admin/index.php");
  }
} elseif(isset($_GET['unblock'])) {
  $unblock_id = $_GET['unblock'];
  $unblock_update = $common->update("`users`", "`status` = '1'", "`id` = '$unblock_id'");
  if($unblock_update) {
    header("Location: https://mejorcadadia.com/admin/index.php");
  }
} elseif(isset($_GET['delete'])) {
  $delete_id = $_GET['delete'];
  $user_delete = $common->delete("`users`", "`id` = '$delete_id'");
  if($user_delete) {
    header("Location: https://mejorcadadia.com/admin/index.php");
  }
}
?>
<style>
  .tox-notifications-container {
    display: none !important;
  }
  .letter {
    float: right;
    margin: 15px 10px 15px 10px;
  }
</style>
    <script src="https://mejorcadadia.com/admin/assets/jquery-3.6.0.min.js"></script>
    <script src="https://mejorcadadia.com/admin/assets/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://mejorcadadia.com/admin/assets/tinymce-jquery.min.js"></script>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3">
      <div class="projects mb-4" style="padding-left: 16px; padding-right: 16px;">
        <div class="projects-inner">
          <!-- <header class="projects-header" style="padding-left: 0px;">
            <div class="title">NoteBook</div>
            <i class="zmdi zmdi-download"></i>
          </header> -->
          <div style="display: none;" id="show">
            <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
              <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>  
            </div>
          </div>
          <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Fecha</label>
                <input class="form-control" type="date" id="date" name="date" placeholder="Enter Fecha">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">De</label>
                <input class="form-control" type="email" id="email" name="email" placeholder="De Parte de">
              </div>
            </div>
            <br />
            <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-6">
                <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Para</label>
                <input class="form-control" type="email" id="emailto" name="emailto" placeholder="Escribe destinatario">
              </div>
              <div class="col-lg-6 col-md-6 col-sm-6">
                <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Título de la Carta</label>
                <input class="form-control" type="text" id="Title" name="Title" placeholder="Escribe título de tu Carta">
              </div>
            </div>
          </div>
          <br />
          <div>
            <textarea id="LetterApplication" name="LetterApplication"></textarea>
          </div>
          <div>
            <input class="btn btn-info letter" type="button" id="emailsend" name="emailsend" value="Guardar" />
            <input class="btn btn-info letter" type="button" id="onlysendcheck" name="onlysendcheck" value="Enviar" />
          </div>
        </div>
      </div>
    </main>
    <script>
      $('#show').css('display','none');
      tinymce.init({
        selector: 'textarea#LetterApplication',
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

      $('#close').click(function() {
        $('#show').css('display','none');
        $('#success_msg_verification_text').html('');
      })

      $('#emailsend').click(function () {
        if($('#date').val() != '') {
          $('#show').css('display','none');
          $('#error_success_msg_verification').css('color','#000000');
          $('#error_success_msg_verification').css('background-color','#ddffff');
          $('#success_msg_verification_text').html('');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
          if($('#email').val() != '') {
            $('#show').css('display','none');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ddffff');
            $('#success_msg_verification_text').html('');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
            if($('#emailto').val() != '') {
              $('#show').css('display','none');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ddffff');
              $('#success_msg_verification_text').html('');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
              if($('#Title').val() != '') {
                $('#show').css('display','none');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ddffff');
                $('#success_msg_verification_text').html('');
                setTimeout(() => {
                  $('#show').css('display','none');
                }, 3000);
                if(tinyMCE.get('LetterApplication').getContent() != '') {
                  $('#show').css('display','none');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ddffff');
                  $('#success_msg_verification_text').html('');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                  var email = $('#email').val();
                  var emailto = $('#emailto').val();
                  var Date = $('#date').val();
                  var Title = $('#Title').val();
                  var LetterApplication = tinyMCE.get('LetterApplication').getContent();
                  $.ajax({
                    url: "https://mejorcadadia.com/admin/ajax/ajax.php",
                    type: "POST",
                    data: {
                      EmailSendCheck: 'EmailSendCheck',
                      email: email,
                      emailto:emailto,
                      Title:Title,
                      Date:Date,
                      LetterApplication: LetterApplication,
                    },
                    success: function (data) {
                      if(data == 'Insert') {
                        $('#email').val('');
                        $('#date').val('');
                        $('#Title').val('');
                        $('#show').css('display','block');
                        $('#error_success_msg_verification').css('color','#000000');
                        $('#error_success_msg_verification').css('background-color','#ddffff');
                        $('#success_msg_verification_text').html('Successfully '+data);
                        setTimeout(() => {
                          $('#show').css('display','none');
                        }, 3000);
                        tinyMCE.get('LetterApplication').setContent('');
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
                else {
                  $('#show').css('display','block');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ffdddd');
                  $('#success_msg_verification_text').html('Fill Field Text');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                }
              }
              else {
                $('#show').css('display','block');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ffdddd');
                $('#success_msg_verification_text').html('Fill Field Title');
                setTimeout(() => {
                    $('#show').css('display','none');
                }, 3000);
              }
            }
            else {
              $('#show').css('display','noneblock');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('Fill Field Email To');
            }
          }
          else {
            $('#show').css('display','block');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ffdddd');
            $('#success_msg_verification_text').html('Fill Field Email From');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
          }
        }
        else {
          $('#show').css('display','block');
          $('#error_success_msg_verification').css('color','#000000');
          $('#error_success_msg_verification').css('background-color','#ffdddd');
          $('#success_msg_verification_text').html('Fill Field Date');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
        }
      });

      $('#onlysendcheck').click(function () {
        if($('#date').val() != '') {
          $('#show').css('display','none');
          $('#error_success_msg_verification').css('color','#000000');
          $('#error_success_msg_verification').css('background-color','#ddffff');
          $('#success_msg_verification_text').html('');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
          if($('#email').val() != '') {
            $('#show').css('display','none');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ddffff');
            $('#success_msg_verification_text').html('');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
            if($('#emailto').val() != '') {
              $('#show').css('display','none');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ddffff');
              $('#success_msg_verification_text').html('');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
              if($('#Title').val() != '') {
                $('#show').css('display','none');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ddffff');
                $('#success_msg_verification_text').html('');
                setTimeout(() => {
                  $('#show').css('display','none');
                }, 3000);
                if(tinyMCE.get('LetterApplication').getContent() != '') {
                  $('#show').css('display','none');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ddffff');
                  $('#success_msg_verification_text').html('');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                  var email = $('#email').val();
                  var emailto = $('#emailto').val();
                  var Date = $('#date').val();
                  var Title = $('#Title').val();
                  var LetterApplication = tinyMCE.get('LetterApplication').getContent();
                  $.ajax({
                    url: "https://mejorcadadia.com/admin/ajax/ajax.php",
                    type: "POST",
                    data: {
                      EmailSendCheckOnlySend: 'EmailSendCheckOnlySend',
                      email: email,
                      emailto:emailto,
                      Title:Title,
                      Date:Date,
                      LetterApplication: LetterApplication,
                    },
                    success: function (data) {
                      if(data == 'Insert') {
                        $('#email').val('');
                        $('#date').val('');
                        $('#Title').val('');
                        $('#show').css('display','block');
                        $('#error_success_msg_verification').css('color','#000000');
                        $('#error_success_msg_verification').css('background-color','#ddffff');
                        $('#success_msg_verification_text').html('Successfully '+data);
                        setTimeout(() => {
                          $('#show').css('display','none');
                        }, 3000);
                        tinyMCE.get('LetterApplication').setContent('');
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
                else {
                  $('#show').css('display','block');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ffdddd');
                  $('#success_msg_verification_text').html('Fill Field Text');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                }
              }
              else {
                $('#show').css('display','block');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ffdddd');
                $('#success_msg_verification_text').html('Fill Field Title');
                setTimeout(() => {
                    $('#show').css('display','none');
                }, 3000);
              }
            }
            else {
              $('#show').css('display','noneblock');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('Fill Field Email To');
            }
          }
          else {
            $('#show').css('display','block');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ffdddd');
            $('#success_msg_verification_text').html('Fill Field Email From');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
          }
        }
        else {
          $('#show').css('display','block');
          $('#error_success_msg_verification').css('color','#000000');
          $('#error_success_msg_verification').css('background-color','#ffdddd');
          $('#success_msg_verification_text').html('Fill Field Date');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
        }
      });
    </script>
<?php require_once "inc/footer.php"; ?>
