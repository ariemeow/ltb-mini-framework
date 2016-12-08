<?php

class db{
	private static $dbs;
	private $data;
	
	public function __construct(){
		include_once 'lib/MySQLi/MysqliDb.php';
       	$this->data = json_decode(file_get_contents('./config.json'), true);
       	if($this->data['db_driver'] == "mysql"){
			$this->db = $this->initMySQL();
       	}else if($this->data['db_driver'] == "mongodb"){
			$this->db = $this->initMongoDB();
       	}
	}
	
	protected static function loadModel($modelname){
		include_once BASE_PATH."model/".$modelname.".php";
		$modelname = $modelname.'_model';
		$model = new $modelname();
		
		return $model;
	}
	
	private function initMySQL() {
        if (self::$dbs == null){
			$host = $this->data['db_host'].":".$this->data['db_port'];
			$username = $this->data['db_user'];
			$password = $this->data['db_password'];
			$dbname = $this->data['db_name'];
			
			$db = new MysqliDb ($host,$username,$password,$dbname);	
			self::$dbs = $db;
		}
        return self::$dbs;
    }

    protected function get($tablename,$where,$order,$limit,$offset = 0){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	foreach ($order as $key => $ord){
    		$key = $this->db->escape($key);
    		$this->db->orderBy($key,$ord);
    	}
    	$data = $this->db->withTotalCount()->get($tablename,array($offset,$limit));
    	return $data;
    }
	protected function getAll($tablename,$order){
    	foreach ($order as $key => $ord){
    		$key = $this->db->escape($key);
    		$this->db->orderBy($key,$ord);
    	}
		$data = $this->db->get($tablename);
		return $data;
	}
    protected function getOne($tablename,$where,$field='*'){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	$data =  $this->db->getOne($tablename,$field);
    	return $data;
    }
	protected function checkDuplicate($tablename,$field,$value){
		$where_query = array();
		$where['key'] = $this->db->escape($field);
		$where['value'] = $this->db->escape($value);
		$where['operator'] = '=';
		$where_query[] = $where;

		foreach ($where_query as $wr){
			$this->db->where($wr['key'],$wr['value'],$wr['operator']);
		}
		$check =  $this->db->has($tablename);
		return $check;
	}
	protected function insertData($tablename,$data){
		$filtered_data = array();
		foreach ($data as $key => $value){
			$key = $this->db->escape($key);
			$value = $this->db->escape($value);
			$filtered_data[$key] = $value;
		}
		return $this->db->insert($tablename, $filtered_data);
	}
	protected function updateData($tablename,$where,$new_data){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	return $this->db->update($tablename,$new_data);
	}
    protected function count($tablename,$where){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	$data = $this->db->withTotalCount()->get($tablename);
		return $this->db->totalCount;
    }
	protected function countAll($tablename){
		return $this->db->getValue($tablename, "count(*)");
	}
	protected function deleteData($tablename,$where){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	return $this->db->delete($tablename);
	}
	protected function checkExists($tablename,$where){
    	foreach ($where as $wr){
    		$key = $this->db->escape($wr['key']);
    		$value = $this->db->escape($wr['value']);
    		$this->db->where($key,$value,$wr['operator']);
    	}
    	return $this->db->has($tablename);
	}
	public function getErrorMessage(){
		return $this->db->getLastError();
	}
	public function getLastQueryCount(){
		return $this->db->totalCount;
	}
	
	private function initMongoDB() {
        if (self::$dbs == null){
			$server = $this->data['db_host'].":".$this->data['db_port'];
			$m = new MongoClient($server);		
			self::$dbs = $m->$this->data['db_name'];
		}
        return self::$dbs;
    }
}

?>