<?php 
$path = realpath(dirname(__FILE__));
include_once ($path . "/../lib/Database.php");

class Format {
	public $db;

	public function __construct() {
		$this->db = new Database();
	}
	public function dateFormat($date) {
		return date("j-M-Y", strtotime($date));
	}
	public function textShorten($text, $limit) {
		$text = substr($text, 0, $limit);
		$text = substr($text, 0, strrpos($text, " "));
		$text = $text . "....";
		return $text;
	}
	public function validation($data) {
		$data = trim($data);
		$data = stripcslashes($data);
		$data = htmlspecialchars($data);
		$data = mysqli_real_escape_string($this->db->link, $data);
		return $data;
	}
	public function removeAndSlash($value) {
		$data = str_replace('&', '-', $value);
		$data = str_replace('/', '-', $data);
        $data = str_replace(' ', '_', $data);
        return $data;
	}
	public function addAndSlash($value) {
		$data = str_replace('-', '&', $value);
		$data = str_replace('-', '/', $data);
        $data = str_replace('_', ' ', $data);
        return $data;
	}
}

?>