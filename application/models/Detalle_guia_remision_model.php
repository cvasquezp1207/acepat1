<?php

include_once "Model.php";

class Detalle_guia_remision_model extends Model {

	public function init() {
		$this->set_schema("almacen");
	}
	
	public function get_items($idguia, $all = FALSE) {
		$sql = "select dv.iddetalle_guia_remision, dv.descripcion as descripcion_detallada, 
			u.descripcion as unidad, dv.cantidad, dv.afecta_stock as controla_stock, 
			dv.afecta_serie as controla_serie, dv.idalmacen, dv.idproducto, dv.idunidad,
			dv.peso, 
			array_to_string(array_agg(dvs.serie), '|'::text) as serie
			from almacen.detalle_guia_remision dv
			join compra.unidad u on u.idunidad = dv.idunidad
			left join almacen.detalle_guia_remision_serie dvs on dvs.iddetalle_guia_remision=dv.iddetalle_guia_remision 
				and dvs.idguia_remision=dv.idguia_remision and dvs.idproducto=dv.idproducto and dvs.estado='A'
			where dv.idguia_remision = ?";
		
		if( ! $all)
			$sql .= " and dv.estado = 'A'";
		
		$sql .= " group by dv.iddetalle_guia_remision, dv.descripcion, u.descripcion, dv.cantidad, dv.afecta_stock, 
				dv.afecta_serie, dv.idalmacen, dv.idproducto, dv.idguia_remision, dv.idunidad, dv.peso
			order by iddetalle_guia_remision";
		
		$query = $this->db->query($sql, array($idguia));
		return $query->result_array();
	}
}
?>