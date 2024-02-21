<?php

include_once "Model.php";

class Cliente_view_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
}
?>