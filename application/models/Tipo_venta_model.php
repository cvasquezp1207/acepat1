<?php

include_once "Model.php";

class Tipo_venta_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
}
?>