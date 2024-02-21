<?php

include_once "Model.php";

class Detalle_notacredito_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
	
	public function get_items($idnotacredito, $all = FALSE) {
		$sql = "SELECT dv.iddetalle_notacredito as iddetalle, dv.idproducto, dv.descripcion as producto, 
			dv.idunidad, dv.cantidad, dv.precio, dv.afecta_serie as controla_serie, dv.serie,
			dv.afecta_stock as controla_stock, dv.idalmacen, u.descripcion as unidad,
			dv.cantidad * dv.precio as importe, dv.codgrupo_igv, dv.codtipo_igv
			FROM venta.detalle_notacredito dv
			INNER JOIN compra.unidad u on u.idunidad = dv.idunidad
			WHERE dv.idnotacredito = ?";
		
		if( ! $all)
			$sql .= " AND dv.estado = 'A'";
		
		$sql .= " ORDER BY iddetalle";
		
		$query = $this->db->query($sql, array($idnotacredito));
		return $query->result_array();
	}
}
?>