<?php require_once "inc/header.php"; ?>
<style>
   .list-text {
      text-decoration: none;
      color: white;
   }

   .list-text:hover {
      color: gainsboro;
   }
</style>

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
         <a class="bg-primary text-white px-2 py-1 rounded" href="<?= SITE_URL; ?>/users/toRememberCalendar.php" id=" calendarBtn"><i class="fa fa-calendar"></i></a>
      </div>
      <div class="p-2-sm p-5-lg py-4">
         <h3 class="text-center">Momentos Para Recordar</h3>
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
   const itemContainer = document.querySelector(".item-container")
   document.getElementById("search-tag").addEventListener("change", (e) => {
      tag = e.target.value;
      pageNumber = 1;
      itemContainer.innerHTML = ""
      loadMoreItems(pageNumber, tag)
   })

   function loadMoreItems(pageNumber, tag) {
      console.log(pageNumber, "46");
      const url = `<?= SITE_URL; ?>/users/ajax/toRemember.php?to_remember=to_remember&page=${pageNumber}${tag? `&tag=${tag}`:""}`;
      fetch(url)
         .then(res => res.json())
         .then(data => {
            totalPage = data.data.total_page;
            const memories = data.data.to_remember;
            console.log(memories);
            memories.forEach(memory => {
               console.log(memory);
               console.log(memory.to_remember);
               if (memory.to_remember) {
                  const classesToAdd = ['py-2', 'mb-2', 'border-bottom', 'border-1', 'border-light', 'border-opacity-25', 'quote-item']
                  const item = document.createElement("li")
                  item.innerHTML =
                     `<div>
                        <p class="my-2"><a class="list-text" href="<?= SITE_URL; ?>/users/toRememberCalendar.php?month_year=${formatMonth(new Date(memory.date))}&date=${formatday(new Date(memory.date))}">${memory.to_remember}</a></p>
                        <div class="d-flex justify-content-between">
                           <p class="text-muted date-font mt-2"> 
                           <small><strong>Date: </strong>${formatDate(new Date(memory.date))}</small>
                           </P>
                           <div class="d-flex">
                              <p class="text-muted date-font mt-2">
                                 <small><strong>${memory.tags.length>1?"Tags: ":"Tag: "}</strong>${memory.tags[0]?`${memory.tags[0].tag}`:""}${memory.tags[1]?`, ${memory.tags[1].tag}`:""}${memory.tags[2]?`, ${memory.tags[2].tag}`:""}</small>
                              </p>
                           </div>
                        </div>
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
         document.getElementById("observe-container").innerHTML = `<div class="text-center">All data loaded</div>`
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