<?php
require_once "../helper.php";
require_once base_path("/users/inc/header.php");
require_once base_path('/users/repositories/dailyVictories.php');

if (isset($_POST['update_daily_victory'])) {
    $currentUrl = SITE_URL . $_SERVER['REQUEST_URI'];

    $tags = [];
    foreach ($_POST['tags'] as $tag) {
        if (str_contains($tag, ' ')) {
            setError('Tags can not have spaces in them');
            header("Location: $currentUrl");
            return;
        }

        $tags[] = $tag;
    }

    $dailyVictory = trim($_POST['daily_victory']);
    $bgColor = trim($_POST['bg_color']);
    if (empty($dailyVictory)) {
        Session::set('error', 'Daily victory can not be empty.');
        header("Location: $currentUrl");
        return;
    }

    try {
        updateVictoryWithTags($_POST['victory_id'], $dailyVictory, $tags, $user_infos['id'],$bgColor);
        setSuccess('Daily victory updated successfully');
    } catch (Exception $e) {
        setError();
    }

    header("Location: $currentUrl");
    return;
}

$month = date('n');
$year = date('Y');
if (!empty($_GET['month_year'])) {
    list($month, $year) = sscanf($_GET['month_year'], "%d-%d");
}

$firstDay = date('N', strtotime("{$year}-{$month}-01"));
$numOfDays = date('t', strtotime("{$year}-{$month}-01"));
$day = 1;

if ($month - 1 > 0) {
    $prevMonthYear = $month - 1 . "-{$year}";
} else {
    $prevMonthYear =  "12-" . ($year - 1);
}

if ($month + 1 > 12) {
    $nextMonthYear = "1-" . ($year + 1);
} else {
    $nextMonthYear = $month + 1 . "-{$year}";
}

$monthlyDailyVictories = getMonthlyVictoriesWithTags($user_infos['id'], $month, $year);

$dates = array_column($monthlyDailyVictories, 'date');
$monthlyDailyVictories = array_combine($dates, $monthlyDailyVictories);

$monthlyVictory = $common->first('monthly_victories', 'user_id = :user_id AND month_year = :month_year', ['user_id' => $user_infos['id'], 'month_year' => "{$month}_{$year}"]);

function getTDClass($day, $monthlyDailyVictory): string
{
    $class = 'border day-box ';
    $class .= !empty($_GET['date']) && $day === (int)$_GET['date'] ? 'target-date ' : '';
    $class .= $monthlyDailyVictory ? 'pointer' : '';

    return $class;
}
?>
<script src="https://mejorcadadia.com/users/assets/jquery-3.6.0.min.js"></script>

<style>
    .ck-editor .ck.ck-editor__main{
            max-height:300px;
            overflow: scroll;
    }
    @media screen and (max-width: 480px) {

        .tag-text {
            font-size: 10px;
        }

        .date-text {
            font-size: 10px;
        }

        .edit-actions {
            display: inline;
        }

        .modal-content {
            width: 95%;
        }

        .modal-content {
            margin: 5% auto;
        }

        .table {
            width: 200%;
        }
        .ck-editor .ck.ck-editor__main{
            max-height:200px;
            overflow: scroll;
        }
    }

    @media screen and (min-width: 600px) {
        .edit-actions {
            display: inline;
        }

        .modal-content {
            width: 95%;
        }

        .modal-content {
            margin: 10% auto;
        }

        .table {
            width: 150%;
        }
    }

    @media screen and (min-width: 1200px) {
        .edit-actions {
            display: none;
        }

        .modal-content {
            margin: 10% auto;
            width: 75%;
        }

        .table {
            width: 100%;
        }
    }

    table {
        display: table;
        table-layout: fixed;
        border-style: hidden;
        overflow-x: scroll;
    }

    td {
        border: 1px solid #ccc;
    }

    /* top-left border-radius */
    table tr:first-child th:first-child {
        border-top-left-radius: 6px;
    }

    /* top-right border-radius */
    table tr:first-child th:last-child {
        border-top-right-radius: 6px;
    }

    /* bottom-left border-radius */
    table tr:last-child td:first-child {
        border-bottom-left-radius: 6px;
    }

    /* bottom-right border-radius */
    table tr:last-child td:last-child {
        border-bottom-right-radius: 6px;
    }

    .modal {
        display: none;
        position: absolute;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        padding: 10px;
        border: 1px solid #888;
    }

    .close {
        color: #666;
        float: right;
        font-size: 16px;
        font-weight: 400;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }

    a.close,
    .pointer {
        cursor: pointer;
    }

    .day-box:hover {
        background-color: #dbdbdb;
    }


    .alpine-alert {
        position: fixed;
        top: 10px;
        right: 25px;
        z-index: 1020;
    }

    .tox-notifications-container {
        display: none;
    }

    .text-bottom-border,
    .text-bottom-border:focus {
        border: none;
        border-bottom: 1px solid white;
        outline: none;
    }

    .check-items {
        width: 1.5rem;
        height: 1.5rem;
        position: absolute;
        right: 0;
    }

    li:hover .edit-actions {
        display: inline;
    }

    .edit-actions i {
        color: #fef200;
    }

    .letter {
        float: right;
        margin: 15px 10px 15px 10px;
    }

    .target-date {
        outline: 2px solid #0D6EFD;
    }
</style>

<main role="main" class="col-md-9 ml-sm-auto col-lg-10 text-white">
    <div class="projects min-vh-100 px-0 px-lg-3 pb-3" style="background-color: #ed008c;">
        <div class="d-flex justify-content-between my-4 px-2 px-lg-0">
            <a class="btn btn-warning" href="<?= SITE_URL; ?>/users/dailyVictories.php" id=" calendarBtn">Atrás</a>
            <div>
                <input class="form-control" type="month" name="month" value="<?= date('Y-m', strtotime("{$year}-{$month}-01")); ?>">
            </div>
        </div>
        <h3 class="text-center my-3">Mi Victoria Diaria</h3>
        <div class="d-flex justify-content-between px-1 px-lg-0">
            <a class="text-white" href="<?= SITE_URL . "/users/dailyVictoriesCalendar.php?month_year={$prevMonthYear}"; ?>"><i class="fa fa-arrow-left fs-4"></i></a>
            <h5><?= date("F Y", strtotime("$year-$month-01")) ?></h5>

            <a class="text-white" href="<?= SITE_URL . "/users/dailyVictoriesCalendar.php?month_year={$nextMonthYear}"; ?>"><i class="fa fa-arrow-right fs-4"></i></a>
        </div>
        <br>
        <table class="table table-borderded calendar">
            <thead style="background-color: #fef200;">
                <tr class="border text-center py-3">
                    <th>L</th>
                    <th>M</th>
                    <th>M</th>
                    <th>J</th>
                    <th>V</th>
                    <th>S</th>
                    <th>D</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php
                while ($day <= $numOfDays) {
                    echo "<tr>\n";
                    for ($j = 1; $j <= 7; $j++) {
                        if ($day === 1 && $j < $firstDay) {
                            echo "<td class='border day-box' style='height: 80px;'></td>\n";
                        } else {
                            if ($day <= $numOfDays) {
                                $monthlyDailyVictory = $monthlyDailyVictories[date('Y-m-d', strtotime("{$year}-{$month}-{$day}"))] ?? null;
                                $bgColor=empty($monthlyDailyVictory['color'])? '#ffffff':trim($monthlyDailyVictory['color']);
                                echo "<td 
                                style='height: 80px; overflow:hidden; background-color:".$bgColor."' class='" . getTDClass($day, $monthlyVictory) . "' " . ($monthlyDailyVictory ? "onClick='openModal(modal{$monthlyDailyVictory['id']}, event)'" : "") . ">
                                    <p class='text-end date-text'>{$day}</p>";
                                if ($monthlyDailyVictory) {
                                    echo '<div>';
                                    foreach ($monthlyDailyVictory['tags'] as $tag) {
                                        echo "<p class='text-start'>{$tag['tag']}</p>";
                                    }
                                    echo '<div id="modal' . $monthlyDailyVictory['id'] . '" class="modal close" onClick="closeModal(modal' . $monthlyDailyVictory['id'] . ')">
                                            <div class="modal-content">
                                                <a onclick="closeModal(modal' . $monthlyDailyVictory['id'] . ')" class="close mb-2">
                                                    <i class="fa fa-times close"></i>
                                                </a>
                                                <form action="" method="post">
                                                    <input type="hidden" name="victory_id" value="' . $monthlyDailyVictory['id'] . '" >
                                                    <textarea class="editor-textarea" name="daily_victory">' . $monthlyDailyVictory["daily_victory"] . '</textarea>
                                                    <div class="mt-3">
                                                        <p class="ms-0 ms-lg-2">Tags: </p>
                                                        <div class="d-block d-lg-flex justify-content-between">';
                                    $i = 0;
                                    foreach ($monthlyDailyVictory['tags'] as $tag) {
                                        echo "<input class='form-control my-2 mx-0 mx-lg-2' type='text' name='tags[]' value={$tag['tag']}>";
                                        $i++;
                                    }
                                    for (; $i < 3; $i++) {
                                        echo "<input class='form-control my-2 mx-0 mx-lg-2' type='text' name='tags[]'>";
                                    }
                                    echo '</div>
                                                    </div>
                                                    <div class="mt-3">
                                                        <p class="ms-0 ms-lg-2">Color: </p>
                                                        <input type="color" class="my-2 mx-0 mx-lg-2" name="bg_color" value="'.$bgColor.'">
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <button name="update_daily_victory" type="submit" class="btn btn-primary mx-2 mt-3">Update</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>';
                                    echo "</div>";
                                }
                                echo "</td>\n";
                                $day++;
                            } else {
                                echo "<td class='border day-box' style='height: 80px;'></td>\n";
                            }
                        }
                    }
                    echo "</tr>\n";
                }
                ?>
            </tbody>
        </table>

        <div class="mt-5 px-1 px-lg-0">
            <h4 class="text-center">Monthly Notes</h4>
            <div x-data="monthlyVictory" class="text-dark">
                <div class="text-dark">
                    <textarea class="editor-textarea" id="monthlyVictory" x-ref="monthlyVictory"><?= $monthlyVictory['victory'] ?? ''; ?></textarea>
                    <button class="btn btn-info letter my-5" @click="saveMonthlyVictory">Save</button>
                </div>

                <div :class="`alpine-alert alert ${alertClass}`" x-show="showAlert">
                    <p x-text="alertMsg"></p>
                </div>
            </div>
        </div>
    </div>

</main>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('monthlyVictory', () => ({
            showAlert: false,
            alertMsg: '',
            alertClass: '',
            month: <?= $month; ?>,
            year: <?= $year; ?>,

            async saveMonthlyVictory() {
                let monthlyVictory = window.editors['monthlyVictory'].getData().trim();
                if (monthlyVictory === '') {
                    this.showToast('Note can not be empty.', 'error');
                    return;
                }

                const request = await fetch(`<?= SITE_URL ?>/users/ajax/monthlyVictories.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        add_monthly_victory: true,
                        victory: monthlyVictory,
                        month: this.month,
                        year: this.year
                    })
                });

                const response = await request.json();

                if (response.success) {
                    this.showToast('Note saved successfully');
                } else {
                    let message = response.message ?? 'Something went wrong. Try again later.'
                    this.showToast(message, 'error');
                }
            },

            showToast(message, type = 'success') {
                this.alertMsg = message;
                this.showAlert = true;
                if (type === 'success') {
                    this.alertClass = 'alert-success';
                } else {
                    this.alertClass = 'alert-danger';
                }
                setTimeout(() => {
                    this.resetAlert();
                }, 3000)
            },

            resetAlert() {
                this.alertMsg = '';
                this.showAlert = false;
                this.alertClass = '';
            }
        }))
    })

    // Modal script

    const openModal = (modal, event) => {
        if (!event.target.classList.contains("close")) {
            console.log(event.target);
            modal.style.display = "block";
        }
    }
    //
    const closeModal = (modal) => {
        modal.style.display = "none";
    }
    //
    // window.onclick = function(event) {
    //     if (event.target == modal) {
    //         modal.style.display = "none";
    //     }
    // }

    // Editor
    document.querySelectorAll( '.editor-textarea' ).forEach( ( node, index ) => {  
        ClassicEditor
        .create( node, {} )
        .then( newEditor => {      
        if(node.id){
            window.editors[ node.id ] = newEditor;
        }else{
            window.editors[ index ] = newEditor	;
        }			
        });
    });
    

    document.querySelector("input[type=month]").addEventListener("change", (e) => {
        let monthYear = e.target.value;
        let parts = monthYear.split("-");
        monthYear = parseInt(parts[1]) + "-" + parts[0];
        window.location.href = window.location.origin + window.location.pathname + '?month_year=' + monthYear;
    })
</script>
<?php require_once "inc/footer.php"; ?>