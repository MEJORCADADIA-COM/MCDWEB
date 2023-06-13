<?php
require_once 'inc/commonIncludes.php';
require_once '../repositories/dailyVictories.php';

header("Content-Type: application/json");

if (isNotAuthenticated()) {
    return response(['success' => false, 'message' => 'You are unauthenticated'], 401);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['get_victories']) && $_GET['get_victories'] === 'get_victories') {
    if (isset($_GET['tag']) && !empty($_GET['tag'])) {
        $tags = $common->leftJoinPaginate(
            'daily_victory_user_tag',
            'user_tags',
            'daily_victory_user_tag.user_tag_id = user_tags.id',
            'user_tags.user_id = :user_id AND user_tags.tag like :tag',
            ['user_id' => $userInfos['id'], 'tag' => '%' . $_GET['tag'] . '%'],
            ['daily_victory_user_tag.daily_victory_id'],
            'daily_victory_user_tag.daily_victory_id',
            'desc',
            10,
            'daily_victory_user_tag.daily_victory_id'
        );

        $totalPage = $tags['total_page'];

        $victories = null;
        if ($tags['data']) {
            $dailyVictoryIds = array_column($tags['data'], 'daily_victory_id');
            $inPlaceHolders = count($dailyVictoryIds) >= 1 ? str_repeat('?,', count($dailyVictoryIds)-1) . '?' : '?';
            $victories = $common->get(table: 'daily_victories', cond: 'id IN (' . $inPlaceHolders . ')', params: $dailyVictoryIds, orderBy: 'date', order: 'DESC');
        }
    } else {
        $victories = $common->paginate(table: 'daily_victories', cond: 'user_id = :user_id', params: ['user_id' => $userInfos['id']], orderBy: 'date', order: 'desc');
        $totalPage = $common->pageCount(table: 'daily_victories', cond: 'user_id = :user_id', params: ['user_id' => $userInfos['id']]);
    }
    setlocale(LC_ALL, "es_ES");
    foreach($victories as $k=>$row){
        $string = date('d/m/Y', strtotime($row['date']));
        $dateObj = DateTime::createFromFormat("d/m/Y", $string);
        $row['local_date']=utf8_encode(strftime("%A, %d %B, %Y", $dateObj->getTimestamp()));
        $victories[$k]=$row;
    }

    $results = addTagsToVictories($victories);


    return response(['success' => true, 'data' => ['victories' => $results, 'total_page' => $totalPage, 'current_page' => !empty($_GET['page']) ? (int)$_GET['page'] : 1]]);
}