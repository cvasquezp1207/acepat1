<?php
include_once "Model.php";
class Acceso_model extends Model {

    private $query;
	private $data = array();

    // public function __construct() {
        // parent::__construct();
    // }
	
	public function init() {
        $this->set_schema("seguridad");
    }


    public function acceso_sistema($idusuario, $idperfil){

        $this->db->distinct();
        $this->db->select('s.idsistema AS idsistema, s.descripcion AS sistema, s.abreviatura AS abre, s.url AS url, s.image AS img');
        $this->db->where('a.idusuario',$idusuario);
        $this->db->where('a.idperfil',$idperfil);
        $this->db->where('s.estado','A');
        $this->db->join('seguridad.modulo AS m', 'a.idmodulo = m.idmodulo');
        $this->db->join('seguridad.sistema AS s', 'm.idsistema = s.idsistema');
        $this->query = $this->db->get('seguridad.acceso AS a');

        if($this->query->num_rows() > 0) :

            return $this->query->result();
        else :

            return false;
        endif;
	}

	public function get_modulo($data) {
		$this->db->select('m.*');
		$this->db->from('seguridad.acceso a');
		$this->db->join('seguridad.modulo m', 'm.idmodulo = a.idmodulo');
		$this->db->where('a.idperfil', $data['idperfil']);
		// $this->db->where('a.idusuario', $data['idusuario']);
		$this->db->where('a.idsucursal', $data['idsucursal']);
		$this->db->where('m.idsistema', $data['idsistema']);
		$this->db->where('m.idpadre', $data['idpadre']);
		$this->db->where('m.estado', 'A');
		$this->db->where('a.acceder', 1);
		$this->db->order_by('orden', 'asc');
		$query = $this->db->get();
		return $query->result_array();
	}
}
?>