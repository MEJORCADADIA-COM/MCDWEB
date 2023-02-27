<div class="to-be-done-area" id="to-be-done-area" style="display:block; ">
    <ol class="to-be-done-list text-white" style="font-size: 1rem;" id="to-be-done-list">
        <?php foreach ($toBeDone as $key => $item) :  ?>
            <li class="border-bottom mb-1 me-3  position-relative" id="to-be-done-list-item-<?= $key; ?>">
                <span><?= $item['to_be_done_today']; ?> </span>
                <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $extendedDailygoals['id']; ?>" value="<?= $key; ?>" class="input-to-be-done check-items" type="checkbox" <?php if ($item['is_checked']) echo 'checked'; ?>>
                <!--                <a class="edit-actions edit-to-be-done-btn" data-type="appointment" data-id="--><? //= $extendedDailygoals['id']; 
                                                                                                                    ?><!--" data-to-be-done-id="--><? //= $key; 
                                                                                                                                                    ?><!--" href="#">-->
                <!--                    <i class="fa fa-pencil" data-id="--><? //= $extendedDailygoals['id']; 
                                                                            ?><!--" data-to-be-done-id="--><? //= $key; 
                                                                                                            ?><!--"></i>-->
                <!--                </a>-->
                <a class="edit-actions delete-to-be-done-btn" data-type="appointment" data-id="<?= $extendedDailygoals['id']; ?>" data-to-be-done-id="<?= $key; ?>" href="#">
                    <i class="fa fa-trash-o" aria-hidden="true" data-id="<?= $extendedDailygoals['id']; ?>" data-to-be-done-id="<?= $key; ?>"></i>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
    <?php if ($today <= $currentDate) : ?>
        <div class="form-group screenonly" style="padding:20px; text-align:right;">

            <button type="button" id="save-to-be-done" style="display:none;" class="button btn btn-info mb-1 mb-lg-0" onClick="saveToBeDone()"><i class="fa fa-save"></i> Save To Be Done</button>

            <button type="button" class="button btn btn-info" onClick="addTextArea('to-be-done-list', 'to-be-done-item', 'to_be_done[]', 'Write to be done details', 'new-to-be-done'); showButton('save-to-be-done')">
                <i class="fa fa-book"></i> Add To Be Done
            </button>

        </div>
    <?php endif; ?>
</div>

<script>
    function saveToBeDone() {
        const toBeDoneElements = document.querySelectorAll('.new-to-be-done');
        const toBeDone = [];
        toBeDoneElements.forEach(e => toBeDone.push(e.value));

        if (toBeDone.length > 0) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    currentDate: currentDate,
                    saveNewToBeDone: 'saveNewToBeDone',
                    to_be_done: toBeDone
                },
                success: function(data) {
                    var jsonObj = JSON.parse(data);
                    console.log(jsonObj);
                    if (jsonObj.success) {
                        $('.to-be-done-item').remove();
                        for (const prop in jsonObj.to_be_done_list) {
                            $("#to-be-done-list").append(
                                `<li class="text-white border-bottom mb-1 me-3" style="font-size: 1rem;" id="to-be-done-list-item-${ prop }">
                                    <span>${ jsonObj.to_be_done_list[prop] }</span>
                                    <input class="input-to-be-done check-items" type="checkbox" data-id="${ jsonObj.id }" value="${ prop }">
                                    <a class="edit-actions delete-to-be-done-btn" href="#" data-to-be-done-id=${prop} data-id="${ jsonObj.id }">
                                        <i class="fa fa-trash-o" data-to-be-done-id=${prop} data-id="${ jsonObj.id }" aria-hidden="true"></i>
                                    </a>
                                </li>`
                                // <a class="edit-actions edit-to-be-done-btn" data-to-be-done-id=${prop} data-id="${ jsonObj.id }" href="#">
                                //     <i class="fa fa-pencil" data-to-be-done-id=${prop} data-id="${ jsonObj.id }"></i>
                                // </a>
                            );
                        }
                        $('#save-to-be-done').hide();
                    }
                }
            });
        }
    }

    $('#to-be-done-list').on('click', (e) => {
        if (e.target.classList.contains('input-to-be-done')) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    id: e.target.dataset.id,
                    toBeDoneChecked: 'toBeDoneChecked',
                    to_be_done_id: e.target.value,
                    is_checked: e.target.checked
                },
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        showToast('success', 'To be done today checked');
                    } else {
                        showToast('error', 'Something went wrong')
                    }
                }
            });
        } else if (e.target.classList.contains('delete-to-be-done-btn') || e.target.parentElement.classList.contains('delete-to-be-done-btn')) {
            if (confirm('Está Seguro que quiere Eliminar?')) {
                $.ajax({
                    url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                    type: "POST",
                    data: {
                        id: e.target.dataset.id,
                        toBeDoneDelete: 'toBeDoneDelete',
                        toBeDone_id: e.target.dataset.toBeDoneId
                    },
                    success: (data) => {
                        data = JSON.parse(data);
                        if (data.success) {
                            showToast('success', 'To be done today deleted');
                            $(`#to-be-done-list-item-${e.target.dataset.toBeDoneId}`).remove();
                        } else {
                            showToast('error', 'Something went wrong')
                        }
                    }
                });
            }
        }
    })
</script>