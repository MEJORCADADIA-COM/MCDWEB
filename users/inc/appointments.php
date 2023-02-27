<div class="appointment-area" id="appointment-area" style="display:block; ">
    <ol class="appointment-list text-white" style="font-size: 1rem;" id="appointment-list">
        <?php foreach ($appointments as $key => $item) :  ?>
            <li class="border-bottom mb-1 me-3 pe-4 position-relative" id="appointment-list-item-<?= $key; ?>">
                <span><?= $item['appointment']; ?> </span>
                <input <?= ($isPastDate == true) ? 'disabled' : ''; ?> data-id="<?= $extendedDailygoals['id']; ?>" value="<?= $key; ?>" class="input-appointments check-items" name="appointment_checked[<?= $key; ?>]" type="checkbox" <?php if ($item['is_checked']) echo 'checked'; ?>>
                <a class="edit-actions delete-appointment-btn" data-appointment-id="<?= $key; ?>" data-id="<?= $extendedDailygoals['id']; ?>" href="#">
                    <i class="fa fa-trash-o" data-appointment-id="<?= $key; ?>" data-id="<?= $extendedDailygoals['id']; ?>" aria-hidden="true"></i>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
    <?php if ($today <= $currentDate) : ?>
        <div class="form-group screenonly" style="padding:20px; text-align:right;">

            <button type="button" id="save-appointment" style="display:none;" class="button btn btn-info mb-1 mb-lg-0" onClick="saveAppointments()">
                <i class="fa fa-save"></i> Guardar Citas y Eventos</button>

            <button type="button" class="button btn btn-info" onClick="addTextArea('appointment-list', 'appointment-item', 'appointments[]', 'Write appointment details', 'new-appointment'); showButton('save-appointment')">
                <i class="fa fa-book"></i> Agrega Citas y Eventos
            </button>

        </div>
    <?php endif; ?>
</div>

<script>
    function saveAppointments() {
        const appointmentElements = document.querySelectorAll('.new-appointment');
        const appointments = [];
        appointmentElements.forEach(e => appointments.push(e.value));

        if (appointments.length > 0) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    currentDate: currentDate,
                    saveNewAppointments: 'saveNewAppointments',
                    appointments: appointments
                },
                success: function(data) {
                    var jsonObj = JSON.parse(data);
                    console.log(jsonObj);
                    if (jsonObj.success) {
                        $('.appointment-item').remove();
                        for (const prop in jsonObj.appointments) {
                            $("#appointment-list").append(
                                `<li class="text-white border-bottom mb-1 me-3" style="font-size: 1rem;" id="appointment-list-item-${ prop }">
                                    <span>${ jsonObj.appointments[prop] }</span>
                                    <input name="appointment_checked[${ prop }]" class="input-appointments check-items" type="checkbox" data-id="${ jsonObj.id }" value="${ prop }">
                                    <a class="edit-actions delete-appointment-btn" data-appointment-id="${ prop }" data-id="${jsonObj.id}" href="#">
                                        <i class="fa fa-trash-o" data-appointment-id="${ prop }" data-id="${jsonObj.id}" aria-hidden="true"></i>
                                    </a>
                                </li>`
                                // <a class="edit-actions edit-appointment-btn" data-type="appointment" data-id="${ prop }" href="#"><i class="fa fa-pencil"></i></a>
                            );
                        }
                        $('#save-appointment').hide();
                    }
                }
            });
        }
    }

    $('#appointment-list').on('click', (e) => {
        if (e.target.classList.contains('input-appointments')) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    id: e.target.dataset.id,
                    appointmentChecked: 'appointmentChecked',
                    appointment_id: e.target.value,
                    is_checked: e.target.checked
                },
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        showToast('success', 'Appointment checked');
                    } else {
                        showToast('error', 'Something went wrong')
                    }
                }
            });
        } else if (e.target.classList.contains('delete-appointment-btn') || e.target.parentElement.classList.contains('delete-appointment-btn')) {
            if (confirm('Está Seguro que quiere Eliminar?')) {
                $.ajax({
                    url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                    type: "POST",
                    data: {
                        id: e.target.dataset.id,
                        appointmentDelete: 'appointmentDelete',
                        appointment_id: e.target.dataset.appointmentId
                    },
                    success: (data) => {
                        data = JSON.parse(data);
                        if (data.success) {
                            showToast('success', 'Appointment deleted');
                            $(`#appointment-list-item-${e.target.dataset.appointmentId}`).remove();
                        } else {
                            showToast('error', 'Something went wrong')
                        }
                    }
                });
            }
        }
    })
</script>