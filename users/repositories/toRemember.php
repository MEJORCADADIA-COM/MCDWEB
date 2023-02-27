<?php

require_once __DIR__ . "/../../helper.php";
require_once  base_path('/classes/Common.php');

$common = new Common();

function getMonthlyDailyToRememberWithTags($userId, $month, $year): array
{
    global $common;

    $firstDay = date('N', strtotime("{$year}-{$month}-01"));
    $numOfDays = date('t', strtotime("{$year}-{$month}-01"));

    $toRemembers = $common->get(
        'to_remember',
        'user_id = :user_id AND date BETWEEN :start_date AND :end_date',
        ['user_id' => $userId, 'start_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$firstDay}")), 'end_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$numOfDays}"))]
    );

    return addTagsToRemember($toRemembers);
}

function addTagsToRemember($toRemembers): array
{
    global $common;

    if (!$toRemembers || count($toRemembers) === 0) {
        return [];
    }

    $ids = [];
    $results = [];
    foreach ($toRemembers as $toRemember){
        $ids[] = $toRemember['id'];
        $toRemember['tags'] = [];
        $results[$toRemember['id']] = $toRemember;
    }

    $inPlaceHolders = count($ids) >= 1 ? str_repeat('?,', count($ids)-1) . '?' : '?';

    $tags = $common->leftJoin(
        'to_remember_user_tag',
        'user_tags',
        'to_remember_user_tag.user_tag_id = user_tags.id',
        'to_remember_id IN (' . $inPlaceHolders . ')',
        $ids,
        ['user_tags.*', 'to_remember_user_tag.to_remember_id']
    );

    foreach ($tags as $tag) {
        $results[$tag['to_remember_id']]['tags'][] = $tag;
    }

    return array_values($results);
}

function updateToRemember($userId, $toRememberId, $toRemember)
{
    global $common;
    return $common->update('to_remember', ['to_remember' => $toRemember], 'id = :id AND user_id = :user_id', ['id' => $toRememberId, 'user_id' => $userId]);
}

function updateToRememberWithTags($toRememberId, $toRemember, $newToRememberTags, $userId)
{
    global $common;

    $common->update('to_remember', ['to_remember' => $toRemember], 'id = :id', ['id' => $toRememberId]);
    syncToRememberTags($toRememberId, $userId, $newToRememberTags);
}

function addToRememberWithTags($toRemember, $toRememberTags, $userId, $currentDate) {
    global $common;

    $common->insert('to_remember', ['to_remember' => $toRemember, 'user_id' => $userId, 'date' => $currentDate]);
    $toRememberId = $common->insertId();

    syncToRememberTags($toRememberId, $userId, $toRememberTags);
}

function syncToRememberTags(int $toRememberId, int $userId, array $newTags)
{
    global $common;

    $alreadyTagged = $common->leftJoin(
        'to_remember_user_tag',
        'user_tags',
        'to_remember_user_tag.user_tag_id = user_tags.id',
        'to_remember_user_tag.to_remember_id = :to_remember_id',
        ['to_remember_id' => $toRememberId],
        ['to_remember_user_tag.id', 'tag', 'user_tags.id as user_tag_id']
    );

    $alreadyTaggedMap = [];
    foreach ($alreadyTagged as $tag) {
        $alreadyTaggedMap[$tag['tag']] = $tag;
    }

    foreach ($newTags as $tag) {
        $tag = strtolower(trim($tag));
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

                $common->insert('to_remember_user_tag', ['to_remember_id' => $toRememberId, 'user_tag_id' => $userTagId]);
            }
        }
    }

    foreach ($alreadyTaggedMap as $tag => $data) {
        if ($data !== true) {
            $common->delete('to_remember_user_tag', 'id =:id', ['id' => $data['id']]);
        }
    }
}