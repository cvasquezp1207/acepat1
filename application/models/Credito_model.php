<?php

include_once "Model.php";

class Credito_model extends Model {

	public function init() {
		$this->set_schema("credito");
	}
	
	/**
	 * Comprobar si existe el credito a registrar
	 */
	public function is_active($idempresa, $nro_credito) {
		$sql = "SELECT * FROM credito.credito WHERE estado='A' AND idsucursal IN (
				SELECT idsucursal FROM seguridad.sucursal WHERE idempresa=?
			) AND nro_credito=?";
		$query = $this->db->query($sql, array($idempresa, $nro_credito));
		return ($query->num_rows() > 0);
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
	public function get_view_creditos($idcliente, $pagado = null) {
		$this->db->where("idcliente", $idcliente);
		$this->db->where("idsucursal", $this->get_var_session("idsucursal"));
		$this->db->where("estado", "A");
		if($pagado !== null) {
			$this->db->where("pagado", $pagado);
		}
		$query = $this->db->get("credito.credito_view");
		return $query->result_array();
	}
	/**
	 * Obtener la lista de creditos cancelados segun cliente 
	 */
	public function get_creditos_cancelados($idcliente) {
		return $this->get_creditos($idcliente, "S");
	}
	
	/**
	 * Obtener la lista de creditos pendientes segun cliente 
	 */
	public function get_creditos_pendientes($idcliente) {
		return $this->get_creditos($idcliente, "N");
	}
	public function get_creditos_pendientes_($idcliente,$pagado='N') {
		return $this->get_view_creditos($idcliente, $pagado);
	}
	
	/**
	 * Obtener la cantidad de letras canceladas de un credito
	 */
	public function letras_canceladas($idcredito, $cancelado="S") {
		$cancelado = strtoupper($cancelado);
		if($cancelado != "S" && $cancelado != "N") {
			$cancelado = "S";
		}
		
		$query = $this->db->where("idcredito", $idcredito)->where("pagado", $cancelado)
			->where("estado", "A")->get("credito.letra");
		
		return $query->num_rows();
	}
	
	/**
	 * Obtener la cantidad de letras pendientes de un credito
	 */
	public function letras_pendientes($idcredito) {
		return $this->letras_canceladas($idcredito, "N");
	}
}
?>