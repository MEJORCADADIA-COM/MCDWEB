<?php
require_once 'inc/commonIncludes.php';

header("Content-Type: application/json");

if (isNotAuthenticated()) {
    return response(['success' => false, 'message' => 'You are unauthenticated'], 401);
}

$request = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ! empty($request['add_monthly_victory'])) {
    if (empty($request['victory']) || empty($request['month']) || empty($request['year'])) {
        return response(['success' => false, 'message' => 'Monthly note is required'], 422);
    }

    try {
        $monthYear = ((int)$request['month']) . '_' . ((int)$request['year']);

        $monthlyVictory = $common->first(
            'monthly_victories',
            'user_id = :user_id AND month_year = :month_year',
            ['user_id' => $userInfos['id'], 'month_year' => $monthYear],
            ['id']
        );

        if ($monthlyVictory) {
            $common->update('monthly_victories', ['victory' => trim($request['victory'])], 'id = :id', ['id' => $monthlyVictory['id']]);
        } else {
            $common->insert('monthly_victories', ['user_id' => $userInfos['id'], 'victory' => trim($request['victory']), 'month_year' => $monthYear]);
        }

        return response(['success' => true]);
    } catch (Exception $e) {
        return response(['success' => false, 'message' => 'Something went wrong. Please try again.'], 500);
    }
}

//// check/uncheck monthly victories
//if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && ! empty($request['update_is_checked'])) {
//    $monthlyVictory = $common->first('monthly_victories', 'id = :id AND user_id = :user_id', ['id' => $request['id'], 'user_id' => $userInfos['id']]);
//
//    if (! $monthlyVictory) {
//        return response(['success' => false, 'message' => 'Monthly note not found'], 404);
//    }
//
//    try {
//        $common->update('monthly_victories', ['is_checked' => (int)$request['is_checked']], 'id = :id', ['id' => $monthlyVictory['id']]);
//    } catch (Exception $e) {
//        return response(['success' => false, 'message' => 'Something went wrong. Please try again.']);
//    }
//
//    return response(['success' => true]);
//}
//
//// delete monthly victories
//if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && ! empty($request['delete_victory'])) {
//    $monthlyVictory = $common->first('monthly_victories', 'id = :id AND user_id = :user_id', ['id' => $request['id'], 'user_id' => $userInfos['id']]);
//
//    if (! $monthlyVictory) {
//        return response(['success' => false, 'message' => 'Monthly note not found'], 404);
//    }
//
//    try {
//        $common->delete('monthly_victories', 'id = :id', ['id' => $monthlyVictory['id']]);
//    } catch (Exception $e) {
//        return response(['success' => false, 'message' => 'Something went wrong. Please try again.']);
//    }
//
//    return response(['success' => true]);
//}