<?php $selectedDate=isset($_GET['date'])? $_GET['date']:'';
require_once "inc/header.php"; ?>
<style>
   .list-text {
      text-decoration: none;
      color: white;
   }

   .list-text:hover {
      color: gainsboro;
   }
   .date-font small{
      font-size:1.2em;
      text-transform: capitalize;
      color:#FFF;
   }
</style>
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>
<main role="main" class="col-md-9 ml-sm-auto col-lg-10 mb-3 text-white min-vh-100">
   <?php require_once 'inc/thirdNav.php'; ?>
   <div class="px-3-sm px-5-lg mt-4 mt-lg-5 pt-0 pt-lg-3">
      <div class="d-flex justify-content-between px-3">
         <!-- Search box -->
         <div>
            <input class="border rounded p-1" type="text" placeholder="write your tag here" id="search-tag">
            <button class="px-2 py-1 rounded bg-primary"><i class="fa fa-search text-white" aria-hidden="true"></i></button>
         </div>
         <!--  -->
         <div class="cal-input-wrapper">
            <div class="input-group date datepicker" id="datepicker">
                
                <input type="text" class="form-control" value="<?=empty($selectedDate)? '':date('d-m-Y', strtotime($selectedDate)); ?>" id="date" readonly />
                <span class="input-group-append">
                  <span class="input-group-text bg-light d-block">
                    <i class="fa fa-calendar"></i>
                  </span>
                </span>
              </div>
         </div>
      </div>
      <div class="p-2-sm p-5-lg py-4">
         <h3 class="text-center"><?=translate('¿Cómo Puedo Mejorar?');?>:</h3>
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
   let selectedDate='<?php echo $selectedDate;?>';
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
            window.location.href = SITE_URL + "/users/improvements.php?date=" + e.format('yyyy-mm-dd');
         }else{
            window.location.href = SITE_URL + "/users/improvements.php";
         }
        });
    
  });
   let tag;
   let pageNumber = 0;
   let totalPage = 0;
   const itemContainer = document.querySelector(".item-container")
   document.getElementById("search-tag").addEventListener("change", (e) => {
      tag = e.target.value;
      pageNumber = 1;
      itemContainer.innerHTML = ""
      loadMoreItems(pageNumber, tag)
   })

   function loadMoreItems(pageNumber, tag) {
      console.log(pageNumber, "46");
      const url = `<?= SITE_URL; ?>/users/ajax/ajax.php?get_improvements=get_improvements&date=${selectedDate}&page=${pageNumber}${tag? `&tag=${tag}`:""}`;
      fetch(url)
         .then(res => res.json())
         .then(data => {
            totalPage = data.data.total_page;
            const dailyVictories = data.data.improvements;
            dailyVictories.forEach(dailyVictory => {
               if (dailyVictory.improvements) {
                  const classesToAdd = ['py-3', 'mb-2', 'border-bottom', 'border-1', 'border-light', 'border-opacity-25', 'quote-item']
                  const item = document.createElement("li")
                  item.innerHTML =
                     `<div>
                     <p class="date-font mt-2"> 
                           <small>${dailyVictory.local_date}</small>
                           </P>
                        <div class="my-2">${dailyVictory.improvements}</div>
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