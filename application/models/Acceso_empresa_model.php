<?php
include_once "Model.php";
// class Acceso_empresa_model extends CI_Model {
class Acceso_empresa_model extends Model {

    private $query;
	private $data = array();

    public function __construct()
    {
        // Call the CI_Model constructor
        parent::__construct();
    }
	
	public function init() {
        $this->set_schema("seguridad");
    }


    public function get_empresa_usuario($idusuario){
		$this->db->distinct();
        $this->db->select('e.idempresa, e.descripcion as empresa, ae.idsucursal, s.descripcion as sucursal, s.direccion, s.telefono');
        $this->db->from('seguridad.acceso_empresa AS ae');
        $this->db->join('seguridad.sucursal AS s', 'ae.idsucursal = s.idsucursal');
        $this->db->join('seguridad.empresa AS e', 's.idempresa = e.idempresa');
        $this->db->where('ae.idusuario',$idusuario);
        $this->db->where('s.estado','A');
        $this->db->where('e.estado','A');
		$this->db->order_by('idempresa');
        $this->query = $this->db->get();

        if($this->query->num_rows() > 0) :
            $datos = $this->query->result();
			$arr = array();
            foreach ($datos as $value) :
				$key = "emp".$value->idempresa;
				
				if(!array_key_exists($key, $arr)) {
					$arr[$key] = array();
				}
				$arr[$key]["idempresa"] = $value->idempresa;
				$arr[$key]["nombre"] = $value->empresa;
				
				if(!array_key_exists("sucursal", $arr[$key])) {
					$arr[$key]["sucursal"] = array();
				}
				$arr[$key]["sucursal"][] = array(
					"idsucursal" => $value->idsucursal
					,"nombre" => $value->sucursal
					,"direccion" => $value->direccion
					,"telefono" => $value->telefono
				);
                // $this->data[$value->idempresa]['nombre'] =  $value->empresa;
                // $this->data[$value->idempresa][$value->idsucursal]['nombre'] = $value->sucursal;
                // $this->data[$value->idempresa][$value->idsucursal]['direccion'] = $value->direccion;
                // $this->data[$value->idempresa][$value->idsucursal]['telefono'] = $value->telefono;

            endforeach;
            
            // return $this->data;
            return $arr;

        else :

            return false;
        endif;

	}

    public function get_sucursal_perfil_usuario($idusuario, $sucursal){

        $this->db->select('e.idempresa as idempresa, e.descripcion as empresa, ae.idsucursal, s.descripcion as sucursal, p.idperfil as idperfil , p.descripcion as perfil');
        $this->db->where('ae.idusuario',$idusuario);
        $this->db->where('ae.idsucursal',$sucursal);
        $this->db->where('s.estado','A');
        $this->db->where('e.estado','A');
        $this->db->join('seguridad.sucursal AS s', 'ae.idsucursal = s.idsucursal');
        $this->db->join('seguridad.empresa AS e', 's.idempresa = e.idempresa');
        $this->db->join('seguridad.usuario AS u', 'ae.idusuario = u.idusuario');
        $this->db->join('seguridad.perfil AS p', 'ae.idperfil = p.idperfil');
        $this->query = $this->db->get('seguridad.acceso_empresa AS ae');

        if($this->query->num_rows() > 0) :
            
            return $this->query->row();

        else :

            return false;
        endif;

    }
	
}
?>