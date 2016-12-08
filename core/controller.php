<?php 

class controller{
	public function __construct(){
		$this->view = new twigLoader();
		$this->css = array();
		$this->js = array();
		$this->add_css = array();
		$this->add_js = array();
	}
	private function loadBaseAssets(){
		// Add Base CSS or JS here
		
	}
	protected static function loadModel($modelname){
		include_once BASE_PATH."model/".$modelname.".php";
		$modelname = $modelname.'_model';
		$model = new $modelname();
		
		return $model;
	}
	protected function renderTemplate($templateName,$var){
		$this->loadBaseAssets();
		$var['css'] = array_merge($this->css,$this->add_css);
		$var['js'] = array_merge($this->js,$this->add_js);
		
		echo $this->view->loadTemplate($templateName, $var);
	}
	protected function getTemplateContent($templateName,$var){
		$var['css'] = $this->css;
		$var['js'] = $this->js;
		
		return $this->view->loadTemplate($templateName, $var);
	}
	protected function redirect($url){
		header("Location:".$url);
	}
}

?>