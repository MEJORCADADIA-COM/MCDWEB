<?php
require_once 'inc/commonIncludes.php';
require_once base_path('/users/repositories/toRemember.php');

header("Content-Type: application/json");

if (isNotAuthenticated()) {
    return response(['success' => false, 'message' => 'You are unauthenticated'], 401);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['to_remember']) && $_GET['to_remember'] === 'to_remember') {
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tags = $common->leftJoinPaginate(
            'to_remember_user_tag',
            'user_tags',
            'to_remember_user_tag.user_tag_id = user_tags.id',
            'user_tags.user_id = :user_id AND user_tags.tag like :tag',
            ['user_id' => $userInfos['id'], 'tag' => '%' . $_GET['tag'] . '%'],
            ['to_remember_user_tag.to_remember_id'],
            'to_remember_user_tag.to_remember_id',
            'desc',
            10,
            'to_remember_user_tag.to_remember_id'
        );

        $totalPage = $tags['total_page'];

        $toRemember = null;
        if ($tags['data']) {
            $toRememberIds = array_column($tags['data'], 'to_remember_id');
            $inPlaceHolders = count($toRememberIds) >= 1 ? str_repeat('?,', count($toRememberIds)-1) . '?' : '?';
            $toRemember = $common->get(table: 'to_remember', cond: 'id IN (' . $inPlaceHolders . ')', params: $toRememberIds, orderBy: 'id', order: 'desc');
        }
    } else {
        $toRemember = $common->paginate(table: 'to_remember', cond: 'user_id = :user_id', params: ['user_id' => $userInfos['id']], orderBy: 'id', order: 'desc');
        $totalPage = $common->pageCount(table: 'to_remember', cond: 'user_id = :user_id', params: ['user_id' => $userInfos['id']]);
    }

    $results = addTagsToRemember($toRemember);

    return response([
        'success' => true,
        'data' => [
            'to_remember' => array_values($results),
            'total_page' => $totalPage,
            'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1,
        ]
    ]);
}