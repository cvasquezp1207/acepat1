<?php
include_once "Model.php";
class Acceso_sistema_model extends Model {

    private $query;
	private $data = array();
	
	public function init() {
        $this->set_schema("seguridad");
    }
}
?>