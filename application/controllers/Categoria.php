<?php

include_once "Controller.php";

class Categoria extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Categor&iacute;as");
		$this->set_subtitle("Lista de categor&iacute;as");
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
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("general.categoria");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->categoria);
		
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
		$this->set_title("Registrar categoria");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("general.categoria");
		$data = $this->categoria->find($id);
		
		$this->set_title("Modificar categoria");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("general.categoria");
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idcategoria"])) {
			if(!$this->categoria->exists(array("descripcion"=>$fields["descripcion"]))) {
				$this->categoria->insert($fields);
			}
			else {
				$this->exception("La categoria ".$fields["descripcion"]." ya existe");
			}
		}
		else {
			$this->categoria->update($fields);
		}
		
		$this->response($this->categoria->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("general.categoria");
		
		// cambiamos de estado
		$fields['idcategoria'] = $id;
		$fields['estado'] = "I";
		$this->categoria->update($fields);
		
		$this->response($fields);
	}
	
	public function autocomplete() {
		// $txt = '%'.$this->input->post("startsWith").'%';
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idcategoria, descripcion, prefijo
			FROM general.categoria
			WHERE estado='A' and (descripcion ILIKE ?)
			ORDER BY descripcion
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
}
?>