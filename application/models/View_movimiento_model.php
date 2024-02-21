<?php

include_once "Model.php";

class View_movimiento_model extends Model {

    public function init() {
		$this->set_schema("caja");
	}
}
?>