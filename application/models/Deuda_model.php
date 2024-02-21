<?php
include_once "Model.php";

class Deuda_model extends Model {
	public function init() {
		$this->set_schema("compra");
	}
	/**
	 * Comprobar si el credito tiene alguna amortizacion
	 */
	public function has_amortizacion($idcredito) {
		$query = $this->db->where("idcredito", $idcredito)->where("estado", "A")->get("credito.amortizacion");
		return ($query->num_rows() >= 1);
	}
	
	/**
	 * Obtener la lista de creditos segun cliente 
	 */
	public function get_creditos($idcliente, $pagado = null) {
		$this->db->where("idcliente", $idcliente);
		$this->db->where("estado", "A");
		if($pagado !== null) {
			$this->db->where("pagado", $pagado);
		}
		$this->db->order_by("fecha_credito");
		$query = $this->db->get("credito.credito");
		return $query->result_array();
	}
	
	public function get_creditos_cancelados($idcliente) {
		return $this->get_creditos($idcliente, "S");
	}
}
?>