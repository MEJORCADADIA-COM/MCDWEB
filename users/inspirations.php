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
                <h3 class="text-center">Inspirations</h3>

                <div>
                    <ul class="list-group p-1-sm p-3-lg">
                        <?php foreach ($dailyInspirations as $inspiration) : ?>
                            <li class="d-flex justify-content-between py-2 mb-2 border-bottom border-1 border-light border-opacity-25">
                                <div>
                                    <!-- Quote -->
                                    <p class="px-3"><?= html_entity_decode($inspiration['inspiration_quote']) ?></p>
                                    <!-- Date -->
                                    <p class="text-muted mt-3 date-font"><?= date('j M, y', strtotime($inspiration['date'])) ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <style>
            .active {
                outline: 2px solid yellow;
                border-radius: 5px;
            }
        </style>
        <!-- Pagination -->
        <div class="container mx-auto pb-4">
            <ul class="pagination d-flex flex-wrap justify-content-center">
                <li class="page-item m-1 <?= $currentPage === 1 ? 'disabled' : '' ?>">
                    <a class="page-link text-white bg-primary rounded" style="font-size: 12px;" href="?page=<?= ($currentPage - 1 < 1) ? 1 : $currentPage - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $pageCount; $i++) : ?>
                    <li class="page-item m-1 <?= $currentPage === $i ? 'active' : '' ?>">
                        <a class="page-link text-white bg-primary rounded" style="font-size: 12px;" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item m-1 <?= $currentPage === $pageCount ? 'disabled' : '' ?>">
                    <a class="page-link text-white bg-primary rounded" style="font-size: 12px;" href="?page=<?= ($currentPage + 1 > $pageCount) ? $pageCount : $currentPage + 1 ?>">Next</a>
                </li>
            </ul>
        </div>
    </div>
</main>

<?php require_once "inc/footer.php"; ?>