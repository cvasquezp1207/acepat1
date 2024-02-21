<?php

include_once "Controller.php";

class Zona extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Zona");
		$this->set_subtitle("Lista de Zona");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal = false) {
		if(!is_array($data)) {
			$data = array();
		}

		$data["controller"] = $this->controller;
		$data["ubigeo"] = $this->get_form_ubigeo();
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		//$this->load_model('view_zona');
		$this->load_model($this->controller);
		$this->load->library('datatables');

		$this->datatables->setModel($this->zona);
		// $this->datatables->setModel($this->perfil);
		$this->datatables->setIndexColumn("idzona");

		$this->datatables->setColumns(array('idzona','zona','estado'));

		$columnasName = array(
			array('ID','5%')
			,array('Descripci&oacute;n','80%')
			,array('Estado','10%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";

		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// agregamos los scripts para el dataTables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);

		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Zona");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);

		$data = $this->zona->find($id);
		//print_r($data);
		$data['ubigeo_descr'] = $this->get_ubigeo($data['idubigeo']);
		$this->set_title("Modificar Zona");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		$fields['estado']= 'A';
		if(empty($fields["idzona"])) {
			$this->zona->insert($fields);
		}else {
			$this->zona->update($fields);
		}
		
		$this->response($this->zona->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		// cambiamos de estado
		$fields['idzona'] = $id;
		$fields['estado'] = "I";
		$this->perfil->update($fields);
		
		$this->response($fields);
	}
	
	public function get_localidad(){
		$post = $this->input->post();
		$q = $this->db->query("SELECT*FROM general.view_localidad WHERE estado='A' AND idubigeo='{$post['idubigeo']}' ORDER BY zona;");
		
		$this->response($q->result_array());
	}

	public function get_ubigeo($idubigeo = null){
		if ($idubigeo) {
			// echo $idubigeo;exit;
			$query = $this->db->query("SELECT * FROM general.ubigeo_view WHERE ubi_id='$idubigeo' ");
			$texto = $query->result_array();
			if(empty($texto)){
				$texto = array(array("ubi_descripcion"=>''
								,"ubi_dpto"=>''
								,"ubi_prov"=>'')
						);
			}

			$distrito = $texto[0]['ubi_descripcion'];

			$cod_prov = $texto[0]['ubi_dpto'].$texto[0]['ubi_prov']. '00';

			$cod_dpto = $texto[0]['ubi_dpto'].'0000';
			$query = $this->db->query("SELECT * FROM general.ubigeo_view WHERE ubi_id='$cod_prov' ");
			$texto = $query->result_array();
			if(empty($texto)){
				$texto = array(array("ubi_descripcion"=>''
								,"ubi_dpto"=>''
								,"ubi_prov"=>''
							)
						);
			}
			$provincia = $texto[0]['ubi_descripcion'];


			$query = $this->db->query("SELECT * FROM general.ubigeo_view WHERE ubi_id='$cod_dpto' ");
			$texto = $query->result_array();
			if(empty($texto)){
				$texto = array(array("ubi_descripcion"=>''
								,"ubi_dpto"=>''
								,"ubi_prov"=>''
							)
						);
			}

			$departamento = $texto[0]['ubi_descripcion'];			

			$ubigeo_descr = $departamento.' - '.$provincia.' - '.$distrito;
		}else{
			$ubigeo_descr = '';
		}
		return $ubigeo_descr;
	}

	public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("zona", "asc")
			->get("general.zona");
		
		$html = '';
		if($query->num_rows() > 0) {
			$fo = "true";
			if($this->input->post("first_option") == "false") {
				$fo = "false";
			}
			if($fo == "true") {
				$html .= '<option value=""></option>';
			}
			foreach($query->result() as $row) {
				$html .= '<option value="'.$row->idzona.'">'.$row->zona.'</option>';
			}
		}
		
		$this->response($html);
	}
}
?>