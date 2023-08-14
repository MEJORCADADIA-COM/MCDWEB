<?php
/*Just for your server-side code*/
// header('Content-Type: text/html; charset=ISO-8859-1');
//$preHead='<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.16.19/dist/css/uikit.min.css" />';
?>
<?php  require_once "inc/header.php"; ?>

<?php
$user_id = Session::get('user_id');


$content='';
$supermemories = $common->first('supermemories', 'user_id = :user_id', ['user_id' => $user_id]);

$supermemoriesGoals = $common->get('supermemories_goals', "user_id = :user_id", ['user_id' => $user_id]);

if(!empty($supermemories)){  
  $content=$supermemories['content'];
}

?>
<script>
  var SITE_URL = '<?= SITE_URL; ?>'; 
</script>

<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
<style>
  .edit-actions {
    display: none;
  }

  .edit-actions i {
    color: #fef200;
  }

  .goals-area.edit .edit-actions {
    display: inline-block;
  }

  .has-errors input {
    border-color: #F00;
  }
  .goal-list textarea {
    width: 100%;
  }
.goals-area ol li {
    font-size: 1rem;
    color: #FFF;
    margin-bottom: 10px;
    position: relative;
}
.goals-area ol li label {
    display: inline;
}
                        
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3">

  <?php require_once 'inc/secondaryNav.php'; ?>

  <div class="projects my-5" style="background-color: #ed008c;">
    <div class="projects-inner">
      <header class="projects-header">
      
        
        <?php setlocale(LC_ALL, "es_ES");
        $string = date('d/m/Y', strtotime($currentDate));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        ?>
        <div class="row">
          <div class="col-sm-2 col-2" style="text-align:left;"></div>
          <div class="col-sm-8 col-8" style="text-align:center;">
            <h2 style="text-transform: capitalize;">SuperMemorias</h2>
          </div>
          <div class="col-sm-2 col-2" style="text-align:right;"></div>
        </div>


      </header>
    
       <div class="mt-5" style="background-color: #fef200; padding: 10px">
              <h3 class="maintitle" style="padding:0; margin:0; width:100%; overflow:hidden; ">Escribe Eventos en tu Vida que quieras recorday y Expandir
              <button type="button" class="btn btn-info btn-sm screenonly pull-right" id="editBtn1">Editar</button>
            </h3>
      </div>
      <div class="cardd mb-4" id="section-1">

        <div class="goals-area" id="top-goals-area" style="display:block; ">
          <ol id="daily-top-goal-list" class="goal-list">
            <?php foreach ($supermemoriesGoals as $key => $item) :  ?>
              <li class="" id="top-goal-list-item-<?= $item['id']; ?>" style="font-size: 1rem;">
                <label id="top-list-label-<?= $item['id']; ?>">

                  <span style="font-size: 1rem;" id="topGoalText-<?= $item['id']; ?>"><?= $item['goal']; ?> </span>
                  
                  <a class="edit-actions edit-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-pencil"></i></a>
                  <a class="edit-actions delete-goal-btn" data-type="top" data-id="<?= $item['id']; ?>" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                </label>
              </li>
            <?php endforeach; ?>
          </ol>

          <div class="form-group" id="new-top-goal-creation-container"></div>
          
            <div class="form-group screenonly" style="padding:20px; text-align:right;" id="create-top-goal-btn-wrapper">

              <button type="button" id="save-new-top-goals-btn" style="display:none;" class="button btn btn-info" onClick="SaveNewTopGoals()"><i class="fa fa-save"></i> Guarda Nuevo Objetivo</button>

              <button type="button" class="button btn btn-info" onClick="CreateDailyTopGoal()"><i class="fa fa-book"></i> Agrega SuperMemoria</button>

            </div>
          
        </div>
      </div>
      
      <div class="cardd mb-5" id="section-2" style="padding:0 5px;">
            <div class="d-flex justify-content-between my-1"><h5 class="card-header" style="color:#FFF;  margin:5px 0; font-size: 1rem;">Expande tu Memoria con Eventos Extraordinarios</h5>
              </div>
              <div class="card-body">
                <div class="form-group">
                  <div class="description-area">
                    <textarea id="content" rows="5" class="LetterApplication editor ckeditor" name="content"><?= $content; ?></textarea>
                  </div>
                </div>
              </div>
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
<script> 
  document.querySelectorAll( '.LetterApplication' ).forEach( ( node, index ) => {  
	ClassicEditor
		.create( node, {} )
		.then( newEditor => {
      newEditor.model.document.on( 'change:data', (e) => {
       
        let body = newEditor.getData();
        console.log('change:data',body);
            $.ajax({
              url: SITE_URL + "/users/ajax/ajax.php",
              type: "POST",
              data: {
                action: 'SaveSuperMemoriesBox',
                body: body
              },
              success: function(data) {
                var jsonObj = JSON.parse(data);
                console.log('data', data, jsonObj);

              }
            });
      });
      if(node.id){
        window.editors[ node.id ] = newEditor;
      }else{
        window.editors[ index ] = newEditor	;
      }
			
		} );
} );
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
$('#editBtn1').click(function(e) {
    if ($(this).text() == 'Editar') {
      $(this).text('Cancelar');
    } else {
      $(this).text('Editar');
    }
    $('#top-goals-area').toggleClass('edit');
  });
function hasFilledNewGoals(classname) {
    var filled = true;
    newgoalsInput = [];
    $newgoalsinputEmpty = document.querySelectorAll("textarea." + classname);
    for (var i = 0; i < $newgoalsinputEmpty.length; ++i) {
      if ($newgoalsinputEmpty[i].value == '') {
        filled = false;
        $newgoalsinputEmpty[i].classList.add('is-invalid');
      } else {
        $newgoalsinputEmpty[i].classList.remove('is-invalid');
        newgoalsInput.push($newgoalsinputEmpty[i].value);
      }
    }
    return filled;
  }
function CreateDailyTopGoal() {
    $wrapper = $('#new-top-goal-creation-container');
    var validated = hasFilledNewGoals('newtopgoals');
    console.log('validated', validated);
    $newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    if (validated) {
      $wrapper.append("<div class='form-group'><textarea placeholder='Write goal details' class='form-input form-control newtopgoals' name='newtopgoals[]'/></textarea></div>");
      
      $('#save-new-top-goals-btn').show();

    } else {
      showToast('error', 'Please fill the the box.');
    }
  }
  function SaveNewTopGoals() {
    var newgoalsinput = document.querySelectorAll("textarea.newtopgoals");
    var validated = hasFilledNewGoals('newtopgoals');
    if (newgoalsInput.length > 0) {

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          SaveNewSuperMemoriesGoals: 'SaveNewSuperMemoriesGoals',
          goals: newgoalsInput
        },
        success: function(data) {
          var jsonObj = JSON.parse(data);
          console.log('data', data, jsonObj);
          if (jsonObj.success) {
            goalstobeadded = 0;
            newgoalsInput = [];
            $('#new-top-goal-creation-container').html('');
            for (const prop in jsonObj.goals) {
              console.log(`obj.${prop} = ${jsonObj.goals[prop]}`);
              console.log(prop, jsonObj.goals[prop]);


              $("#daily-top-goal-list").append('<li class="" id="top-goal-list-item-' + prop + '"><label class="form-label" id="top-list-label-' + prop + '"><span id="topGoalText-' + prop + '">' + jsonObj.goals[prop] + '</span> <a class="edit-actions edit-goal-btn" data-type="top" data-id="' + prop + '" href="#"><i class="fa fa-pencil"></i></a>                 <a class="edit-actions delete-goal-btn" data-id="' + prop + '" href="#"><i class="fa fa-trash-o" aria-hidden="true"></i></a></label></li>');
            }
            $('#save-new-top-goals-btn').hide();
          }
        }
      });
    }
  }
  $(document).on('click', '.edit-goal-btn', function(e) {
    e.preventDefault();
    var sectionType = $(this).data('type');
    var goalId = $(this).data('id');
    console.log('goalId', goalId, sectionType);
  
    var goalTextElem = $('#topGoalText-' + goalId);
   
    $(this).addClass(sectionType);
    goalText = goalTextElem.text();
    console.log('goalText', goalText);
    if ($(this).find('.fa').hasClass('fa-pencil')) {
      $(this).find('.fa').removeClass('fa-pencil');
      $(this).find('.fa').addClass('fa-save');
      $(this).addClass('save');
      goalTextElem.hide();
      var containterItemId = sectionType + '-list-label-' + goalId;
      $("#" + containterItemId).append('<textarea id="edittextarea-' + sectionType + goalId + '">' + goalText + '</textarea>');
    } else {
      $(this).removeClass('save');
      
      $(this).find('.fa').addClass('fa-pencil');
      $(this).find('.fa').removeClass('fa-save');
      goalTextElem.show();
      var textareaElem = $('#edittextarea-' + sectionType + goalId);
      var goalText = textareaElem.val();
      goalTextElem.text(goalText);
      textareaElem.remove();
     

      $.ajax({
        url: SITE_URL + "/users/ajax/ajax.php",
        type: "POST",
        data: {
          action: 'UpdateSuperMemoryGoal',
          goalText: goalText,
          goalId: goalId,
          edit: 1,
        },
        success: function(data) {
          console.log('data', data);
          if (data == 'Update') {
            $('#show').css('display', 'block');
            $('#error_success_msg_verification').css('color', '#000000');
            $('#error_success_msg_verification').css('background-color', '#ddffff');
            $('#success_msg_verification_text').html('Update Successfully');
            setTimeout(() => {
              $('#show').css('display', 'none');
            }, 3000);

          }
        }
      });
    }

  });
</script>

<?php require_once "inc/footer.php"; ?>