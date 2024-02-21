<?php

include_once "Controller.php";

class Paginate extends Controller {

	public function init_controller() {
		
	}
	
	public function end_controller() {
		
	}
	
	public function form() {
		return null;
	}
	
	public function grilla() {
		return null;
	}
	
	public function get_data() {
		$req = $this->input->get();		
		$table_name = (empty($req["table_name"])) ? "temp" : $req["table_name"];
		$schema = (empty($req["schema"])) ? '' : $req["schema"];
		$popup = (empty($req["popup_enable"])) ? false : $req["popup_enable"];
		$columns = (empty($req["columns"])) ? array() : json_decode($req["columns"], true);
		$where = (empty($req["where"])) ? array() : json_decode($req["where"], true);
		$indexcolumn = (empty($req["index_column"])) ? '' : $req["index_column"];
		// echo '<pre>';print_r($where);echo '</pre>';return;
		
		$this->load->library('datatables');
		// $this->load_model($table_name);
		$this->load_model($schema.".".$table_name);
		// $this->load_model_generic($schema.".".$table_name);
		$this->datatables->setModel($this->$table_name);

		$this->datatables->setRequest($req);
		$this->datatables->setColumns($columns);
		$this->datatables->setWhere($where);
		// echo $this->datatables->sWhereAdicional;return;
		
		$this->datatables->setIndexColumn($indexcolumn);
		$this->datatables->prepareQuery();
		$this->datatables->executeQuery();
		// echo $this->datatables->getQuery();return;

		// $records = $this->datatables->getRecords($popup);
		$records = $this->datatables->getRecords($popup, true);

		$this->response($records);
	}
}
?>