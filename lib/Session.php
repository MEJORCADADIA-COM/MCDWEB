<?php

use JetBrains\PhpStorm\Pure;

class Session
{
	
	public static function init()
	{
		$lifetime=3600*24*30;
		if (session_status() == PHP_SESSION_NONE) {
			session_set_cookie_params($lifetime);
			session_start();
			setcookie(session_name(),session_id(),time()+$lifetime);
		}
	}

	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public static function get($key)
	{
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		} else {
			return false;
		}
	}

	public static function checkSession()
	{
		self::init();
		if (self::get('login') == false) {
            session_destroy();

            return false;
		}

        return true;
	}

	public static function checkLogin()
	{
		self::init();
		if (self::get('login') == true) {
			header("Location: " . SITE_URL . "/users/index.php");
		}
	}

	public static function destroy()
	{
		session_destroy();
		header("Location: " . SITE_URL);
	}

	public static function adminSession()
	{
		self::init();
		if (self::get('admin_login') == false) {
			session_destroy();

			return false;
		}

        return true;
	}

	public static function adminLogin()
	{
		self::init();
		if (self::get('admin_login') == true) {
			header("Location: " . SITE_URL . "/admin/index.php");
		}
	}

	public static function adminDestroy()
	{

		session_destroy();
		header("Location: " . SITE_URL . "/admin/login.php");
	}

	public static function unset($key)
	{
		unset($_SESSION[$key]);
	}

	#[Pure] public static function hasSuccess(): bool
    {
		return (bool)Session::get('success');
	}

	public static function getSuccess()
	{
		$message = Session::get('success') ?: null;
		Session::unset('success');
		return $message;
	}

	#[Pure] public static function hasError(): bool
    {
		return (bool)Session::get('error');
	}

	public static function getError()
	{
		$message = Session::get('error') ?: null;
		Session::unset('error');
		return $message;
	}
}
