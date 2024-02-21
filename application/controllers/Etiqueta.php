<?php

include_once "Controller.php";

class Etiqueta extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Etiquetas de Impresion");
		$this->set_subtitle("Lista de Etiquetas");
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
		$this->load_model("general.etiqueta");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->etiqueta);
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('label_impresion','etiqueta'));
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Label Impresion'
			,'Etiqueta'
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
		$this->set_title("Registrar nueva Etiqueta para Impresion");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("general.etiqueta");
		$data = $this->etiqueta->find($id);
		
		$this->set_title("Modificar etiqueta de Impresion");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("general.etiqueta");
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idetiqueta"])) {
			if($this->etiqueta->exists(array("label_impresion"=>$fields["label_impresion"])) == false) {
				$this->etiqueta->insert($fields);
			}
			else {
				$this->exception("La etiqueta ".$fields["label_impresion"]." ya existe");
			}
		}
		else {
			$this->etiqueta->update($fields);
		}
		
		$this->response($this->etiqueta->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("general.etiqueta");
		
		// cambiamos de estado
		$fields['idetiqueta'] = $id;
		$fields['estado'] = "I";
		$this->etiqueta->update($fields);
		
		$this->response($fields);
	}
	
	// public function autocomplete() {
	// 	// $txt = '%'.$this->input->post("startsWith").'%';
	// 	$txt = trim($this->input->post("startsWith"));
	// 	$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
	// 	$sql = "SELECT idlinea, descripcion, prefijo
	// 		FROM general.linea
	// 		WHERE estado='A' and (descripcion ILIKE ?)
	// 		ORDER BY descripcion
	// 		LIMIT ?";
	// 	$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
	// 	$this->response($query->result_array());
	// }
}
?>