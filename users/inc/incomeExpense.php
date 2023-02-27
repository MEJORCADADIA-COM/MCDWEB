<div class="income-expenses-area" id="income-expenses-area" style="display:block; ">
    <ol class="income-expense-list text-white" style="font-size: 1rem;" id="income-expense-list">
        <?php foreach ($incomeExpenses as $key => $item) :  ?>
            <li class="border-bottom mb-1 me-3" id="income-expense-list-item-<?= $key; ?>">
                <span><?= $item['income_expenses']; ?> </span>
                <!--                <a class="edit-actions edit-income-expense-btn" data-type="appointment" data-income-expense-id="--><? //= $key 
                                                                                                                                        ?><!--" data-id="--><? //= $extendedDailygoals['id']; 
                                                                                                                                                            ?><!--" href="#">-->
                <!--                    <i class="fa fa-pencil" data-income-expense-id="--><? //= $key 
                                                                                            ?><!--" data-id="--><? //= $extendedDailygoals['id']; 
                                                                                                                ?><!--"></i>-->
                <!--                </a>-->
                <a class="edit-actions delete-income-expense-btn" data-type="appointment" data-income-expense-id="<?= $key ?>" data-id="<?= $extendedDailygoals['id']; ?>" href="#">
                    <i class="fa fa-trash-o" aria-hidden="true" data-income-expense-id="<?= $key ?>" data-id="<?= $extendedDailygoals['id']; ?>"></i>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>

    <div class="form-group" id="income-expenses-textarea"></div>
    <?php if ($today <= $currentDate) : ?>
        <div class="form-group screenonly" style="padding:20px; text-align:right;">

            <button type="button" id="save-income-expenses" style="display:none;" class="button btn btn-info mb-1 mb-lg-0" onClick="saveIncomeExpense()"><i class="fa fa-save"></i> Save Income & Expenses</button>

            <button type="button" class="button btn btn-info" onClick="addTextArea('income-expense-list','income-expense-item', 'income_expenses[]', 'Write Income Expenses', 'new-income-expense'); showButton('save-income-expenses')">
                <i class="fa fa-book"></i> Add Income & Expenses
            </button>

        </div>
    <?php endif; ?>
</div>

<script>
    function saveIncomeExpense() {
        const incomeExpensesElements = document.querySelectorAll('.new-income-expense');
        const incomeExpenses = [];
        incomeExpensesElements.forEach(e => incomeExpenses.push(e.value));

        if (incomeExpenses.length > 0) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    currentDate: currentDate,
                    saveNewIncomeExpenses: 'saveNewIncomeExpenses',
                    income_expenses: incomeExpenses
                },
                success: function(data) {
                    var jsonObj = JSON.parse(data);
                    console.log(jsonObj);
                    if (jsonObj.success) {
                        $('.income-expense-item').remove();
                        for (const prop in jsonObj.income_expenses) {
                            $("#income-expense-list").append(
                                `<li class="text-white border-bottom mb-1 me-3" style="font-size: 1rem;" id="income-expense-list-item-${ prop }">
                                    <span>${ jsonObj.income_expenses[prop] }</span>
                                    <a class="edit-actions delete-income-expense-btn" data-id="${jsonObj.id}" data-income-expense-id="${ prop }" href="#">
                                        <i class="fa fa-trash-o" aria-hidden="true" data-id="${jsonObj.id}" data-income-expense-id="${ prop }"></i>
                                    </a>
                               </li>`
                                //         <a class="edit-actions edit-income-expense-btn" data-id="${jsonObj.id}" data-income-expense-id="${ prop }" href="#">
                                //         <i class="fa fa-pencil" data-id="${jsonObj.id}" data-income-expense-id="${ prop }"></i>
                                // </a>
                            );
                        }
                        $('#save-income-expenses').hide();
                    }
                }
            });
        }
    }

    $('#income-expense-list').on('click', (e) => {
        if (e.target.classList.contains('input-income-expenses')) {
            $.ajax({
                url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                type: "POST",
                data: {
                    id: e.target.dataset.id,
                    incomeExpenseChecked: 'incomeExpenseChecked',
                    income_expense_id: e.target.value,
                    is_checked: e.target.checked
                },
                success: function(data) {
                    data = JSON.parse(data);
                    if (data.success) {
                        showToast('success', 'Income & expense checked');
                    } else {
                        showToast('error', 'Something went wrong')
                    }
                }
            });
        } else if (e.target.classList.contains('delete-income-expense-btn') || e.target.parentElement.classList.contains('delete-income-expense-btn')) {
            if (confirm('Está Seguro que quiere Eliminar?')) {
                $.ajax({
                    url: SITE_URL + "/users/ajax/extendedDailyGoalsAjax.php",
                    type: "POST",
                    data: {
                        id: e.target.dataset.id,
                        incomeExpenseDelete: 'incomeExpenseDelete',
                        income_expense_id: e.target.dataset.incomeExpenseId
                    },
                    success: (data) => {
                        data = JSON.parse(data);
                        if (data.success) {
                            showToast('success', 'Income & Expense deleted');
                            $(`#income-expense-list-item-${e.target.dataset.incomeExpenseId}`).remove();
                        } else {
                            showToast('error', 'Something went wrong')
                        }
                    }
                });
            }
        }
    })
</script>