<?php

include_once "Model.php";

class Proveedor_model extends Model {

	public function init() {
		$this->set_schema("compra");
	}
	/* funciones de gabriel*/
    public function obtener_cliente_id($id){
		// $query = $this->db->get('seguridad.perfil', 10);
        // return $query->result();
	}

	public function obtener_cliente(){
        $query = $this->db->get('compra.proveedor');
        return $query->result();
	}
}
?>