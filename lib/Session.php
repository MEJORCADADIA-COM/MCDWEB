<?php 

class Session {
	public static function init() {
		if (version_compare(phpversion(), '5.4.0', '<')) {
			if (session_id() == '') {
				session_start();
			}
		} else {
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
		}
	}

	public static function set($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	public static function get($key) {
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		} else {
			return false;
		}
	}
	public static function checkSession() {
		self::init();
		if (self::get('login') == false) {
			session_destroy();
			header("Location: https://mejorcadadia.com/");
		}
	}
	public static function checkLogin() {
		self::init();
		if (self::get('login') == true) {
			header("Location: https://mejorcadadia.com/users/index.php");
		}
	}
	public static function destroy() {
		session_destroy();
		header("Location: https://mejorcadadia.com/");
	}
  
  	public static function adminSession() {
		self::init();
		if (self::get('admin_login') == false) {
			session_destroy();
			header("Location: https://mejorcadadia.com/admin/login.php");
		}
	}
	public static function adminLogin() {
		self::init();
		if (self::get('admin_login') == true) {
			header("Location: https://mejorcadadia.com/admin/index.php");
		}
	}
	public static function adminDestroy() {
		session_destroy();
		header("Location: https://mejorcadadia.com/admin/login.php");
	}
  
	public static function unset($key) {
		unset($_SESSION[$key]);
	}
}

?>