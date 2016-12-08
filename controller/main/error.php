<?php

class error extends controller{
	public function __construct(){
		parent::__construct();
	}
	public function errno_404(){
		header('Content-type: application/json');
		$return = array();
		$return['errno'] = 404;
		$return['message'] = 'Not Found';
		echo json_encode($return);
	}
}