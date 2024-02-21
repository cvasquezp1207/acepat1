<?php

include_once "Model.php";

class Tipo_movi_almacen_model extends Model {

	public function init() {
		$this->set_table_name("almacen.tipo_movimiento");
	}
}
?>