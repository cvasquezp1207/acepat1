<?php

include_once "Model.php";

class Visita_model extends Model {

	public function init() {
		$this->set_schema("cobranza");
	}
}
?>