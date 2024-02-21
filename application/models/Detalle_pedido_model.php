<?php

include_once "Model.php";

class Detalle_pedido_model extends Model {

	public function init() {
		$this->set_schema("compra");
	}
	
	public function get_item_pedido($idpedido) {
		$sql = "SELECT dp.idproducto, p.descripcion, dp.idunidad, dp.cantidad, p.descripcion_detallada
			FROM compra.detalle_pedido dp 
			INNER JOIN compra.producto p ON p.idproducto = dp.idproducto
			WHERE dp.estado = ? AND dp.idpedido = ?";
		
		$query = $this->db->query($sql, array("A", $idpedido));
		return $query->result_array();
	}
}
?>