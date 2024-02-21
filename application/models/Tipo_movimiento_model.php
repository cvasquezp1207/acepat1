<?php

include_once "Model.php";

class Tipo_movimiento_model extends Model {

	public function init() {
		$this->set_schema("almacen");
	}
}
?>