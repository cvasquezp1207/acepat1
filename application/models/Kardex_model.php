<?php

include_once "Model.php";

class Kardex_model extends Model {

	public function init() {
		$this->set_schema("almacen");
	}
}
?>