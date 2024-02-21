<?php
include_once "Model.php";


class Consulta_model extends Model {
 
   public function init() {
		$this->set_schema("compra");
	} 
	
    /* Devuelve la lista de alumnos que se encuentran en la tabla tblalumno */
    function obtenerListaAlumnos(){
      $this->load->database();
        $alumnos = $this->db->get('compra.pedido');
        return $alumnos->result();
    }
}
?>;