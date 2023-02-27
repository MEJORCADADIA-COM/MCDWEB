<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../../lib/Database.php');
include_once($filepath . '/../../lib/Session.php');
include_once($filepath . '/../../helper/Format.php');
include_once($filepath . '/../../lib/RememberCookie.php');


spl_autoload_register(function ($class_name) {
    include_once "../../classes/" . $class_name . ".php";
});
$format = new Format();
$common = new Common();

if (isset($_POST['saveNewAppointments']) && ($_POST['saveNewAppointments'] == 'saveNewAppointments')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $appointments = isset($_POST['appointments']) ? $_POST['appointments'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'extended_dailygoals';
    $addedAppointments = [];
    $newAppointments = [];

    $prevAppointments = $common->first($table_name, 'user_id = :user_id AND date = :date' , ['user_id' => $user_id, 'date' => $currentDate]);
    $extendedDailyGoalsId = null;

    if ($prevAppointments) {
        $extendedDailyGoalsId = $prevAppointments['id'];
        $addedAppointments = json_decode($prevAppointments['appointments'], true);
    }

    foreach ($appointments as $appointment) {
        $appointment = trim($appointment);
        if (!empty($appointment)) {
            $id = uniqid();
            $addedAppointments[$id] = ['appointment' => $appointment, 'is_checked' => false];
            $newAppointments[$id] = $appointment;
        }
    }

    $addedAppointments = json_encode($addedAppointments);
    if ($prevAppointments) {
        $common->update($table_name, ['appointments' => $addedAppointments], 'id = :id', ['id' => $prevAppointments['id']]);
    } else {
        $common->insert($table_name, ['appointments' => $addedAppointments, 'date' => $currentDate, 'user_id' => $user_id]);
        $extendedDailyGoalsId = $common->insertId();
    }
    echo json_encode(['success' => true, 'appointments' => $newAppointments, 'id' => $extendedDailyGoalsId]);
}

if (isset($_POST['appointmentChecked']) && $_POST['appointmentChecked'] === 'appointmentChecked') {
    $isChecked = $_POST['is_checked'] === 'true';
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['appointments']);

    $appointments = json_decode($extendedDailyGoals['appointments'], true);
    $appointments[$_POST['appointment_id']]['is_checked'] = $isChecked;

    $common->update('extended_dailygoals', ['appointments' => json_encode($appointments)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}

if (isset($_POST['appointmentUpdate']) && $_POST['appointmentUpdate'] === 'appointmentUpdate') {
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['appointments']);

    $appointments = json_decode($extendedDailyGoals['appointments'], true);
    $appointments[$_POST['appointment_id']]['appointment'] = $_POST['appointment'];

    $common->update('extended_dailygoals', ['appointments' => json_encode($appointments)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true, 'appointment' => $_POST['appointment']]);
}

if (isset($_POST['appointmentDelete']) && $_POST['appointmentDelete'] === 'appointmentDelete') {
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['appointments']);

    $appointments = json_decode($extendedDailyGoals['appointments'], true);
    unset($appointments[$_POST['appointment_id']]);

    $common->update('extended_dailygoals', ['appointments' => json_encode($appointments)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}

if (isset($_POST['toBeDoneDelete']) && $_POST['toBeDoneDelete'] === 'toBeDoneDelete') {
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['to_be_done_today']);

    $toBeDoneList = json_decode($extendedDailyGoals['to_be_done_today'], true);
    unset($toBeDoneList[$_POST['toBeDone_id']]);

    $common->update('extended_dailygoals', ['to_be_done_today' => json_encode($toBeDoneList)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}

if (isset($_POST['saveNewToBeDone']) && ($_POST['saveNewToBeDone'] == 'saveNewToBeDone')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $toBeDone = isset($_POST['to_be_done']) ? $_POST['to_be_done'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'extended_dailygoals';
    $addedToBeDoneList = [];
    $newToBeDoneList = [];

    $prevAppointments = $common->first($table_name, 'user_id = :user_id AND date = :date' , ['user_id' => $user_id, 'date' => $currentDate]);
    $extendedDailyGoalsId = null;

    if ($prevAppointments) {
        $extendedDailyGoalsId = $prevAppointments['id'];
        $addedToBeDoneList = json_decode($prevAppointments['to_be_done_today'], true);
    }

    foreach ($toBeDone as $toBeDoneTask) {
        $toBeDoneTask = trim($toBeDoneTask);
        if (!empty($toBeDoneTask)) {
            $id = uniqid();
            $addedToBeDoneList[$id] = ['to_be_done_today' => $toBeDoneTask, 'is_checked' => false];
            $newToBeDoneList[$id] = $toBeDoneTask;
        }
    }

    $addedToBeDoneList = json_encode($addedToBeDoneList);
    if ($prevAppointments) {
        $common->update($table_name, ['to_be_done_today' => $addedToBeDoneList], 'id = :id', ['id' => $prevAppointments['id']]);
    } else {
        $common->insert($table_name, ['to_be_done_today' => $addedToBeDoneList, 'date' => $currentDate, 'user_id' => $user_id]);
        $extendedDailyGoalsId = $common->insertId();
    }
    echo json_encode(['success' => true, 'to_be_done_list' => $newToBeDoneList, 'id' => $extendedDailyGoalsId]);
}

if (isset($_POST['toBeDoneChecked']) && $_POST['toBeDoneChecked'] === 'toBeDoneChecked') {
    $isChecked = $_POST['is_checked'] === 'true';
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['to_be_done_today']);

    $toBeDone = json_decode($extendedDailyGoals['to_be_done_today'], true);
    $toBeDone[$_POST['to_be_done_id']]['is_checked'] = $isChecked;

    $common->update('extended_dailygoals', ['to_be_done_today' => json_encode($toBeDone)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}


if (isset($_POST['saveNewIncomeExpenses']) && ($_POST['saveNewIncomeExpenses'] == 'saveNewIncomeExpenses')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $incomeExpenses = isset($_POST['income_expenses']) ? $_POST['income_expenses'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'extended_dailygoals';
    $addedIncomeExpenses = [];
    $newIncomeExpenses = [];

    $extendedDailyGoals = $common->first($table_name, 'user_id = :user_id AND date = :date' , ['user_id' => $user_id, 'date' => $currentDate]);
    $extendedDailyGoalsId = null;

    if ($extendedDailyGoals) {
        $extendedDailyGoalsId = $extendedDailyGoals['id'];
        $addedIncomeExpenses = json_decode($extendedDailyGoals['income_expenses'], true);
    }

    foreach ($incomeExpenses as $incomeExpense) {
        $incomeExpense = trim($incomeExpense);
        if (!empty($incomeExpense)) {
            $id = uniqid();
            $addedIncomeExpenses[$id] = ['income_expenses' => $incomeExpense, 'is_checked' => false];
            $newIncomeExpenses[$id] = $incomeExpense;
        }
    }

    $addedIncomeExpenses = json_encode($addedIncomeExpenses);
    if ($extendedDailyGoals) {
        $common->update($table_name, ['income_expenses' => $addedIncomeExpenses], 'id = :id', ['id' => $extendedDailyGoals['id']]);
    } else {
        $common->insert($table_name, ['income_expenses' => $addedIncomeExpenses, 'date' => $currentDate, 'user_id' => $user_id]);
        $extendedDailyGoalsId = $common->insertId();
    }
    echo json_encode(['success' => true, 'income_expenses' => $newIncomeExpenses, 'id' => $extendedDailyGoalsId]);
}

if (isset($_POST['incomeExpenseChecked']) && $_POST['incomeExpenseChecked'] === 'incomeExpenseChecked') {
    $isChecked = $_POST['is_checked'] === 'true';
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['income_expenses']);

    $incomeExpense = json_decode($extendedDailyGoals['income_expenses'], true);
    $incomeExpense[$_POST['income_expense_id']]['is_checked'] = $isChecked;

    $common->update('extended_dailygoals', ['income_expenses' => json_encode($incomeExpense)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}

if (isset($_POST['incomeExpenseDelete']) && $_POST['incomeExpenseDelete'] === 'incomeExpenseDelete') {
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['income_expenses']);

    $incomeExpenses = json_decode($extendedDailyGoals['income_expenses'], true);
    unset($incomeExpenses[$_POST['income_expense_id']]);

    $common->update('extended_dailygoals', ['income_expenses' => json_encode($incomeExpenses)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}


if (isset($_POST['saveNewNotes']) && ($_POST['saveNewNotes'] == 'saveNewNotes')) {
    $user_id = Session::get('user_id');
    if (!$user_id) {
        $rememberCookieData = RememberCookie::getRememberCookieData();
        if ($rememberCookieData) {
            $user_id = $rememberCookieData[RememberCookie::ID];
        }
    }
    $notes = isset($_POST['notes']) ? $_POST['notes'] : [];
    $currentDate = isset($_POST['currentDate']) ? $_POST['currentDate'] : date('Y-m-d');
    $table_name = 'extended_dailygoals';
    $addedNotes = [];
    $newNotes = [];

    $extendedDailyGoals = $common->first($table_name, 'user_id = :user_id AND date = :date' , ['user_id' => $user_id, 'date' => $currentDate]);
    $extendedDailyGoalsId = null;

    if ($extendedDailyGoals) {
        $extendedDailyGoalsId = $extendedDailyGoals['id'];
        $addedNotes = json_decode($extendedDailyGoals['notes'], true);
    }

    foreach ($notes as $note) {
        $note = trim($note);
        if (!empty($note)) {
            $id = uniqid();
            $addedNotes[$id] = ['notes' => $note, 'is_checked' => false];
            $newNotes[$id] = $note;
        }
    }

    $addedNotes = json_encode($addedNotes);
    if ($extendedDailyGoals) {
        $common->update($table_name, ['notes' => $addedNotes], 'id = :id', ['id' => $extendedDailyGoals['id']]);
    } else {
        $common->insert($table_name, ['notes' => $addedNotes, 'date' => $currentDate, 'user_id' => $user_id]);
        $extendedDailyGoalsId = $common->insertId();
    }
    echo json_encode(['success' => true, 'notes' => $newNotes, 'id' => $extendedDailyGoalsId]);
}

if (isset($_POST['noteChecked']) && $_POST['noteChecked'] === 'noteChecked') {
    $isChecked = $_POST['is_checked'] === 'true';
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['notes']);

    $note = json_decode($extendedDailyGoals['notes'], true);
    $note[$_POST['note_id']]['is_checked'] = $isChecked;

    $common->update('extended_dailygoals', ['notes' => json_encode($note)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}

if (isset($_POST['noteDelete']) && $_POST['noteDelete'] === 'noteDelete') {
    $extendedDailyGoals = $common->first('extended_dailygoals', 'id = :id', ['id' => $_POST['id']], ['notes']);

    $notes = json_decode($extendedDailyGoals['notes'], true);
    unset($notes[$_POST['note_id']]);

    $common->update('extended_dailygoals', ['notes' => json_encode($notes)], 'id = :id', ['id' => $_POST['id']]);
    echo json_encode(['success' => true]);
}