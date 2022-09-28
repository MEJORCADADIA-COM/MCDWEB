
<?php require_once "inc/header.php"; ?>
<?php 

 $letterid=isset($_REQUEST['id'])? (int) $_REQUEST['id']:0;

 $useridletter = Session::get('user_id');


$letterapp=[];
if(!empty($letterid)){
 
  $result = $common->select("`letterapplication`", "id='".$letterid."'");

  if($result){
    $letterapp = mysqli_fetch_assoc($result);
 
  }
}

?>

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
          height:auto;
        }
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 14px;
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
          height:auto;
        }
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 14px;
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
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 28px;
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
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 28px;
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
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 28px;
        }
      }
    </style>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 maincontonent" style="margin-top: 0rem!important;margin-bottom: 0rem!important;">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
                <div style="background-color: #fef200;padding: 15px">
                    <h1 class="maintitle">Como seria tu Vida ldeal, que harias, donde vivirias ?</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
                <div style="background-color: #ed008c;padding: 15px;">
                    <div class="projects mb-4" style="background-color: #ed008c; border: 1px solid #ed008c;">
                        <div class="projects-inner" style="width:99%">
                            <div style="display: none;" id="show">
                                <div style="padding: 15px; border-radius: 7px; margin-bottom: 15px;display: flex; align-content: center; justify-content: space-between;align-items: center;" id="error_success_msg_verification" class="msg">
                                <p id="success_msg_verification_text" style="font-size: 14px; font-weight: 600;"></p><button style="border: 0px; background: transparent; font-size: 18px; font-weight: 800;align-items: center;" id="close">x</button>  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Fecha</label>
                              <input class="form-control" type="date" id="date" name="date" placeholder="Enter Fecha" value="<?php if(!empty($letterapp) && !empty($letterapp['date'])) echo $letterapp['date']; ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">De</label>
                              <input class="form-control" type="email" id="email" name="email" placeholder="De Parte de" value="<?php if(!empty($letterapp) && !empty($letterapp['email'])) echo $letterapp['email']; ?>">
                            </div>
                          </div>
                          <br />
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Para</label>
                              <input class="form-control" type="email" id="emailto" name="emailto" placeholder="Escribe destinatario" value="<?php if(!empty($letterapp) && !empty($letterapp['emailto'])) echo $letterapp['emailto']; ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Título de la Carta</label>
                              <input class="form-control" type="text" id="Title" name="Title" placeholder="Escribe título de tu Carta" value="<?php if(!empty($letterapp) && !empty($letterapp['title'])) echo $letterapp['title']; ?>">
                            </div>
                          </div>
                        </div>
                        <br />
                        <div>
                            <textarea id="LetterApplication" name="LetterApplication">

                            <?php if(!empty($letterapp) && !empty($letterapp['letterapplicationtext'])) echo $letterapp['letterapplicationtext']; ?>
                            </textarea>
                        </div>
                        <div>
                          
                            <input type="hidden" id="letter_id" value="<?=$letterid; ?>">
                            <input class="btn btn-info letter" type="button" id="emailsend" name="emailsend" value="Enviar" />
                            <input class="btn btn-info letter" type="button" id="onlysendcheck" name="onlysendcheck" value="Guardar" />
                        </div>
                        </div>
                    </div>
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
          $('#error_success_msg_verification').css('background-color','#ffdddd');
          $('#success_msg_verification_text').html('');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
          if($('#email').val() != '') {
            $('#show').css('display','none');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ffdddd');
            $('#success_msg_verification_text').html('');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
            if($('#emailto').val() != '') {
              $('#show').css('display','none');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
              if($('#Title').val() != '') {
                $('#show').css('display','none');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ffdddd');
                $('#success_msg_verification_text').html('');
                setTimeout(() => {
                  $('#show').css('display','none');
                }, 3000);
                if(tinyMCE.get('LetterApplication').getContent() != '') {
                  $('#show').css('display','none');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ffdddd');
                  $('#success_msg_verification_text').html('');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                  
                  var letter_id = $('#letter_id').val();
                  var email = $('#email').val();
                  var emailto = $('#emailto').val();
                  var Date = $('#date').val();
                  var Title = $('#Title').val();
                  var LetterApplication = tinyMCE.get('LetterApplication').getContent();
                  $.ajax({
                    url: SITE_URL+"/users/ajax/ajax.php",
                    type: "POST",
                    data: {
                      EmailSendCheck: 'EmailSendCheck',
                      email: email,
                      emailto: emailto,
                      Title:Title,
                      Date:Date,
                      LetterApplication: LetterApplication,
                      id:letter_id,
                    },
                    success: function (data) {
                      if(data == 'Insert') {
                        $('#email').val('');
                        $('#emailto').val('');
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
              $('#show').css('display','block');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('Fill Field Email To');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
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
          $('#error_success_msg_verification').css('background-color','#ffdddd');
          $('#success_msg_verification_text').html('');
          setTimeout(() => {
            $('#show').css('display','none');
          }, 3000);
          if($('#email').val() != '') {
            $('#show').css('display','none');
            $('#error_success_msg_verification').css('color','#000000');
            $('#error_success_msg_verification').css('background-color','#ffdddd');
            $('#success_msg_verification_text').html('');
            setTimeout(() => {
              $('#show').css('display','none');
            }, 3000);
            if($('#emailto').val() != '') {
              $('#show').css('display','none');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
              if($('#Title').val() != '') {
                $('#show').css('display','none');
                $('#error_success_msg_verification').css('color','#000000');
                $('#error_success_msg_verification').css('background-color','#ffdddd');
                $('#success_msg_verification_text').html('');
                setTimeout(() => {
                  $('#show').css('display','none');
                }, 3000);
                if(tinyMCE.get('LetterApplication').getContent() != '') {
                  $('#show').css('display','none');
                  $('#error_success_msg_verification').css('color','#000000');
                  $('#error_success_msg_verification').css('background-color','#ffdddd');
                  $('#success_msg_verification_text').html('');
                  setTimeout(() => {
                    $('#show').css('display','none');
                  }, 3000);
                  
                  var letter_id = $('#letter_id').val();
                  var email = $('#email').val();
                  var emailto = $('#emailto').val();
                  var Date = $('#date').val();
                  var Title = $('#Title').val();
                  var LetterApplication = tinyMCE.get('LetterApplication').getContent();
                  $.ajax({
                    url: SITE_URL+"/users/ajax/ajax.php",
                    type: "POST",
                    data: {
                      EmailSendCheckOnlySend: 'EmailSendCheckOnlySend',
                      email: email,
                      emailto: emailto,
                      Title:Title,
                      Date:Date,
                      LetterApplication: LetterApplication,
                      id:letter_id
                    },
                    success: function (data) {
                      
                      if(data == 'Insert' || data == 'Update') {
                        $('#email').val('');
                        $('#emailto').val('');
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
              $('#show').css('display','block');
              $('#error_success_msg_verification').css('color','#000000');
              $('#error_success_msg_verification').css('background-color','#ffdddd');
              $('#success_msg_verification_text').html('Fill Field Email To');
              setTimeout(() => {
                $('#show').css('display','none');
              }, 3000);
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