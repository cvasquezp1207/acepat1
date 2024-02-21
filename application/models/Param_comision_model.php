<?php
include_once "Model.php";

class Param_comision_model extends Model {

	public function init() {
		$this->set_schema("comision");
	}
	
	public function get_rangos($idsucursal, $fecha_inicio, $fecha_fin) {
		$sql = "select pc.*, m.descripcion as marca
			from comision.param_comision pc
			join general.marca m on m.idmarca = pc.idmarca
			where pc.idsucursal = ? and pc.fecha_inicio = ? and pc.fecha_fin = ?
			order by pc.dias_min asc, marca";
		$query = $this->db->query($sql, array($idsucursal, $fecha_inicio, $fecha_fin));
		return $query->result_array();
	}
}
?>