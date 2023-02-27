<div class="notes-area" id="notes-area" style="display:block; ">
    <ol class="note-list text-white" style="font-size: 1rem;" id="note-list">
        <?php foreach ($notes as $key => $item) :  ?>
            <li class="border-bottom mb-1 me-3  position-relative" id="note-list-item-<?= $key; ?>">
                <span><?= $item['notes']; ?> </span>
                <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $extendedDailygoals['id']; ?>" value="<?= $key; ?>" class="input-notes check-items" type="checkbox" <?php if ($item['is_checked']) echo 'checked'; ?>>
                <!--                <a class="edit-actions edit-note-btn" data-type="appointment" data-id="--><? //= $extendedDailygoals['id']; 
                                                                                                                ?><!--" data-note-id="--><? //= $key; 
                                                                                                                                            ?><!--" href="#">-->
                <!--                    <i class="fa fa-pencil" data-id="--><? //= $extendedDailygoals['id']; 
                                                                            ?><!--" data-note-id="--><? //= $key; 
                                                                                                        ?><!--"></i>-->
                <!--                </a>-->
                <a class="edit-actions delete-note-btn" data-type="appointment" data-id="<?= $extendedDailygoals['id']; ?>" data-note-id="<?= $key; ?>" href="#">
                    <i class="fa fa-trash-o" aria-hidden="true" data-id="<?= $extendedDailygoals['id']; ?>" data-note-id="<?= $key; ?>"></i>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="form-group" id="notes-textarea"></div>
    <?php if ($today <= $currentDate) : ?>
        <div class="form-group screenonly" style="padding:20px; text-align:right;">

            <button type="button" id="save-notes" style="display:none;" class="button btn btn-info mb-1 mb-lg-0" onClick="saveNote()"><i class="fa fa-save"></i> Save Notes</button>

            <button type="button" class="button btn btn-info" onClick="addTextArea('note-list','notes-item', 'notes[]', 'Write Notes', 'new-note'); showButton('save-notes')">
                <i class="fa fa-book"></i> Add Note
            </button>

        </div>
    <?php endif; ?>
</div>

<script>
    function saveNote() {
        const notesElements = document.querySelectorAll('.new-note');
        const notes = [];
        notesElements.forEach(e => notes.push(e.value));

        if (notes.length > 0) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    currentDate: currentDate,
                    saveNewNotes: 'saveNewNotes',
                    notes: notes
                },
                success: function(data) {
                    var jsonObj = JSON.parse(data);
                    console.log(jsonObj);
                    if (jsonObj.success) {
                        $('.notes-item').remove();
                        for (const prop in jsonObj.notes) {
                            $("#note-list").append(
                                `<li class="border-bottom mb-1 me-3" style="font-size: 1rem;" id="note-list-item-${ prop }">
                                    <span>${ jsonObj.notes[prop] }</span>
                                    <input class="input-notes check-items" type="checkbox" data-id="${ jsonObj.id }" value="${ prop }">
                                    <a class="edit-actions delete-note-btn" data-note-id="${prop}" data-id="${ jsonObj.id }" href="#"><i class="fa fa-trash-o" aria-hidden="true" data-note-id="${prop}" data-id="${ jsonObj.id }"></i></a>
                               </li>`
                                // <a class="edit-actions edit-note-btn" data-note-id="${prop}" data-id="${ jsonObj.id }" href="#"><i class="fa fa-pencil" data-note-id="${prop}" data-id="${ jsonObj.id }"></i></a>
                            );
                        }
                        $('#save-notes').hide();
                    }
                }
            });
        }
    }

    $('#note-list').on('click', (e) => {
        if (e.target.classList.contains('input-notes')) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    id: e.target.dataset.id,
                    noteChecked: 'noteChecked',
                    note_id: e.target.value,
                    is_checked: e.target.checked
                },
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        showToast('success', 'Note checked');
                    } else {
                        showToast('error', 'Something went wrong')
                    }
                }
            });
        } else if (e.target.classList.contains('delete-note-btn') || e.target.parentElement.classList.contains('delete-note-btn')) {
            if (confirm('Está Seguro que quiere Eliminar?')) {
                $.ajax({
                    url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                    type: "POST",
                    data: {
                        id: e.target.dataset.id,
                        noteDelete: 'noteDelete',
                        note_id: e.target.dataset.noteId
                    },
                    success: (data) => {
                        data = JSON.parse(data);
                        if (data.success) {
                            showToast('success', 'Note deleted');
                            $(`#note-list-item-${e.target.dataset.noteId}`).remove();
                        } else {
                            showToast('error', 'Something went wrong')
                        }
                    }
                });
            }
        }
    })
</script>