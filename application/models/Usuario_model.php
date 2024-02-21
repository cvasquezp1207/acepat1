<?php

include_once "Model.php";

class Usuario_model extends Model {
	
    public function init() {
        $this->set_schema("seguridad");
    }

	public function autentificar_usuario($usuario, $password){

        $this->db->where('usuario',$usuario)->where("estado", "A")->where("baja", "N");
        $query = $this->db->get('seguridad.usuario');
		
        if($query->num_rows() >= 1) {
            $row = $query->row_array();
            $pass_encriptado = $row["clave"];

            if($this->bcrypt->check_password($password, $pass_encriptado)) {
                return $row;
            }
        } else {
            // Implementar para cuando existan mas de 2 usuarios
		}
		
		return false;
    }
	
	public function get_vendedor($idsucursal, $idtipoempleado, $estado = 'A', $baja=array('N')) {
		$sql = "SELECT idusuario, nombres||' '||appat||' '||apmat as nombre 
			FROM seguridad.usuario 
			WHERE estado = ? AND idusuario IN (
				SELECT idusuario FROM seguridad.acceso_empresa
				WHERE idsucursal = ? AND idtipoempleado = ? AND estado = 'A'
			) AND baja IN ('".implode("','", $baja)."') order by nombre";
		$query = $this->db->query($sql, array($estado, $idsucursal, $idtipoempleado));
		return $query->result_array();
	}
	
	public function get_empleado($idusuario) {
		$sql = "SELECT idusuario, nombres||' '||appat||' '||apmat as nombre 
			FROM seguridad.usuario 
			WHERE idusuario = ?";
		$query = $this->db->query($sql, array($idusuario));
		return $query->result_array();
	}
}
?>