<?php
date_default_timezone_set ( "Asia/Jakarta" );
set_error_handler('exceptions_error_handler');
define ( 'BASE_PATH', realpath ( dirname ( __FILE__ ) ) . DIRECTORY_SEPARATOR );
define ( 'BASE_URL', "http://" . $_SERVER ['HTTP_HOST'] );

function is_session_started() {
	if (php_sapi_name () !== 'cli') {
		if (version_compare ( phpversion (), '5.4.0', '>=' )) {
			return session_status () === PHP_SESSION_ACTIVE ? TRUE : FALSE;
		} else {
			return session_id () === '' ? FALSE : TRUE;
		}
	}
	return FALSE;
}

function exceptions_error_handler($severity, $message, $filename, $lineno) {
	if (error_reporting() == 0) {
		return;
	}
	if (error_reporting() & $severity) {
		$return = array(
			'result' => 'ERROR',
			'message' => 'Fatal Error',
			'error_message' => $message,
			'line' => $lineno,
			'file' => $filename
		);
		echo json_encode($return);
		die();
	}
}

if (is_session_started () === FALSE) {
	session_start ();
}

class index {
	public function __construct() {
		$this->request = $this->parseUrl ();
		include BASE_PATH . "core/db.php";
		include BASE_PATH . "core/twigLoader.php";
		include BASE_PATH . "core/controller.php";
		include BASE_PATH . "core/helper.php";
	}
	private function parseUrl() {
		$result = array ();
		$req_url = parse_url ( $_SERVER ['REQUEST_URI'], PHP_URL_PATH );
		
		$result ['path'] = explode ( '/', $req_url );
		$result ['count'] = count ( $result ['path'] );
		
		if (($result ['count'] == 2 || $result ['count'] == 1) && $result ['path'] ['1'] == null) {
			$result ['page'] = 'index';
		}
		
		return $result;
	}
	public function main() {
		$request_url = $this->request;
		if (isset ( $request_url ['page'] ) && $request_url ['page'] === 'index') {
			include BASE_PATH . "controller".DIRECTORY_SEPARATOR."main".DIRECTORY_SEPARATOR."welcome.php";
			$welcome = new welcome();
			$welcome->index ();
			exit ();
		} else {
			$handled = false;
			if (file_exists ( BASE_PATH . "controller".DIRECTORY_SEPARATOR."main".DIRECTORY_SEPARATOR . $request_url ['path'] ['1'] . ".php" )) {
				include BASE_PATH . "controller".DIRECTORY_SEPARATOR."main".DIRECTORY_SEPARATOR. $request_url ['path'] ['1'] . ".php";
				$controller = new $request_url ['path'] ['1'] ();
				$method = (isset( $request_url ['path'] ['2']) ?  $request_url ['path'] ['2'] : "index");
				if (method_exists ( $controller, $method )) {
					$controller->$method();
					$handled = true;
					exit ();
				}
			}
			if (! $handled) {
				$this->error ( 404 );
			}
		}
	}
	public function error($errNo) {
		include_once BASE_PATH."controller".DIRECTORY_SEPARATOR."main".DIRECTORY_SEPARATOR."error.php";
		$error = new error();
		$method_name = "errno_".$errNo;
		if(method_exists($error, $method_name)){
			$error->$method_name();
		}else{
			header('Content-type: application/json');
			$return = array(
				'result' => 'FATAL ERROR'
			);
			echo json_encode($return);
		}
	}
}

$index = new index ();
$index->main ();

?>