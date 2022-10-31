
<?php require_once "inc/header.php"; ?>
<?php 
//error_reporting(E_ALL);
 $letterid=isset($_REQUEST['id'])? (int) $_REQUEST['id']:0;
 $useridletter=$UserId=$user_id = Session::get('user_id');
 
 

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
        
        
        .maintitle {
          color: #db3e49; 
          text-align: center;
          margin: 0px; 
          font-size: 28px;
        }
      }
      .projects{
        overflow:hidden;
      }
    </style>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 " style="margin-top: 0rem!important;margin-bottom: 0rem!important;">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
                <div style="background-color: #fef200;padding: 15px">
                    <h1 class="maintitle">Como seria tu Vida ldeal, que harias, donde vivirias ?</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12" style="padding: 0px;">
            <form method="post" action="<?=SITE_URL;?>/users/ajax/ajax.php?id=<?=$letterid;?>">
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
                              <div class="input-group date daily-datepicker datepicker" id="datepicker">
            
                            <input type="text" class="form-control" id="date" name="date"  required placeholder="Enter Fecha" value="<?php if(!empty($letterapp) && !empty($letterapp['date'])) echo date('d-m-Y',strtotime($letterapp['date'])); ?>" id="date" readonly/>
                              <span class="input-group-append">
                                <span class="input-group-text bg-light d-block">
                                  <i class="fa fa-calendar"></i>
                                </span>
                              </span>
                            </div>
                              
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">De</label>
                              <input class="form-control" required type="email" id="email" name="email" placeholder="De Parte de" value="<?php if(!empty($letterapp) && !empty($letterapp['email'])) echo $letterapp['email']; ?>">
                            </div>
                          </div>
                          <br />
                          <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Para</label>
                              <input class="form-control" required type="email" id="emailto" name="emailto" placeholder="Escribe destinatario" value="<?php if(!empty($letterapp) && !empty($letterapp['emailto'])) echo $letterapp['emailto']; ?>">
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                              <label style="color: #ffffff;font-size: 20px; float: left;" class="form-label">Título de la Carta</label>
                              <input class="form-control" required type="text" id="Title" name="Title" placeholder="Escribe título de tu Carta" value="<?php if(!empty($letterapp) && !empty($letterapp['title'])) echo $letterapp['title']; ?>">
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
                          <div class="form-group screenonly">
                          <input type="hidden" id="letter_id" value="<?=$letterid; ?>">
                            <input class="btn btn-info letter" type="button" id="emailsend" name="emailsend" value="Enviar" />
                            <input class="btn btn-info letter" type="button" id="savePrintBtn" name="savePrintBtn" value="Guardar pdf" />
                            <input class="btn btn-info letter" type="submit" id="onlysendcheck" name="saveDinstyLetter" value="Guardar" />
                       
                          </div>
                          
                           </div>
                    </div>
                  </div>

                </div>
    </form>
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
                    success: function (json) {
                      const data = JSON.parse(json);
                      if(data.success) {
                        $('#letter_id').val(data.letterId);
                       if(data.new==true){
                        window.location.href='<?=SITE_URL;?>/users/notebook.php?id='+data.letterId;
                       }
                        $('#show').css('display','block');
                        $('#error_success_msg_verification').css('color','#000000');
                        $('#error_success_msg_verification').css('background-color','#ddffff');
                        $('#success_msg_verification_text').html('Successfully '+data);
                        setTimeout(() => {
                          $('#show').css('display','none');
                        }, 3000);
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

      $('#savePrintBtn').click(function () { 
       // var dailyEvolution = tinyMCE.get('dailyEvolution').getContent(); 
        //$("#print-evaluation").html(dailyEvolution); 
        window.print();       
      });
      $(function() {
        $('.datepicker').datepicker({
                format:'dd-mm-yyyy',
                autoclose:true,
                todayHighlight:true,
                weekStart:1
                });
      });

    </script>
<?php require_once "inc/footer.php"; ?>