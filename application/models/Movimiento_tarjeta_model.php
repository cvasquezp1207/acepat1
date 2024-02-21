<?php

include_once "Model.php";

class Movimiento_tarjeta_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
}
?>