<?php

require_once __DIR__ . "/../../helper.php";
require_once base_path('/vendor/autoload.php');
require_once  base_path('/classes/Common.php');

use Users\ajax\exceptions\InvalidTagException;


$common = new Common();

function getMonthlyVictoriesWithTags($userId, $month, $year): array
{
    global $common;

    $firstDay = date('j', strtotime("{$year}-{$month}-01"));
    $numOfDays = date('t', strtotime("{$year}-{$month}-01"));

    $victories = $common->get(
        'daily_victories',
        'user_id = :user_id AND date BETWEEN :start_date AND :end_date',
        ['user_id' => $userId, 'start_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$firstDay}")), 'end_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$numOfDays}"))]
    );

    return addTagsToVictories($victories);
}

function addTagsToVictories($victories): array
{
    global $common;

    if (!$victories || count($victories) === 0) {
        return [];
    }

    $ids = [];
    $results = [];
    foreach ($victories as $victory){
        $ids[] = $victory['id'];
        $victory['tags'] = [];
        $results[$victory['id']] = $victory;
    }

    $inPlaceHolders = count($ids) >= 1 ? str_repeat('?,', count($ids)-1) . '?' : '?';

    $tags = $common->leftJoin(
        'daily_victory_user_tag',
        'user_tags',
        'daily_victory_user_tag.user_tag_id = user_tags.id',
        'daily_victory_id IN (' . $inPlaceHolders . ')',
        $ids,
        ['user_tags.*', 'daily_victory_user_tag.daily_victory_id']
    );

    foreach ($tags as $tag) {
        $results[$tag['daily_victory_id']]['tags'][] = $tag;
    }

    return array_values($results);
}

function updateVictory($userId, $victoryId, $victory)
{
    global $common;
    return $common->update('daily_victories', ['daily_victory' => $victory], 'id = :id AND user_id = :user_id', ['id' => $victoryId, 'user_id' => $userId]);
}

function updateVictoryWithTags($victoryId, $dailyVictory, $newVictoryTags, $userId,$bgColor='')
{
    global $common;

    
    if(!empty($bgColor)){
        $common->update('daily_victories', ['daily_victory' => $dailyVictory,'color'=>$bgColor], 'id = :id', ['id' => $victoryId]);
    }else{
        $common->update('daily_victories', ['daily_victory' => $dailyVictory], 'id = :id', ['id' => $victoryId]);
    }
    syncVictoryTags($victoryId, $userId, $newVictoryTags);
}

function addVictoryWithTags($dailyVictory, $dailyVictoryTags, $userId, $currentDate) {
    global $common;

    $common->insert('daily_victories', ['daily_victory' => $dailyVictory, 'user_id' => $userId, 'date' => $currentDate]);
    $dailyVictoryId = $common->insertId();

    syncVictoryTags($dailyVictoryId, $userId, $dailyVictoryTags);
}

function syncVictoryTags(int $victoryId, int $userId, array $newTags)
{
    global $common;

    $alreadyTagged = $common->leftJoin(
        'daily_victory_user_tag',
        'user_tags',
        'daily_victory_user_tag.user_tag_id = user_tags.id',
        'daily_victory_user_tag.daily_victory_id = :victory_id',
        ['victory_id' => $victoryId],
        ['daily_victory_user_tag.id', 'tag', 'user_tags.id as user_tag_id']
    );

    $alreadyTaggedMap = [];
    foreach ($alreadyTagged as $tag) {
        $alreadyTaggedMap[$tag['tag']] = $tag;
    }

    foreach ($newTags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            if (isset($alreadyTaggedMap[$tag])) {
                $alreadyTaggedMap[$tag] = true;
            } else {
                $userTag = $common->first('user_tags', 'user_id = :user_id AND tag = :tag', ['user_id' => $userId, 'tag' => $tag]);
                if ($userTag) {
                    $userTagId = $userTag['id'];
                } else {
                    $common->insert('user_tags', ['user_id' => $userId, 'tag' => $tag]);
                    $userTagId = $common->insertId();
                }

                $common->insert('daily_victory_user_tag', ['daily_victory_id' => $victoryId, 'user_tag_id' => $userTagId]);
            }
        }
    }

    foreach ($alreadyTaggedMap as $tag => $data) {
        if ($data !== true) {
            $common->delete('daily_victory_user_tag', 'id =:id', ['id' => $data['id']]);
        }
    }
}