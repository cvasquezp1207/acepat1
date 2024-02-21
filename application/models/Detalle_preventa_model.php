<?php

include_once "Model.php";

class Detalle_preventa_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
	
	public function get_items($idpreventa, $idsucursal = NULL) {
		if(empty($idsucursal))
			$idsucursal = $this->ci->get_var_session("idsucursal");
		
		$sql = "SELECT dv.iddetalle_preventa as iddetalle, dv.idproducto, p.descripcion_detallada, 
			dv.idunidad, dv.cantidad, coalesce(dv.precio,0.00) as precio, p.tipo, p.controla_serie, p.controla_stock,
			dv.idalmacen, dv.serie, dv.oferta, dv.codgrupo_igv, dv.codtipo_igv, pu.precio_compra, 
			coalesce(pu.precio_venta,0.00) as precio_venta
			FROM venta.detalle_preventa dv
			JOIN compra.producto p ON p.idproducto = dv.idproducto
			LEFT JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal=?
			WHERE dv.estado = ? AND dv.idpreventa = ?
			ORDER BY iddetalle_preventa";
		
		$query = $this->db->query($sql, array($idsucursal, "A", $idpreventa));
		return $query->result_array();
	}
}
?>