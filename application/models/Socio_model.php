<?php

include_once "Model.php";

class Socio_model extends Model {

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
	
	public function saldo($idcliente) {
		$idempresa	= $this->get_var_session("idempresa");
		$sql = "SELECT SUM(total - pagado) as pendiente
			FROM (
				SELECT c.monto_credito as total, COALESCE(SUM(a.monto),0) AS pagado
				FROM credito.credito c
				LEFT JOIN credito.amortizacion a ON a.idcredito=c.idcredito 
				AND a.estado=?
				JOIN seguridad.sucursal seg ON seg.idsucursal = c.idsucursal
				WHERE c.estado=? AND c.pagado=? AND c.idcliente=? AND seg.idempresa=?
				group by c.monto_credito
			) AS sq";
		$query = $this->db->query($sql, array("A", "A", "N", $idcliente,$idempresa));

		//$sql2 = ""
		$query2 = $this->db->query("	SELECT 
											monto 
									FROM credito.ampliar_linea_credito WHERE estado='A' AND f_desde<=CURRENT_DATE AND f_hasta>=CURRENT_DATE AND idcliente='{$idcliente}';");
		$row2 = $query2->row_array();
		
		$ampliacion = $row2["monto"];
		
		if ($query2->num_rows() > 0) :
			return $ampliacion;
		else:	
			if ($query->num_rows() > 0) {
				$row = $query->row_array();
				$en_credito = $credito = $row["pendiente"];
				
				$query = $this->db->get_where("venta.cliente_lineadecredito", array("idcliente"=>$idcliente,"idempresa"=>$idempresa));
				if($query->num_rows() > 0) {
					$row = $query->row_array();
					$credito = $row["limite_credito"];
				}
				
				return ($credito - $en_credito);
			}
			
			
		endif;	
		return 0;
	}
}
?>