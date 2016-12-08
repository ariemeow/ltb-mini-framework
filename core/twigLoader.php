<?php

class twigLoader{
	public function __construct(){
		include_once 'lib/Twig/Autoloader.php';
		Twig_Autoloader::register();
		$this->loader = new Twig_Loader_Filesystem('view');
		$this->twig = new Twig_Environment($this->loader);
	}
	public function loadTemplate($templateName,$value){
		$template = $this->twig->loadTemplate($templateName);
		return $template->render($value);
	}
}

?>