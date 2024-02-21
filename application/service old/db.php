<?php

class Db {
	protected $link = null;
	protected $result_set = null;
	protected $error_msg = "";
	
	public function __construct($path = FALSE) {
		if($path !== FALSE)
			$this->open($path);
	}
	
	public function open($path) {
		$this->link = new SQLite3($path);
	}
	
	protected function prepare_rs($resultSet) {
		$this->result_set = array();
		while($row = $resultSet->fetchArray(SQLITE3_ASSOC)) {
			$this->result_set[] = $row;
		}
	}
	
	public function query($sql) {
		$results = $this->link->query($sql);
		if($results === false) {
			$this->error_msg = $this->link->lastErrorMsg();
			return false;
		}
		
		if($results !== true) {
			$this->prepare_rs($results);
		}
		
		return true;
	}
	
	public function get_error() {
		return $this->error_msg;
	}
	
	public function get_array() {
		if(is_array($this->result_set) && count($this->result_set) > 0) {
			return $this->result_set[0];
		}
		return array();
	}
	
	public function get_all() {
		if(is_array($this->result_set))
			return $this->result_set;
		return array();
	}
	
	public function get_value($col=0, $row=0) {
		if(is_array($this->result_set)) {
			if(count($this->result_set) > $row) {
				$array = $this->result_set[$row];
				if(array_key_exists($col, $array))
					return $array[$col];
			}
		}
			
		return null;
	}
	
	public function get_num_rows() {
		return count($this->result_set);
	}
}

?>