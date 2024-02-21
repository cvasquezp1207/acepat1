<?php

include_once "Model.php";

class Empresa_model extends Model {

	public function init() {
		$this->set_schema("seguridad");
	}
}
?>