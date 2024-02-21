<?php

include_once "Model.php";

class Personas_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
	
    public function obtener_cliente_id($id){
		// $query = $this->db->get('seguridad.perfil', 10);
        // return $query->result();
	}

	public function obtener_cliente(){
        $query = $this->db->get('venta.cliente');
        return $query->result();
	}
	
	
}
?>