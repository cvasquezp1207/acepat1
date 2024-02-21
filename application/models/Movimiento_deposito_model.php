<?php

include_once "Model.php";

class Movimiento_deposito_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
}
?>