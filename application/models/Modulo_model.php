<?php

include_once "Model.php";

class Modulo_model extends Model {

    public function init() {
		$this->set_schema("seguridad");
	}

	public function get_modulo_acceso($sistema, $perfil, $padre = 0){

        $this->db->select('m.idmodulo as idmodulo, m.idpadre as padre, m.descripcion as menu, m.url as url, m.icono as icono');
        $this->db->where('m.idsistema',$sistema);
        $this->db->where('a.idperfil',$perfil);
        $this->db->where('a.acceder', 1);
        $this->db->where('m.idpadre', $padre);
        $this->db->join('seguridad.modulo m', 'a.idmodulo = m.idmodulo');
        $this->db->join('seguridad.sistema s', 'm.idsistema = s.idsistema');
        $this->db->order_by('padre', 'DESC');
        $query = $this->db->get('seguridad.acceso a');

        if($query->num_rows() > 0) :
        
            return $query->result();

        else :
            return FALSE;
        endif;

    }
	
}
?>