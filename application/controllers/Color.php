<?php

include_once "Controller.php";

class Color extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Color");
		$this->set_subtitle("Lista de colores");
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
		$this->load_model("general.color");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->color);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Descripci&oacute;n'
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
		$this->set_title("Registrar Color");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("general.color");
		$data = $this->color->find($id);
		
		$this->set_title("Modificar Color");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("general.color");
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idcolor"])) {
			if($this->color->exists(array("descripcion"=>$fields["descripcion"])) == false) {
				$this->color->insert($fields);
			}
			else {
				$this->exception("El color ".$fields["descripcion"]." ya existe");
			}
		}
		else {
			$this->color->update($fields);
		}
		
		$this->response($this->color->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("general.color");
		
		// cambiamos de estado
		$fields['idcolor'] = $id;
		$fields['estado'] = "I";
		$this->color->update($fields);
		
		$this->response($fields);
	}
	
	public function options() {
		$query = $this->db->where("estado", "A")
			->order_by("descripcion", "asc")
			->get("general.color");
		
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
				$html .= '<option value="'.$row->idcolor.'">'.$row->descripcion.'</option>';
			}
		}
		
		$this->response($html);
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idcolor, descripcion
			FROM general.color
			WHERE estado='A' and (descripcion ILIKE ?)
			ORDER BY descripcion
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
}
?>