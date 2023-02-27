<?php
$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/classes/Common.php');
$common = new Common();

$dailyVictoryTags = $common->leftJoin(
    'daily_victory_user_tag',
    'user_tags',
    'daily_victory_user_tag.user_tag_id = user_tags.id',
    'daily_victory_user_tag.daily_victory_id = :victory_id',
    ['victory_id' => 5],
    ['daily_victory_user_tag.id', 'tag', 'user_tags.id as user_tag_id']
);

dd($dailyVictoryTags);