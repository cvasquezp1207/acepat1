<?php

include_once "Model.php";

class Detalle_compra_model extends Model {

	public function init() {
		$this->set_schema("compra");
	}
	
	public function get_items($idcompra) {
		$sql = "SELECT dp.iddetalle_compra, dp.idproducto, p.descripcion, dp.idunidad, dp.cantidad, 
			dp.precio as precio_compra, p.descripcion_detallada, p.idtipo_producto, 
			dp.afecta_stock as controla_stock, dp.afecta_serie as controla_serie, 
			array_to_string(array_agg(dps.serie), '|'::text) AS serie
			FROM compra.detalle_compra dp 
			INNER JOIN compra.producto p ON p.idproducto = dp.idproducto
			LEFT JOIN compra.detalle_compra_serie dps ON dps.idcompra = dp.idcompra 
				AND dps.iddetalle_compra = dp.iddetalle_compra AND dps.estado = 'A'
			WHERE dp.estado = ? AND dp.idcompra = ?
			GROUP BY dp.iddetalle_compra, dp.idproducto, p.descripcion, dp.idunidad, dp.cantidad, 
				dp.precio, p.descripcion_detallada, p.idtipo_producto, dp.afecta_stock, dp.afecta_serie
			ORDER BY dp.iddetalle_compra";
		
		$query = $this->db->query($sql, array("A", $idcompra));
		return $query->result_array();
	}
}
?>