<?php require_once "inc/header.php"; ?>

<?php
$dailyInspirations = $common->paginate(table: 'daily_inspirations', limit: 50);
$pageCount = $common->pageCount(table: 'daily_inspirations', limit: 50);
$currentPage = !empty($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
?>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 my-3 text-white min-vh-100">
    <div class="">
        <div class="px-3-sm px-5-lg">
            <div class="container p-2-sm p-5-lg py-4 shadow">
                <h3 class="text-center"><?=translate('Inspirations');?></h3>

                <div>
                <ul class="list-group p-1-sm p-3-lg item-container list-unstyled">
              
                </ul>
                <div class="observe-container" id="observe-container"></div>

                    
                </div>
            </div>
        </div>

        <style>
            .active {
                outline: 2px solid yellow;
                border-radius: 5px;
            }
        </style>
        
    </div>
</main>
<script>
      const itemContainer = document.querySelector(".item-container");
      let pageNumber = 0;
    let totalPage = 0;
    let tag='';
    let selectedDate='';
      const options = {
      rootmargin: "200px",
      threshold: 0.1,
   }
   function loadMoreItems(pageNumber, tag) {
      const url = `<?= SITE_URL; ?>/users/ajax/ajax.php?daily_inspirations=daily_inspirations&date=${selectedDate}&page=${pageNumber}${tag? `&tag=${tag}`:""}`;
      fetch(url)
         .then(res => res.json())
         .then(data => {
            totalPage = data.data.total_page;
            const dailyVictories = data.data.inspirations;
            dailyVictories.forEach(dailyVictory => {
               if (dailyVictory.inspiration_quote) {
                  const classesToAdd = ['d-flex','justify-content-between','py-2','mb-2','border-bottom', 'border-1', 'border-light', 'border-opacity-25']
                  const item = document.createElement("li")
                  const div = document.createElement("div");
                  var jstr = $("<div/>").html(dailyVictory.inspiration_quote).text();
                  div.innerHTML =
                     `<div>
                        <p class="text-muted date-font px-3">${dailyVictory.local_date}</p>    
                        <div class="px-3 mt-3">${jstr}</div>
                     </div>`;
                  item.classList.add(...classesToAdd)
                  item.appendChild(div)
                  itemContainer.appendChild(item)
               }
            })
         })
   }
   const callBack = (entries, observer) => {
    console.log('callBack',entries);
      if (entries[0].isIntersecting && pageNumber <= totalPage) {
         console.log(totalPage);
         pageNumber += 1
         loadMoreItems(pageNumber, tag)
         observer.observe(document.querySelector('.observe-container'))
      } else {
         document.getElementById("observe-container").innerHTML = `<div class="text-center text-mited mt-4"><small>No more data to show</small></div>`
      }

   }
   const observer = new IntersectionObserver(callBack, options);
   observer.observe(document.querySelector('.observe-container'))
   console.log('observer',observer,IntersectionObserver);
</script>
<?php require_once "inc/footer.php"; ?>