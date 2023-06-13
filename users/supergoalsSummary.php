<?php
$selectedDate=isset($_GET['date'])? $_GET['date']:'';
$type = 'weekly';



if (isset($_REQUEST['type'])) {
  $type = trim($_REQUEST['type']);
}
$start_date='';
$end_date='';
if(!empty($selectedDate)){
   $selectedYear = date('Y', strtotime($selectedDate));
   $selectedMonth = date('m', strtotime($selectedDate));
   $selectedQuarter = floor(($selectedMonth - 1) / 3) + 1;

   if ($type == 'weekly') {  
      $currentWeekNumber = date('W', strtotime($selectedDate));
      $start_date = date('Y-m-d', strtotime('Last Monday', strtotime($selectedDate)));
      $end_date = date('Y-m-d', strtotime('Next Sunday', strtotime($selectedDate)));
    } elseif ($type == 'monthly') {       
      $start_date = date('Y-m-01', strtotime($selectedDate));
      $end_date = date('Y-m-t', strtotime($selectedDate));
    } elseif ($type == 'yearly') { 
      $start_date = $selectedYear . '-01-01';
      $end_date = $selectedYear . '-12-31';
    } elseif ($type == 'quarterly') {
      if ($selectedQuarter == 1) {
         $start_date = $selectedYear . '-01-01';
         $end_date = $selectedYear . '-03-31';
      } elseif ($selectedQuarter == 2) {
         $start_date = $selectedYear . '-04-01';
         $end_date = $selectedYear . '-06-30';
      } elseif ($selectedQuarter == 3) {
         $start_date = $selectedYear . '-07-01';
          $end_date = $selectedYear . '-09-30';
      }elseif ($selectedQuarter == 4) {
         $start_date = $selectedYear . '-10-01';
         $end_date = $selectedYear . '-12-31';
      }
   }
}




 if ($type == 'weekly') {
   $evaluation_heading = 'Semana: Evaluación y Progreso';   
 } elseif ($type == 'monthly') {   
   $evaluation_heading = 'Mes: Evaluación y Progreso';   
 } elseif ($type == 'yearly') { 
   $evaluation_heading = 'Año: Evaluación y Progreso';   
 } elseif ($type == 'quarterly') {
   $evaluation_heading = 'Trimestre: Evaluación y Progreso';
 }
 
require_once "inc/header.php"; ?>
<style>
   .list-text {
      text-decoration: none;
      color: white;
   }

   .list-text:hover {
      color: gainsboro;
   }
</style>
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white min-vh-100">
   <?php require_once 'inc/thirdNav.php'; ?>
   <div class="px-3-sm px-5-lg mt-4 mt-lg-5 pt-0 pt-lg-3">
      <div class="row">
         <div class="col-md-6">
         <div class="input-group mb-3 p-2">
  <input type="text" class="form-control" placeholder="write your tag here" id="search-tag" aria-label="write your tag here" aria-describedby="button-addon2">
  <button class="btn bg-primary" type="button" id="button-addon2"><i class="fa fa-search text-white" aria-hidden="true"></i></button>
</div>
         
         </div>
         <div class="col-md-6">
            <div class="input-group mb-3 date datepicker p-2" id="datepicker">
            <input type="text" class="form-control" placeholder="Elige Fecha" value="<?=empty($selectedDate)? '':date('d-m-Y', strtotime($selectedDate)); ?>" id="date" readonly aria-label="Elige Fecha" aria-describedby="button-addon2">
            <button class="btn bg-primary" type="button" id="button-addon2"><i class="fa fa-calendar text-white" aria-hidden="true"></i></button>
            </div>
        

         </div>
      </div>
      <div class="d-flex justify-content-between px-3">
         <!-- Search box -->
         
         <!--  -->
         
      </div>



      <div class="p-2-sm p-5-lg py-4">
         <h3 class="text-center"><?=$evaluation_heading;?></h3>
         <br>
         <div class="px-2 px-lg-3">
            <ul class="list-group p-1-sm p-3-lg item-container list-unstyled">
               <!-- Daily Victories -->
            </ul>
            <div class="observe-container" id="observe-container"></div>
         </div>
      </div>
   </div>
</main>


<script>
   let tag;
   let pageNumber = 0;
   let totalPage = 0;
   var type='<?=$type;?>';
   var selectedDate='<?=$selectedDate;?>';
   var startDate='<?=$start_date;?>';
   var endDate='<?=$end_date;?>';
   const itemContainer = document.querySelector(".item-container");
   $(function() {
      var calOptions = {
        format: 'dd-mm-yyyy',
        autoclose: true,
        calendarWeeks: true,
        todayHighlight: true,
        weekStart: 1,
        todayBtn:true,
        clearBtn:true
      };
     
      $('#datepicker').datepicker(calOptions).on('changeDate', function(e) {
         console.log('changeDate', e.date, e.format('yyyy-mm-dd'));
         if(e.date){
            window.location.href = SITE_URL + "/users/supergoalsSummary.php?type="+type+"&date=" + e.format('yyyy-mm-dd');
         }else{
            window.location.href = SITE_URL + "/users/supergoalsSummary.php?type="+type;
         }
        });
    
  });
   document.getElementById("search-tag").addEventListener("change", (e) => {
      tag = e.target.value;
      pageNumber = 1;
      itemContainer.innerHTML = ""
      loadMoreItems(pageNumber, tag)
   })

   function loadMoreItems(pageNumber, tag) {
      console.log(pageNumber, "46");
      const url = `<?= SITE_URL; ?>/users/ajax/ajax.php?get_supergoals_summary=get_supergoals_summary&startDate=${startDate}&endDate=${endDate}&date=${selectedDate}&type=${type}&page=${pageNumber}${tag? `&tag=${tag}`:""}`;
      fetch(url)
         .then(res => res.json())
         .then(data => {
            totalPage = data.data.total_page;
            const dailyVictories = data.data.evolutions;
            dailyVictories.forEach(dailyVictory => {
               if (dailyVictory.description) {
                  const classesToAdd = ['py-2', 'mb-2', 'border-bottom', 'border-1', 'border-light', 'border-opacity-25', 'quote-item']
                  const item = document.createElement("li")
                  item.innerHTML =
                     `<div>
                        <p class="text-muted date-font mt-2"> 
                           <small>${dailyVictory.local_date}</small>
                           
                           </P>
                        <div class="my-2">${dailyVictory.description}</div>                        
                     </div>`
                  item.classList.add(...classesToAdd)
                  itemContainer.appendChild(item)
               }
            })
         })
   }

   const callBack = (entries, observer) => {
      if (entries[0].isIntersecting && pageNumber <= totalPage) {
         console.log(totalPage);
         pageNumber += 1
         loadMoreItems(pageNumber, tag)
         observer.observe(document.querySelector('.observe-container'))
      } else {
         document.getElementById("observe-container").innerHTML = `<div class="text-center text-mited mt-4"><small>No more data to show</small></div>`
      }

   }
   const options = {
      rootmargin: "200px",
      threshold: 0.1,
   }
   const observer = new IntersectionObserver(callBack, options)

   function formatDate(date) {
      const year = date.getFullYear();
      const month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
      const day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
      return `${day}-${month}-${year}`;
   }

   function formatMonth(date) {
      const year = date.getFullYear();
      const month = date.getMonth() + 1;
      return `${month}-${year}`;
   }

   function formatday(date) {
      const day = date.getDate();
      return `${day}`;
   }



   // Call functions=================================
   observer.observe(document.querySelector('.observe-container'))
</script>

<?php require_once "inc/footer.php"; ?>