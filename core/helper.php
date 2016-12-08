<?php 

include_once 'lib/Validator/Validator.php';
include_once 'lib/Upload/class.upload.php';
include_once 'lib/CURL/autoloader.php';
include_once 'lib/XSSFilter/xss_filter.class.php';

use \Curl\Curl;

class helper{
	public static function issetCheck($var,$default_val){
		return (isset($var) ? self::filterInput($var) : $default_val);
	}
	public static function createID(){
		return hash("md5",uniqid().rand());
	}
	public static function filterInput($input){
		$input = strip_tags($input);
		
		$filter = new xss_filter();
		return $filter->filter_it($input);
	}
	public static function createHashKey($value){
		return hash('crc32b', $value);
	}
	public static function checkHashKey($hash,$value){
		if(sprintf("%u",crc32($value)) == hexdec($hash)){
			return true;
		}
		return false;
	}
	public static function createRandomPassword($lenght = 8){
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array();
		$alphaLength = strlen($alphabet) - 1;
		for ($i = 0; $i <= $lenght; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass);
	}
	public static function varCheck($var){
		echo "<pre>".print_r($var,true)."</pre>";
	}
	public static function uploadImage($image_file){
		$upload_image_path = BASE_PATH."assets/img/";
		
		$foo = new Upload($image_file);
		if ($foo->uploaded) {
			$new_filename = 'upload_'.time();
			$foo->file_new_name_body = $new_filename;
			$foo->image_resize = true;
			$foo->image_x = 200;
			$foo->image_y = 200;
			$foo->image_ratio_crop = true;
// 			$foo->image_ratio_y = true;
			$foo->Process($upload_image_path);
			if (!$foo->processed) {
				return false;
			}
			
			return $new_filename.".".$foo->file_dst_name_ext;
		}else{
			return false;
		}
		
		return false;
	}
	public static function isEmail($value){
		return preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i', $value);
	}
	public static function getURLParse(){
		return explode ( '/', parse_url ( $_SERVER ['REQUEST_URI'], PHP_URL_PATH ) );
	}
}

?>