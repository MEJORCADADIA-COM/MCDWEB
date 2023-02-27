<?php

$filepath = realpath(dirname(__FILE__));
include_once($filepath . '/../../classes/Common.php');
include_once($filepath . '/../../lib/Session.php');
include_once($filepath . '/../../lib/RememberCookie.php');

$userInfos = null;

function isNotAuthenticated(): bool
{
    global $userInfos;

    $common = new Common();
    $rememberCookieData = RememberCookie::getRememberCookieData();
    if ($rememberCookieData) {
        if ($rememberCookieData[RememberCookie::PASSWORD]) {
            $passwordComparator = "=";
        } else {
            $passwordComparator = "IS";
        }
        $userInfos = $common->first(
            "`users`",
            "`id` = :id AND password {$passwordComparator} :password AND remember_token = :remember_token",
            ['id' => $rememberCookieData[RememberCookie::ID], 'remember_token' => $rememberCookieData[RememberCookie::REMEMBER_TOKEN], 'password' => $rememberCookieData[RememberCookie::PASSWORD]]
        );
    }

    if (!$userInfos && Session::get('user_id') !== NULL) {
        $user_id = Session::get('user_id');
        $userInfos = $common->first("`users`", "`id` = :id", ['id' => $user_id]);
    }

    return (! Session::checkSession() && ! $userInfos);
}