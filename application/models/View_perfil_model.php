<?php

include_once "Model.php";

class View_perfil_model extends Model {

	public function init() {
		$this->set_schema("seguridad");
	}
}