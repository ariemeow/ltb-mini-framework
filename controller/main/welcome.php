<?php

class welcome extends controller{
	public function index(){
		$var = array();
		$this->renderTemplate('welcome.html', $var);
	}
}