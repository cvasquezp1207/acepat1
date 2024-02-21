<?php

include_once "Model.php";

class Ubigeo_model extends Model {

	public function init() {
		$this->set_schema("general");
	}
	
	public function get_departamento($idubigeo = "") {
		$match = "__0000";
		if(!empty($idubigeo)) {
			$match = substr_replace($idubigeo, "0000", 2);
		}
		
		$sql = "select * from general.ubigeo where idubigeo like ? order by idubigeo";
		$query = $this->db->query($sql, array($match));
		return $query->result_array();
	}
	
	public function get_provincia($idubigeo) {
		$match = substr_replace($idubigeo, "__00", 2);
		$not = substr_replace($idubigeo, "0000", 2);
		
		$sql = "select * from general.ubigeo where idubigeo like ? and idubigeo != ? order by idubigeo";
		$query = $this->db->query($sql, array($match, $not));
		return $query->result_array();
	}
	
	public function get_distrito($idubigeo) {
		$match = substr_replace($idubigeo, "__", -2);
		$not = substr_replace($idubigeo, "00", -2);
		
		$sql = "select * from general.ubigeo where idubigeo like ? and idubigeo != ? order by idubigeo";
		$query = $this->db->query($sql, array($match, $not));
		return $query->result_array();
	}
	
	public function get_data($idubigeo) {
		$res["idubigeo"] = $idubigeo;
		
		// departamento
		$id = substr_replace($idubigeo, "0000", 2);
		$query = $this->db->query("select * from general.ubigeo where idubigeo like ?", array($id));
		$res["departamento"] = $query->row()->descripcion;
		
		// provincia
		$id = substr_replace($idubigeo, "00", 4);
		$query = $this->db->query("select * from general.ubigeo where idubigeo like ?", array($id));
		$res["provincia"] = $query->row()->descripcion;
		
		// distrito
		$query = $this->db->query("select * from general.ubigeo where idubigeo like ?", array($idubigeo));
		$res["distrito"] = $query->row()->descripcion;
		
		return $res;
	}
}
?>