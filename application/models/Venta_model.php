<?php

include_once "Model.php";

class Venta_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
	
	public function has_despacho($idventa) {
		$sql = "SELECT * FROM almacen.despacho
			WHERE estado <> ? AND referencia=? AND idreferencia=?";
		
		$query = $this->db->query($sql, array("I", "V", $idventa));
		return ($query->num_rows() > 0);
	}
}
?>