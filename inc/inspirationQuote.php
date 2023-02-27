<?php
$filepath = realpath(dirname(__FILE__));
include_once ($filepath . '/../lib/Database.php');
include_once ($filepath . '/../classes/Common.php');

$common = new Common();

function getInspirationQuote()
{
    global $common;
    $date = date('Y-m-d', time());
    $inspirationQuote = $common->first('daily_inspirations', 'date = :date', ['date' => $date], ['inspiration_quote']);
    return $inspirationQuote['inspiration_quote'] ?? '';
}