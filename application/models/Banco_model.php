<?php

include_once "Model.php";

class Banco_model extends Model {

	public function init() {
		$this->set_schema("general");
	}
}
?>