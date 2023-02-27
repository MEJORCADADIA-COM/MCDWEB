<?php
require_once 'inc/commonIncludes.php';

header("Content-Type: application/json");

if (isNotAuthenticated()) {
    return response(['success' => false, 'message' => 'You are unauthenticated'], 401);
}

$request = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ! empty($request['add_monthly_to_remember'])) {
    if (empty($request['to_remember']) || empty($request['month']) || empty($request['year'])) {
        return response(['success' => false, 'message' => 'Monthly note is required'], 422);
    }

    try {
        $monthYear = ((int)$request['month']) . '_' . ((int)$request['year']);

        $monthlyToRemember = $common->first(
            'monthly_to_remembers',
            'user_id = :user_id AND month_year = :month_year',
            ['user_id' => $userInfos['id'], 'month_year' => $monthYear],
            ['id']
        );

        if ($monthlyToRemember) {
            $common->update('monthly_to_remembers', ['to_remember' => trim($request['to_remember'])], 'id = :id', ['id' => $monthlyToRemember['id']]);
        } else {
            $common->insert('monthly_to_remembers', ['user_id' => $userInfos['id'], 'to_remember' => trim($request['to_remember']), 'month_year' => $monthYear]);
        }

        return response(['success' => true]);
    } catch (Exception $e) {
        return response(['success' => false, 'message' => 'Something went wrong. Please try again.'], 500);
    }
}

//// check/uncheck monthly to_remembers
//if ($_SERVER['REQUEST_METHOD'] === 'PATCH' && ! empty($request['update_is_checked'])) {
//    $monthlyToRemember = $common->first('monthly_to_remembers', 'id = :id AND user_id = :user_id', ['id' => $request['id'], 'user_id' => $userInfos['id']]);
//
//    if (! $monthlyToRemember) {
//        return response(['success' => false, 'message' => 'Monthly note not found'], 404);
//    }
//
//    try {
//        $common->update('monthly_to_remembers', ['is_checked' => (int)$request['is_checked']], 'id = :id', ['id' => $monthlyToRemember['id']]);
//    } catch (Exception $e) {
//        return response(['success' => false, 'message' => 'Something went wrong. Please try again.']);
//    }
//
//    return response(['success' => true]);
//}
//
//// delete monthly to_remembers
//if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && ! empty($request['delete_to_remember'])) {
//    $monthlyToRemember = $common->first('monthly_to_remembers', 'id = :id AND user_id = :user_id', ['id' => $request['id'], 'user_id' => $userInfos['id']]);
//
//    if (! $monthlyToRemember) {
//        return response(['success' => false, 'message' => 'Monthly note not found'], 404);
//    }
//
//    try {
//        $common->delete('monthly_to_remembers', 'id = :id', ['id' => $monthlyToRemember['id']]);
//    } catch (Exception $e) {
//        return response(['success' => false, 'message' => 'Something went wrong. Please try again.']);
//    }
//
//    return response(['success' => true]);
//}