<?php

include_once "Model.php";

class Detalle_venta_model extends Model {

	public function init() {
		$this->set_schema("venta");
	}
	
	public function get_items($idventa, $all = FALSE, $idsucursal = NULL) {
		if(empty($idsucursal))
			$idsucursal = $this->ci->get_var_session("idsucursal");
		
		$sql = "SELECT dv.iddetalle_venta as iddetalle, dv.idproducto, dv.descripcion as descripcion_detallada, 
			dv.idunidad, dv.cantidad, dv.precio, dv.despachado, p.tipo, dv.afecta_serie as controla_serie, 
			dv.afecta_stock as controla_stock, dv.idalmacen, coalesce(dv.oferta,'N') as oferta, 
			array_to_string(array_agg(s.serie), '|'::text) AS serie, dv.codgrupo_igv, dv.codtipo_igv,
			pu.precio_compra, pu.precio_venta
			FROM venta.detalle_venta dv
			JOIN compra.producto p ON p.idproducto = dv.idproducto
			LEFT JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal = ?
			LEFT JOIN venta.detalle_venta_serie s on s.iddetalle_venta=dv.iddetalle_venta and s.idventa=dv.idventa
			WHERE dv.idventa = ?";
		
		if( ! $all)
			$sql .= " AND dv.estado = 'A'";
		
		$sql .= " GROUP BY dv.iddetalle_venta, dv.idproducto, dv.descripcion, dv.idunidad, dv.cantidad, dv.precio, 
			dv.despachado, p.tipo, dv.afecta_serie, dv.afecta_stock, dv.idalmacen, dv.oferta, dv.codgrupo_igv, 
			dv.codtipo_igv, pu.precio_compra, pu.precio_venta
			ORDER BY dv.iddetalle_venta";
		
		$query = $this->db->query($sql, array($idsucursal, $idventa));
		return $query->result_array();
	}
}
?>