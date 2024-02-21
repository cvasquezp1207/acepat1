<?php

include_once "Model.php";

class Pedido_model extends Model {

	public function init() {
		$this->set_schema("compra");
	}
	
	public function get_detalle($where) {
		$sql = "SELECT d.iddetalle_pedido, d.idpedido, d.idproducto, d.idunidad, d.cantidad,
			p.descripcion, u.descripcion as unidad_medida, u.abreviatura, p.idtipo_producto, 
			p.controla_serie, p.controla_stock, p.precio_compra, p.descripcion_detallada
			FROM compra.detalle_pedido d
			JOIN compra.producto p on p.idproducto = d.idproducto
			JOIN compra.unidad u on u.idunidad = d.idunidad";
		
		$filtros = array_keys($where);
		$values = array_values($where);
		
		if(!empty($filtros)) {
			$cols = array();
			foreach($filtros as $c) {
				$cols[] = "$c = ?";
			}
			$sql .= " WHERE ".implode(" AND ", $cols);
		}

		// ECHO $sql;
		$query = $this->db->query($sql, $values);
		return $query->result_array();
	}
}
?>