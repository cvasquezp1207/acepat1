<?php

include_once "Controller.php";

class Modelo extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Modelo");
		$this->set_subtitle("Lista de modelos");
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
	public function form($data = null, $prefix = "") {
		if(!is_array($data)) {
			$data = array();
		}
		
		// $this->load->library('combobox');
		// $this->combobox->setAttr("id","idmarca");
		// $this->combobox->setAttr("name","idmarca");
		// $this->combobox->setAttr("class","form-control");
		// $this->combobox->setAttr("required","");
		// $this->db->select('idmarca,descripcion');
		// $query = $this->db->where("estado","A")->order_by("descripcion")->get("general.marca");
		// $this->combobox->addItem($query->result_array());
		// if(isset($data["idmarca"])) {
			// $this->combobox->setSelectedOption($data["idmarca"]);
		// }
		// $data['marca'] = $this->combobox->getObject();
		
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model($this->controller);
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->modelo);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion','prefijo'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Descripci&oacute;n'
			,'Prefijo'
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Modelo");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->modelo->find($id);
		
		$this->set_title("Modificar Modelo");
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
		$fields['estado'] = "A";
		if(empty($fields["idmodelo"])) {
			if($this->modelo->exists(array("descripcion"=>$fields["descripcion"])) == false) {
				$this->modelo->insert($fields);
			}
			else {
				$this->exception("La marca ".$fields["descripcion"]." ya existe");
			}
		}
		else {
			$this->modelo->update($fields);
		}
		
		$this->response($this->modelo->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idmodelo'] = $id;
		$fields['estado'] = "I";
		$this->modelo->update($fields);
		
		$this->response($fields);
	}
	
	public function options() {
		$idmarca = $this->input->post("idmarca");
		
		$query = $this->db->where("estado", "A")
			->where("idmarca", $idmarca)
			->order_by("descripcion", "asc")
			->get("general.modelo");
		
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
				$html .= '<option value="'.$row->idmodelo.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idmodelo, descripcion, prefijo
			FROM general.modelo
			WHERE estado='A' and (descripcion ILIKE ?)
			ORDER BY descripcion
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
}
?>