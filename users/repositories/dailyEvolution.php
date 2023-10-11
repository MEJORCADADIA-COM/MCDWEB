<?php

require_once __DIR__ . "/../../helper.php";
require_once  base_path('/classes/Common.php');

$common = new Common();

function getMonthlyDailyToEvolutionWithTags($userId, $month, $year): array
{
    global $common;

      $firstDay = date('d', strtotime("{$year}-{$month}-01"));
      $numOfDays = date('t', strtotime("{$year}-{$month}-01"));
    $evolutions = $common->get(
        'dailygaols',
        'user_id= :user_id AND DATE(created_at) BETWEEN :start_date AND :end_date',
        ['user_id' => $userId, 'start_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$firstDay}")), 'end_date' => date('Y-m-d', strtotime("{$year}-{$month}-{$numOfDays}"))]
    );
    return addTagsToEvolution($evolutions);
}

function addTagsToEvolution($evolutions): array
{
    global $common;

    if (!$evolutions || count($evolutions) === 0) {
        return [];
    }

    $ids = [];
    $results = [];
    foreach ($evolutions as $evolution){
        $ids[] = $evolution['id'];
        $evolution['tags'] = [];
        $results[$evolution['id']] = $evolution;
    }

    $inPlaceHolders = count($ids) >= 1 ? str_repeat('?,', count($ids)-1) . '?' : '?';

    $tags = $common->leftJoin(
        'evolution_user_tag',
        'user_tags',
        'evolution_user_tag.user_tag_id = user_tags.id',
        'evolution_id IN (' . $inPlaceHolders . ')',
        $ids,
        ['user_tags.*', 'evolution_user_tag.evolution_id'],
        'evolution_user_tag.id'
    );

    foreach ($tags as $tag) {
        $results[$tag['evolution_id']]['tags'][] = $tag;
    }

    return array_values($results);
}



function updateEvolutionWithTags($id,$tags,$userId,$bgColor='')
{
    global $common;
    
    if(!empty($bgColor)){
        $common->update('dailygaols', ['color'=>$bgColor], 'id = :id', ['id' => $id]);
    }   
    syncEvolutionTags($id, $userId, $tags);
}


function syncEvolutionTags(int $id, int $userId, array $newTags)
{
    global $common;

    $alreadyTagged = $common->leftJoin(
        'evolution_user_tag',
        'user_tags',
        'evolution_user_tag.user_tag_id = user_tags.id',
        'evolution_user_tag.evolution_id = :evolution_id',
        ['evolution_id' => $id],
        ['evolution_user_tag.id', 'tag', 'user_tags.id as user_tag_id']
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

                $common->insert('evolution_user_tag', ['evolution_id' => $id, 'user_tag_id' => $userTagId]);
            }
        }
    }

    foreach ($alreadyTaggedMap as $tag => $data) {
        if ($data !== true) {
            $common->delete('evolution_user_tag', 'id =:id', ['id' => $data['id']]);
        }
    }
}